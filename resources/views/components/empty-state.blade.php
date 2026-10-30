@props([
    'icon' => 'bi-folder-x',
    'title' => 'No records found',
    'message' => 'There are currently no items to display matching your criteria.',
    'action' => null
])

<div class="text-center py-5">
    <div class="mb-3 text-muted opacity-50">
        <i class="bi {{ $icon }}" style="font-size: 3.5rem;"></i>
    </div>
    <h5 class="fw-semibold text-secondary mb-1">{{ $title }}</h5>
    <p class="text-muted small mb-3 max-w-sm mx-auto" style="max-width: 400px;">{{ $message }}</p>
    @if ($action)
        <div>{{ $action }}</div>
    @endif
</div>

