@php
    $title = $title ?? '';
    $areaDescriptor = $areaDescriptor ?? '';
    $periodLabel = $periodLabel ?? null;
@endphp

<div class="d-flex flex-column flex-sm-row flex-wrap justify-content-between align-items-sm-center gap-2 mb-3">
    <div>
        <h6 class="dk-card__title mb-0">{{ $title }}</h6>
        @if (!empty($areaDescriptor))
            <p class="text-xs text-muted mb-0">{{ $areaDescriptor }}</p>
        @endif
    </div>
    @if (!empty($periodLabel))
        <span class="badge bg-light text-dark text-uppercase">{{ $periodLabel }}</span>
    @endif
</div>
