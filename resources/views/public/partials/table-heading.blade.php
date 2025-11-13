@php
    $title = $title ?? '';
    $areaDescriptor = $areaDescriptor ?? '';
    $periodLabel = $periodLabel ?? null;
    $description = $description ?? null;
    $secondaryAreaDescriptor = $secondaryAreaDescriptor ?? null;
    $areaLabel = $areaLabel ?? 'Wilayah';
    $secondaryAreaLabel = $secondaryAreaLabel ?? null;
    $categoryOptions = $categoryOptions ?? [];
    $activeCategory = $activeCategory ?? null;
    $fullscreenRoute = $fullscreenRoute ?? 'public.data.fullscreen';
    $customDownloads = $customDownloads ?? null;
    $dropdownId = null;

    if (!empty($categoryOptions)) {
        $dropdownId = 'aggregateTabsSelect';
        if (!empty($activeCategory)) {
            $dropdownId .= '-' . $activeCategory;
        }
    }
@endphp

<div class="dk-table-heading mb-4">
    <div class="dk-table-heading__info">
        <div class="space-y-1">
            <h6 class="text-lg font-semibold text-gray-900 tracking-tight">{{ $title }}</h6>
            @if (!empty($description))
                <p class="text-sm text-gray-500 leading-relaxed">{{ $description }}</p>
            @endif
        </div>
        <div class="mt-3 space-y-1 text-sm text-gray-600">
            @if (!empty($areaDescriptor))
                <div class="flex flex-col sm:flex-row sm:items-start sm:gap-2">
                    <span class="font-medium text-gray-700">{{ $areaLabel }}:</span>
                    <span class="text-gray-500">{{ $areaDescriptor }}</span>
                </div>
            @endif
            @if (!empty($secondaryAreaDescriptor))
                <div class="flex flex-col sm:flex-row sm:items-start sm:gap-2">
                    <span class="font-medium text-gray-700">{{ $secondaryAreaLabel ?? 'Wilayah Pembanding' }}:</span>
                    <span class="text-gray-500">{{ $secondaryAreaDescriptor }}</span>
                </div>
            @endif
            @if (!empty($periodLabel))
                <div class="flex flex-col sm:flex-row sm:items-start sm:gap-2">
                    <span class="font-medium text-gray-700">Periode:</span>
                    <span class="text-gray-500">{{ $periodLabel }}</span>
                </div>
            @endif
        </div>
    </div>
    <div class="dk-table-heading__actions flex flex-col md:flex-row md:items-center md:justify-end gap-3 w-full md:w-auto md:ml-auto">
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
        <div class="flex flex-wrap sm:flex-nowrap items-center justify-end gap-3 w-full">
            @php
                $currentTabPane = $activeCategory ?? 'tab-gender';
                $fullscreenUrl = route($fullscreenRoute, array_merge(request()->query(), ['category' => str_replace('tab-', '', $currentTabPane)]));
            @endphp
            @if (!empty($customDownloads))
                {!! $customDownloads !!}
            @elseif (isset($showDownloadButtons) && $showDownloadButtons)
                @php
                    $downloadCategory = $activeCategory ?? request()->query('category', 'gender');
                    $downloadQuery = array_merge(request()->query(), ['category' => $downloadCategory]);
                    $pdfUrl = route('public.data.download.pdf', $downloadQuery);
                    $excelUrl = route('public.data.download.excel', $downloadQuery);
                    $defaultYear = request()->query('year', $period['year'] ?? now()->year);
                    $defaultSemester = request()->query('semester', $period['semester'] ?? 1);
                    $downloadLabelBase = 'data-' . $downloadCategory . '-' . $defaultYear . '-s' . $defaultSemester;
                @endphp
                <div class="dk-table-heading__downloads flex flex-wrap sm:flex-nowrap items-center gap-2 justify-end">
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
            <a href="{{ $fullscreenUrl }}" target="_blank" class="inline-flex items-center justify-center w-10 h-10 border border-green-300 rounded-lg shadow-sm bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dk-table-heading__fullscreen-btn js-fullscreen-btn ml-0 sm:ml-4" data-base-url="{{ route($fullscreenRoute, request()->query()) }}" title="Buka di tab baru (Fullscreen)">
                <img src="{{ asset('img/maximize.png') }}" alt="" class="w-5 h-5 object-contain" aria-hidden="true">
                <span class="sr-only">Buka di tab baru (Fullscreen)</span>
            </a>
        </div>
    </div>
</div>
