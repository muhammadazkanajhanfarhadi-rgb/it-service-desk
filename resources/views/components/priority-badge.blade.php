@props(['priority'])

@php
$slug  = $priority?->slug ?? 'medium';
$label = $priority?->name ?? 'Medium';
@endphp

<span class="badge badge-{{ $slug }}">
    <span class="material-symbols-outlined" style="font-size: 10px;">{{ $priority?->icon ?? 'remove' }}</span>
    {{ $label }}
</span>
