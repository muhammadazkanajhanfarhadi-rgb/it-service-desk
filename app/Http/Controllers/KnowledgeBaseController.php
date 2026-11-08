<?php

namespace App\Http\Controllers;

use App\Models\ArticleFeedback;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\KnowledgeArticle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KnowledgeBaseController extends Controller
{
    /* ── Public catalog (all authenticated users) ──────── */

    public function index(Request $request): View
    {
        $query = KnowledgeArticle::published()
            ->with(['category', 'author'])
            ->orderByDesc('published_at');

        if ($request->filled('search')) {
            $query->search($request->search);
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $articles = $query->paginate(12)->withQueryString();

        // Popular articles (most viewed, published)
        $popular = KnowledgeArticle::published()
            ->orderByDesc('views_count')
            ->take(5)
            ->get(['id', 'title', 'slug', 'views_count']);

        $categories = Category::active()->topLevel()->get();

        return view('knowledge-base.index', compact('articles', 'popular', 'categories'));
    }

    /* ── Article detail ─────────────────────────────────── */

    public function show(Request $request, KnowledgeArticle $article): View
    {
        // Draft/archived articles are NOT accessible to employees
        if (!$article->isPublished() && !$request->user()->canManage()) {
            abort(404);
        }

        // Increment views only for published articles
        $article->incrementViews();

        // Check if user already gave feedback
        $userFeedback = ArticleFeedback::where('article_id', $article->id)
            ->where('user_id', $request->user()->id)
            ->first();

        // Related articles (same category, published, exclude self)
        $related = KnowledgeArticle::published()
            ->where('id', '!=', $article->id)
            ->where('category_id', $article->category_id)
            ->take(4)
            ->get(['id', 'title', 'slug', 'excerpt']);

        $article->load(['category', 'author']);

        return view('knowledge-base.show', compact('article', 'userFeedback', 'related'));
    }

    /* ── Feedback ────────────────────────────────────────── */

    public function feedback(Request $request, KnowledgeArticle $article): RedirectResponse
    {
        // Only published articles can receive feedback
        if (!$article->isPublished()) {
            return back()->with('error', 'Artikel ini tidak menerima feedback.');
        }

        $validated = $request->validate([
            'is_helpful' => ['required', 'boolean'],
        ]);

        // Prevent duplicate feedback (unique constraint on article_id + user_id)
        $existing = ArticleFeedback::where('article_id', $article->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($existing) {
            // Update existing feedback
            $existing->update(['is_helpful' => $validated['is_helpful']]);
        } else {
            ArticleFeedback::create([
                'article_id' => $article->id,
                'user_id'    => $request->user()->id,  // from auth, NOT input
                'is_helpful' => $validated['is_helpful'],
            ]);
        }

        AuditLog::record('article.feedback', 'knowledge_base',
            "Feedback pada artikel '{$article->title}' oleh " . $request->user()->name,
            $request->user()->id,
            KnowledgeArticle::class, $article->id,
        );

        return back()->with('success', 'Terima kasih atas feedback Anda.');
    }
}
