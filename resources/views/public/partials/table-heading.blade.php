@php
    $title = $title ?? '';
    $areaDescriptor = $areaDescriptor ?? '';
    $periodLabel = $periodLabel ?? null;
    $categoryOptions = $categoryOptions ?? [];
    $activeCategory = $activeCategory ?? null;
    $fullscreenRoute = $fullscreenRoute ?? 'public.data.fullscreen';
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
    <div class="dk-table-heading__actions flex flex-col md:flex-row md:items-center md:justify-end gap-3 w-full md:w-auto">
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
        <div class="flex flex-wrap gap-3 items-center justify-end w-full md:w-auto">
            @if (!empty($periodLabel))
                <div class="dk-table-heading__badge">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dk-table-heading__chip">{{ $periodLabel }}</span>
                </div>
            @endif
            @php
                $currentTabPane = $activeCategory ?? 'tab-gender';
                $fullscreenUrl = route($fullscreenRoute, array_merge(request()->query(), ['category' => str_replace('tab-', '', $currentTabPane)]));
            @endphp
            <a href="{{ $fullscreenUrl }}" target="_blank" class="inline-flex items-center px-3 py-1.5 border border-green-300 rounded-md shadow-sm text-sm font-medium text-green-700 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dk-table-heading__fullscreen-btn js-fullscreen-btn" data-base-url="{{ route($fullscreenRoute, request()->query()) }}" title="Buka di tab baru (Fullscreen)">
                <i class="fas fa-expand mr-1"></i> Fullscreen
            </a>
            @if (isset($showDownloadButtons) && $showDownloadButtons)
                @php
                    $downloadCategory = $activeCategory ?? request()->query('category', 'gender');
                    $downloadQuery = array_merge(request()->query(), ['category' => $downloadCategory]);
                    $pdfUrl = route('public.data.download.pdf', $downloadQuery);
                    $excelUrl = route('public.data.download.excel', $downloadQuery);
                    $defaultYear = request()->query('year', $period['year'] ?? now()->year);
                    $defaultSemester = request()->query('semester', $period['semester'] ?? 1);
                    $downloadLabelBase = 'data-' . $downloadCategory . '-' . $defaultYear . '-s' . $defaultSemester;
                @endphp
                <div class="dk-table-heading__downloads flex flex-wrap gap-2 items-center justify-end text-right">
                    <span class="text-sm font-semibold text-gray-600 whitespace-nowrap leading-tight">Download:</span>
                    <span 
                        class="js-download-btn inline-flex items-center px-4 py-1.5 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 cursor-pointer select-none"
                        data-download-type="table"
                        data-download-format="pdf"
                        data-download-url="{{ $pdfUrl }}"
                        data-download-label="{{ $downloadLabelBase }}.pdf"
                        data-year-default="{{ $defaultYear }}"
                        data-semester-default="{{ $defaultSemester }}"
                        role="button"
                        tabindex="0"
                        aria-label="Download PDF">
                        <img src="{{ asset('img/pdf.png') }}" alt="PDF icon" class="w-5 h-5 object-contain">
                    </span>
                    <span 
                        class="js-download-btn inline-flex items-center px-4 py-1.5 border border-green-300 rounded-md shadow-sm text-sm font-medium text-green-700 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 cursor-pointer select-none"
                        data-download-type="table"
                        data-download-format="excel"
                        data-download-url="{{ $excelUrl }}"
                        data-download-label="{{ $downloadLabelBase }}.xlsx"
                        data-year-default="{{ $defaultYear }}"
                        data-semester-default="{{ $defaultSemester }}"
                        role="button"
                        tabindex="0"
                        aria-label="Download Excel">
                        <img src="{{ asset('img/sheet.png') }}" alt="Excel icon" class="w-5 h-5 object-contain">
                    </span>
                </div>
            @endif
        </div>
    </div>
</div>
