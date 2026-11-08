@props(['label', 'value', 'icon' => null, 'color' => 'accent-electric', 'trend' => null])

<div class="stat-card">
    <div class="flex items-start justify-between">
        <div>
            <div class="stat-value">{{ $value }}</div>
            <div class="stat-label">{{ $label }}</div>
            @if($trend)
            <div class="mt-2" style="font-size: var(--font-size-caption); color: var(--color-text-tertiary);">
                {{ $trend }}
            </div>
            @endif
        </div>
        @if($icon)
        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background-color: rgba(79,140,255,0.12);">
            <span class="material-symbols-outlined"
                  style="font-size: 20px; color: var(--color-{{ $color }});">
                {{ $icon }}
            </span>
        </div>
        @endif
    </div>
</div>
