<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\KnowledgeArticle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KnowledgeArticleController extends Controller
{
    /* ── Index with analytics ──────────────────────────────── */

    public function index(Request $request): View
    {
        $query = KnowledgeArticle::with(['category', 'author'])
            ->orderByDesc('created_at');

        if ($request->filled('search')) {
            $query->search($request->search);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $articles = $query->paginate(20)->withQueryString();

        // Analytics
        $stats = [
            'total'     => KnowledgeArticle::count(),
            'published'  => KnowledgeArticle::published()->count(),
            'draft'     => KnowledgeArticle::draft()->count(),
            'archived'  => KnowledgeArticle::archived()->count(),
            'totalViews' => KnowledgeArticle::sum('views_count'),
        ];

        // Most viewed
        $mostViewed = KnowledgeArticle::orderByDesc('views_count')
            ->take(5)->get(['id', 'title', 'slug', 'views_count', 'status']);

        // Most helpful (articles with most helpful feedback)
        $mostHelpful = KnowledgeArticle::withCount(['feedback as helpful_count' => fn($q) => $q->where('is_helpful', true)])
            ->orderByDesc('helpful_count')
            ->take(5)->get(['id', 'title', 'slug', 'status']);

        $categories = Category::active()->get();

        return view('admin.knowledge-articles.index', compact('articles', 'stats', 'mostViewed', 'mostHelpful', 'categories'));
    }

    /* ── Create ──────────────────────────────────────────────── */

    public function create(): View
    {
        $categories = Category::active()->get();
        return view('admin.knowledge-articles.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'excerpt'     => ['nullable', 'string', 'max:500'],
            'content'     => ['required', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'status'      => ['required', 'in:draft,published,archived'],
        ]);

        // XSS protection: sanitize content
        $validated['content']    = KnowledgeArticle::sanitizeContent($validated['content']);
        $validated['author_id']  = $request->user()->id;  // from auth, NOT input
        $validated['slug']       = \Illuminate\Support\Str::slug($validated['title']) . '-' . \Illuminate\Support\Str::random(6);

        if ($validated['status'] === KnowledgeArticle::PUBLISHED) {
            $validated['published_at'] = now();
        }

        $article = KnowledgeArticle::create($validated);

        AuditLog::record('article.created', 'knowledge_base',
            "Artikel '{$article->title}' dibuat oleh " . $request->user()->name . " (status: {$article->status})",
            $request->user()->id,
            KnowledgeArticle::class, $article->id,
        );

        $action = $validated['status'] === 'published' ? 'article.published' : 'article.created';
        if ($validated['status'] === 'published') {
            AuditLog::record('article.published', 'knowledge_base',
                "Artikel '{$article->title}' dipublikasi oleh " . $request->user()->name,
                $request->user()->id,
                KnowledgeArticle::class, $article->id,
            );
        }

        return redirect()->route('admin.knowledge-articles.index')
            ->with('success', "Artikel '{$article->title}' berhasil dibuat.");
    }

    /* ── Edit ───────────────────────────────────────────────── */

    public function edit(KnowledgeArticle $article): View
    {
        $categories = Category::active()->get();
        return view('admin.knowledge-articles.edit', compact('article', 'categories'));
    }

    public function update(Request $request, KnowledgeArticle $article): RedirectResponse
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'excerpt'     => ['nullable', 'string', 'max:500'],
            'content'     => ['required', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'status'      => ['required', 'in:draft,published,archived'],
        ]);

        $validated['content'] = KnowledgeArticle::sanitizeContent($validated['content']);
        $oldStatus = $article->status;

        // Track publication
        if ($validated['status'] === KnowledgeArticle::PUBLISHED && $oldStatus !== KnowledgeArticle::PUBLISHED) {
            $validated['published_at'] = now();
        } elseif ($validated['status'] !== KnowledgeArticle::PUBLISHED) {
            $validated['published_at'] = null;
        }

        $old = $article->toArray();
        $article->update($validated);

        AuditLog::record('article.updated', 'knowledge_base',
            "Artikel '{$article->title}' diperbarui oleh " . $request->user()->name,
            $request->user()->id,
            KnowledgeArticle::class, $article->id,
            $old, $validated,
        );

        // Log status changes
        if ($validated['status'] !== $oldStatus) {
            if ($validated['status'] === KnowledgeArticle::PUBLISHED) {
                AuditLog::record('article.published', 'knowledge_base',
                    "Artikel '{$article->title}' dipublikasi oleh " . $request->user()->name,
                    $request->user()->id,
                    KnowledgeArticle::class, $article->id,
                );
            } elseif ($validated['status'] === KnowledgeArticle::ARCHIVED) {
                AuditLog::record('article.archived', 'knowledge_base',
                    "Artikel '{$article->title}' diarsipkan oleh " . $request->user()->name,
                    $request->user()->id,
                    KnowledgeArticle::class, $article->id,
                );
            }
        }

        return redirect()->route('admin.knowledge-articles.index')
            ->with('success', "Artikel '{$article->title}' berhasil diperbarui.");
    }

    /* ── Delete ─────────────────────────────────────────────── */

    public function destroy(KnowledgeArticle $article): RedirectResponse
    {
        $title = $article->title;
        $article->delete();

        AuditLog::record('article.deleted', 'knowledge_base',
            "Artikel '{$title}' dihapus oleh " . auth()->user()->name,
            auth()->id(),
        );

        return back()->with('success', "Artikel '{$title}' telah dihapus.");
    }
}
