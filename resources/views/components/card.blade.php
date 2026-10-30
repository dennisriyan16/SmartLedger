@props(['title' => null, 'actions' => null, 'footer' => null])

<div {{ $attributes->merge(['class' => 'card mb-3']) }}>
    @if ($title || $actions)
        <div class="card-header py-2.5 px-3 d-flex flex-wrap justify-content-between align-items-center">
            @if ($title)
                <h6 class="m-0 fw-semibold text-dark" style="font-size: 0.84rem;">{{ $title }}</h6>
            @endif
            @if ($actions)
                <div class="card-header-actions ms-auto">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif
    <div class="card-body p-3">
        {{ $slot }}
    </div>
    @if ($footer)
        <div class="card-footer bg-light py-2 px-3 border-top">
            {{ $footer }}
        </div>
    @endif
</div>
