@props(['href' => '#', 'icon' => 'circle', 'active' => false])

<a href="{{ $href }}"
   class="flex items-center gap-3 px-3 py-2 rounded-xl transition-all duration-150 group"
   style="{{ $active
       ? 'background-color: var(--color-interactive-active-bg); color: var(--color-accent-electric);'
       : 'color: var(--color-text-secondary);' }}"
   onmouseover="{{ $active ? '' : 'this.style.backgroundColor=\'var(--color-surface-container-high)\'; this.style.color=\'var(--color-text-primary)\'' }}"
   onmouseout="{{ $active ? '' : 'this.style.backgroundColor=\'transparent\'; this.style.color=\'var(--color-text-secondary)\'  ' }}">

    <span class="material-symbols-outlined flex-shrink-0"
          style="font-size: 19px; {{ $active ? 'color: var(--color-accent-electric);' : '' }}">
        {{ $icon }}
    </span>

    <span style="font-size: 13px; font-weight: {{ $active ? '600' : '400' }};">
        {{ $slot }}
    </span>

    @if($active)
    <span class="ml-auto w-1 h-4 rounded-full"
          style="background-color: var(--color-accent-electric);"></span>
    @endif
</a>
