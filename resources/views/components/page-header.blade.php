@props(['title', 'subtitle' => null])

<div class="mb-6">
    <h1 style="font-size: var(--font-size-headline-lg); font-weight: 700; color: var(--color-text-primary);">
        {{ $title }}
    </h1>
    @if($subtitle)
    <p class="mt-1" style="font-size: var(--font-size-body-sm); color: var(--color-text-secondary);">
        {{ $subtitle }}
    </p>
    @endif
</div>
