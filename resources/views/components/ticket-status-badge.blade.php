@props(['status'])

@php
$labels = [
    'new'         => 'New',
    'assigned'    => 'Assigned',
    'in_progress' => 'In Progress',
    'pending'     => 'Pending',
    'resolved'    => 'Resolved',
    'closed'      => 'Closed',
    'reopened'    => 'Reopened',
];
$label = $labels[$status] ?? ucfirst($status);
@endphp

<span class="badge badge-{{ str_replace('_', '-', $status) }}">
    {{ $label }}
</span>
