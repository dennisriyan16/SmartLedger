@props(['status'])

@php
    $statusLower = strtolower((string) $status);
    $badgeStyle = match ($statusLower) {
        'paid', 'completed', 'active', 'on track' => 'bg-success-subtle text-success border border-success-subtle',
        'pending', 'near limit' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
        'overdue', 'cancelled', 'exceeded' => 'bg-danger-subtle text-danger border border-danger-subtle',
        'product' => 'bg-primary-subtle text-primary border border-primary-subtle',
        'service' => 'bg-info-subtle text-info-emphasis border border-info-subtle',
        'monthly' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
        'yearly' => 'bg-dark-subtle text-dark border border-dark-subtle',
        default => 'bg-light text-secondary border',
    };
    $label = ucfirst($status);
@endphp

<span {{ $attributes->merge(['class' => 'badge ' . $badgeStyle . ' fw-medium px-2 py-0.5']) }} style="font-size: 0.72rem; letter-spacing: 0.2px;">
    {{ $slot->isEmpty() ? $label : $slot }}
</span>
