@props(['status'])

@php
$labels = [
    'available'   => 'Available',
    'assigned'    => 'Assigned',
    'maintenance' => 'Maintenance',
    'retired'     => 'Retired',
];
$badgeClass = match($status) {
    'available'   => 'badge-resolved',
    'assigned'    => 'badge-assigned',
    'maintenance' => 'badge-high',
    'retired'     => 'badge-closed',
    default       => 'badge-new',
};
$icon = match($status) {
    'available'   => 'check_circle',
    'assigned'    => 'assignment_ind',
    'maintenance' => 'build',
    'retired'     => 'block',
    default       => 'info',
};
$label = $labels[$status] ?? ucfirst($status);
@endphp

<span class="badge {{ $badgeClass }}">
    <span class="material-symbols-outlined" style="font-size: 10px;">{{ $icon }}</span>
    {{ $label }}
</span>
