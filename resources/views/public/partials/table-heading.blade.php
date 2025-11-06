@php
    $title = $title ?? '';
    $areaDescriptor = $areaDescriptor ?? '';
    $periodLabel = $periodLabel ?? null;
    $categoryOptions = $categoryOptions ?? [];
    $activeCategory = $activeCategory ?? null;
    $dropdownId = null;

    if (!empty($categoryOptions)) {
        $dropdownId = 'aggregateTabsSelect';
        if (!empty($activeCategory)) {
            $dropdownId .= '-' . $activeCategory;
        }
    }
@endphp

<div class="dk-table-heading mb-3">
    <div class="dk-table-heading__info">
        <h6 class="dk-card__title mb-1">{{ $title }}</h6>
        @if (!empty($areaDescriptor))
            <p class="text-xs text-muted mb-0">{{ $areaDescriptor }}</p>
        @endif
    </div>
    <div class="dk-table-heading__actions">
        @if (!empty($categoryOptions))
            <div class="dk-table-heading__dropdown">
                <label for="{{ $dropdownId }}" class="form-label text-uppercase text-xs text-muted">Kategori</label>
                <select id="{{ $dropdownId }}" class="form-select dk-tabs-dropdown__select js-aggregate-tabs-select"
                    aria-label="Pilih kategori data">
                    @foreach ($categoryOptions as $optionKey => $optionLabel)
                        @php
                            $optionValue = 'tab-' . $optionKey;
                        @endphp
                        <option value="{{ $optionValue }}" {{ $optionValue === $activeCategory ? 'selected' : '' }}>
                            {{ $optionLabel }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
        @if (!empty($periodLabel))
            <div class="dk-table-heading__badge">
                <span class="badge dk-table-heading__chip">{{ $periodLabel }}</span>
            </div>
        @endif
        <div class="dk-table-heading__fullscreen">
            @php
                $currentTabPane = $activeCategory ?? 'tab-gender';
                $fullscreenUrl = route('public.data.fullscreen', array_merge(request()->query(), ['category' => str_replace('tab-', '', $currentTabPane)]));
            @endphp
            <a href="{{ $fullscreenUrl }}" target="_blank" class="btn btn-sm btn-outline-primary dk-table-heading__fullscreen-btn js-fullscreen-btn" data-base-url="{{ route('public.data.fullscreen', request()->query()) }}" title="Buka di tab baru (Fullscreen)">
                <i class="fas fa-expand"></i> Fullscreen
            </a>
        </div>
    </div>
</div>
