<x-app-layout>
    <x-slot name="title">{{ $article->title }} — {{ config('app.name') }}</x-slot>
    <x-slot name="breadcrumb">
        <a href="{{ route('dashboard') }}" style="color: var(--color-text-tertiary);">Dashboard</a>
        <span style="color: var(--color-text-tertiary);">/</span>
        <a href="{{ route('knowledge-base.index') }}" style="color: var(--color-text-tertiary);">Knowledge Base</a>
        <span style="color: var(--color-text-tertiary);">/</span>
        <span style="color: var(--color-text-primary);">{{ Str::limit($article->title, 40) }}</span>
    </x-slot>

    <div class="grid lg:grid-cols-3 gap-5">
        {{-- Main article --}}
        <div class="lg:col-span-2">
            <div class="card mb-4">
                {{-- Header --}}
                <div class="flex items-center gap-2 mb-4 flex-wrap">
                    @if($article->category)
                    <span class="badge badge-assigned">{{ $article->category->name }}</span>
                    @endif
                    <span style="font-size: 12px; color: var(--color-text-tertiary);">
                        Oleh {{ $article->author?->name ?? '—' }} · {{ $article->published_at?->format('d M Y') }}
                    </span>
                    <span style="font-size: 12px; color: var(--color-text-tertiary);">
                        · {{ $article->views_count }} views
                    </span>
                </div>

                <h1 style="font-size: 22px; font-weight: 700; color: var(--color-text-primary); margin-bottom: 0.5rem;">{{ $article->title }}</h1>

                @if($article->excerpt)
                <p style="font-size: 14px; color: var(--color-text-secondary); line-height: 1.6; margin-bottom: 1.5rem;">{{ $article->excerpt }}</p>
                @endif

                {{-- Content — rendered with {!! !!} but sanitized via sanitizeContent() --}}
                <div style="font-size: 14px; color: var(--color-on-surface); line-height: 1.8;">
                    {!! $article->content !!}
                </div>

                {{-- Feedback --}}
                <div class="mt-6 pt-5" style="border-top: 1px solid var(--color-border-subtle);">
                    <p class="mb-3" style="font-size: 14px; color: var(--color-text-primary); font-weight: 600;">
                        Apakah artikel ini membantu?
                    </p>
                    <div class="flex items-center gap-3">
                        <form method="POST" action="{{ route('knowledge-base.feedback', $article) }}" class="inline">
                            @csrf
                            <input type="hidden" name="is_helpful" value="1">
                            <button type="submit"
                                    class="flex items-center gap-2 px-4 py-2 rounded-xl transition-colors"
                                    style="{{ $userFeedback?->is_helpful
                                        ? 'background-color: rgba(34,197,94,0.15); border: 1px solid rgba(34,197,94,0.3); color: var(--color-status-success);'
                                        : 'background-color: var(--color-card-elevated); border: 1px solid var(--color-border-subtle); color: var(--color-text-secondary);' }}"
                                    onmouseover="this.style.borderColor='var(--color-status-success)'"
                                    onmouseout="this.style.borderColor='{{ $userFeedback?->is_helpful ? 'rgba(34,197,94,0.3)' : 'var(--color-border-subtle)' }}'">
                                <span class="material-symbols-outlined" style="font-size: 18px;">thumb_up</span>
                                Helpful ({{ $article->helpfulCount() }})
                            </button>
                        </form>
                        <form method="POST" action="{{ route('knowledge-base.feedback', $article) }}" class="inline">
                            @csrf
                            <input type="hidden" name="is_helpful" value="0">
                            <button type="submit"
                                    class="flex items-center gap-2 px-4 py-2 rounded-xl transition-colors"
                                    style="{{ $userFeedback && !$userFeedback->is_helpful
                                        ? 'background-color: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: var(--color-status-danger);'
                                        : 'background-color: var(--color-card-elevated); border: 1px solid var(--color-border-subtle); color: var(--color-text-secondary);' }}"
                                    onmouseover="this.style.borderColor='var(--color-status-danger)'"
                                    onmouseout="this.style.borderColor='{{ $userFeedback && !$userFeedback->is_helpful ? 'rgba(239,68,68,0.3)' : 'var(--color-border-subtle)' }}'">
                                <span class="material-symbols-outlined" style="font-size: 18px;">thumb_down</span>
                                Not Helpful ({{ $article->notHelpfulCount() }})
                            </button>
                        </form>
                        @if($article->helpfulnessRate() > 0)
                        <span style="font-size: 12px; color: var(--color-text-tertiary);">{{ $article->helpfulnessRate() }}% helpful</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar: related articles --}}
        <div class="flex flex-col gap-4">
            @if($related->isNotEmpty())
            <div class="card">
                <h2 class="mb-3" style="font-size: 14px; font-weight: 600; color: var(--color-text-primary);">
                    <span class="material-symbols-outlined" style="font-size: 18px; vertical-align: middle;">link</span>
                    Related Articles
                </h2>
                <div class="flex flex-col gap-2">
                    @foreach($related as $r)
                    <a href="{{ route('knowledge-base.show', $r) }}" style="text-decoration: none;">
                        <div class="flex items-start gap-2 p-2 rounded-xl transition-colors"
                             onmouseover="this.style.backgroundColor='var(--color-surface-container-high)'"
                             onmouseout="this.style.backgroundColor='transparent'">
                            <span class="material-symbols-outlined" style="font-size: 14px; color: var(--color-accent-electric); margin-top: 2px;">arrow_forward</span>
                            <span style="font-size: 13px; color: var(--color-text-secondary);">{{ $r->title }}</span>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Back to KB --}}
            <div class="card">
                <a href="{{ route('knowledge-base.index') }}" class="btn-secondary w-full justify-center">
                    <span class="material-symbols-outlined" style="font-size: 16px;">arrow_back</span>
                    Back to Knowledge Base
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
