<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class KnowledgeArticle extends Model
{
    protected $fillable = [
        'title', 'slug', 'excerpt', 'content',
        'featured_image', 'category_id', 'author_id',
        'status', 'published_at', 'views_count',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'views_count'  => 'integer',
    ];

    const DRAFT     = 'draft';
    const PUBLISHED  = 'published';
    const ARCHIVED   = 'archived';

    /** Allowed HTML tags for content (XSS protection) */
    const ALLOWED_TAGS = '<p><br><h1><h2><h3><h4><h5><h6><ul><ol><li><a><strong><em><img><blockquote><code><pre><hr><table><thead><tbody><tr><td><th>';

    /** Safe URL protocols */
    const SAFE_PROTOCOLS = ['http', 'https', 'mailto', 'tel', ''];

    /** Attributes that carry URLs — need protocol validation */
    const URL_ATTRIBUTES = ['href', 'src'];

    /** Attributes that are always forbidden (event handlers, style injection) */
    const FORBIDDEN_ATTR_PREFIX = 'on';

    /** Tags whose content must be removed entirely (not unwrapped) */
    const REMOVE_WITH_CONTENT = ['script', 'style', 'iframe', 'object', 'embed', 'applet', 'frame', 'frameset', 'noscript', 'template', 'link', 'meta', 'base', 'title', 'head'];

    protected static function booted(): void
    {
        static::creating(function (KnowledgeArticle $article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title) . '-' . Str::random(6);
            }
        });
    }

    /* ── Relationships ─────────────────────────────────── */

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(ArticleFeedback::class);
    }

    /* ── Helpers ───────────────────────────────────────── */

    public function isDraft(): bool     { return $this->status === self::DRAFT; }
    public function isPublished(): bool { return $this->status === self::PUBLISHED; }
    public function isArchived(): bool  { return $this->status === self::ARCHIVED; }

    /** Increment view count safely — only for published articles */
    public function incrementViews(): void
    {
        if ($this->isPublished()) {
            $this->increment('views_count');
        }
    }

    /** Helpful feedback count */
    public function helpfulCount(): int
    {
        return $this->feedback()->where('is_helpful', true)->count();
    }

    /** Not helpful feedback count */
    public function notHelpfulCount(): int
    {
        return $this->feedback()->where('is_helpful', false)->count();
    }

    /** Helpfulness rate as percentage */
    public function helpfulnessRate(): float
    {
        $total = $this->feedback()->count();
        return $total > 0 ? round(($this->helpfulCount() / $total) * 100, 1) : 0;
    }

    /**
     * Sanitize content HTML using DOMDocument for reliable parsing.
     *
     * Defense layers:
     * 1. Parse HTML into DOM tree (handles malformed HTML, encoding tricks)
     * 2. Remove tags not in allowlist (unwrap — keep text content)
     * 3. Remove all on* event handler attributes
     * 4. Validate URL protocols on href/src (block javascript:, data:, etc.)
     * 5. Remove style attribute (CSS injection vector)
     *
     * @param string $html Raw HTML input from editor
     * @return string Sanitized HTML safe for rendering
     */
    public static function sanitizeContent(string $html): string
    {
        if (trim($html) === '') {
            return '';
        }

        // Parse allowed tags from constant format '<p><br>...' into ['p','br',...]
        $allowedTagList = array_filter(explode('>', str_replace('<', '', self::ALLOWED_TAGS)));

        // Use DOMDocument for proper HTML parsing
        // mb_convert_encoding ensures UTF-8 is handled correctly
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $previousErrorState = libxml_use_internal_errors(true);

        $dom->loadHTML(
            mb_convert_encoding('<div id="wrapper">' . $html . '</div>', 'HTML-ENTITIES', 'UTF-8'),
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previousErrorState);

        // Find the wrapper div and sanitize its children
        $wrapper = null;
        foreach ($dom->childNodes as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE && strtolower($child->nodeName) === 'div') {
                $wrapper = $child;
                break;
            }
        }

        if (!$wrapper) {
            // Fallback: if no wrapper found, sanitize all document children
            $wrapper = $dom;
        }

        // Sanitize the DOM tree
        self::sanitizeDomNode($wrapper, $allowedTagList);

        // Extract inner HTML of the wrapper
        $output = '';
        foreach ($wrapper->childNodes as $child) {
            $output .= $dom->saveHTML($child);
        }

        return trim($output);
    }

    /**
     * Recursively sanitize DOM nodes.
     */
    private static function sanitizeDomNode(\DOMNode $node, array $allowedTags): void
    {
        // Collect nodes to process (can't modify while iterating)
        $nodesToRemove = [];

        foreach ($node->childNodes as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE) {
                $tagName = strtolower($child->nodeName);

                if (!in_array($tagName, $allowedTags, true)) {
                    // Tag not allowed — collect for removal
                    $nodesToRemove[] = $child;
                    continue;
                }

                // Sanitize attributes on this element
                if ($child instanceof \DOMElement) {
                    self::sanitizeAttributes($child);
                }

                // Recurse into children
                self::sanitizeDomNode($child, $allowedTags);
            }
        }

        // Remove disallowed tags
        foreach ($nodesToRemove as $element) {
            $parent = $element->parentNode;
            if (!$parent) {
                continue;
            }

            $tagName = strtolower($element->nodeName);

            // Dangerous tags (script, style, iframe, etc.) — remove with all children
            if (in_array($tagName, self::REMOVE_WITH_CONTENT, true)) {
                $parent->removeChild($element);
                continue;
            }

            // Other disallowed tags — unwrap (move children to parent, keep text)
            while ($element->firstChild) {
                $parent->insertBefore($element->firstChild, $element);
            }
            $parent->removeChild($element);
        }
    }

    /**
     * Sanitize attributes on an element.
     */
    private static function sanitizeAttributes(\DOMElement $element): void
    {
        $attrsToRemove = [];

        foreach ($element->attributes as $attr) {
            $name  = strtolower($attr->name);
            $value = $attr->value;

            // 1. Remove event handlers (onclick, onerror, onload, on*)
            if (str_starts_with($name, self::FORBIDDEN_ATTR_PREFIX)) {
                $attrsToRemove[] = $attr->name;
                continue;
            }

            // 2. Remove style attribute (CSS injection vector)
            if ($name === 'style') {
                $attrsToRemove[] = $attr->name;
                continue;
            }

            // 3. Validate URL protocols on href and src
            if (in_array($name, self::URL_ATTRIBUTES, true)) {
                if (!self::isUrlSafe($value)) {
                    // Replace with safe placeholder
                    $element->setAttribute($attr->name, '#');
                    continue;
                }
            }
        }

        foreach ($attrsToRemove as $attrName) {
            $element->removeAttribute($attrName);
        }
    }

    /**
     * Check if a URL is safe — only allow whitelisted protocols.
     * Handles encoding tricks, whitespace variations, and case insensitivity.
     */
    private static function isUrlSafe(string $url): bool
    {
        $url = trim($url);

        // Empty URLs and anchors are safe
        if ($url === '' || $url === '#') {
            return true;
        }

        // Relative URLs (starting with /, #, or ./) are safe
        if (str_starts_with($url, '/') || str_starts_with($url, '#') || str_starts_with($url, './')) {
            return true;
        }

        // Decode HTML entities (catches &#106;avascript: etc.)
        $decoded = html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $decoded = trim($decoded);

        // Remove all whitespace (catches java\tscript: and java\nscript:)
        $noWhitespace = preg_replace('/\s+/', '', $decoded);
        $lowerNoWs = strtolower($noWhitespace);

        // Block known dangerous protocols (after whitespace removal)
        $dangerous = ['javascript:', 'data:', 'vbscript:', 'file:', 'about:'];
        foreach ($dangerous as $proto) {
            if (str_starts_with($lowerNoWs, $proto)) {
                return false;
            }
        }

        // Check for protocol — if present, must be in safe list
        if (preg_match('/^([a-zA-Z][a-zA-Z0-9+.\-]*):/i', $decoded, $matches)) {
            $protocol = strtolower(trim($matches[1]));
            if (!in_array($protocol, ['http', 'https', 'mailto', 'tel'], true)) {
                return false;
            }
        }

        return true;
    }

    /* ── Scopes ────────────────────────────────────────── */

    public function scopePublished($query)
    {
        return $query->where('status', self::PUBLISHED);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', self::DRAFT);
    }

    public function scopeArchived($query)
    {
        return $query->where('status', self::ARCHIVED);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(fn($q) => $q
            ->where('title', 'like', "%{$term}%")
            ->orWhere('content', 'like', "%{$term}%")
            ->orWhere('excerpt', 'like', "%{$term}%")
        );
    }
}
