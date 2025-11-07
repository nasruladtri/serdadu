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
                    <p class="text-xs text-gray-500 mb-0">{{ $areaDescriptor }}</p>
        @endif
    </div>
    <div class="dk-table-heading__actions">
        @if (!empty($categoryOptions))
            <div class="dk-table-heading__dropdown">
                <label for="{{ $dropdownId }}" class="block text-xs font-medium text-gray-500 uppercase mb-1">Kategori</label>
                <select id="{{ $dropdownId }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dk-tabs-dropdown__select js-aggregate-tabs-select"
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
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dk-table-heading__chip">{{ $periodLabel }}</span>
            </div>
        @endif
        <div class="dk-table-heading__fullscreen">
            @php
                $currentTabPane = $activeCategory ?? 'tab-gender';
                $fullscreenUrl = route('public.data.fullscreen', array_merge(request()->query(), ['category' => str_replace('tab-', '', $currentTabPane)]));
            @endphp
            <a href="{{ $fullscreenUrl }}" target="_blank" class="inline-flex items-center px-3 py-1.5 border border-green-300 rounded-md shadow-sm text-sm font-medium text-green-700 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dk-table-heading__fullscreen-btn js-fullscreen-btn" data-base-url="{{ route('public.data.fullscreen', request()->query()) }}" title="Buka di tab baru (Fullscreen)">
                <i class="fas fa-expand mr-1"></i> Fullscreen
            </a>
        </div>
    </div>
</div>
