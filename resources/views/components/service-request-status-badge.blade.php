@props(['status'])

@php
$labels = [
    'pending_approval' => 'Pending Approval',
    'approved'         => 'Approved',
    'rejected'         => 'Rejected',
    'in_progress'      => 'In Progress',
    'completed'        => 'Completed',
    'cancelled'        => 'Cancelled',
];
$badgeClass = match($status) {
    'pending_approval' => 'badge-pending',
    'approved'         => 'badge-assigned',
    'rejected'         => 'badge-critical',
    'in_progress'      => 'badge-in-progress',
    'completed'        => 'badge-resolved',
    'cancelled'        => 'badge-closed',
    default            => 'badge-new',
};
$label = $labels[$status] ?? ucfirst($status);
@endphp

<span class="badge {{ $badgeClass }}">{{ $label }}</span>
