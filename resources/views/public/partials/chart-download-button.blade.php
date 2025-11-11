@php
    $category = $category ?? 'gender';
    $queryParams = array_merge(request()->query(), ['category' => $category]);
    $downloadUrl = route('public.charts.download.pdf', $queryParams);
    $defaultYear = request()->query('year', now()->year);
    $defaultSemester = request()->query('semester', 1);
    $downloadLabelBase = 'chart-' . $category . '-' . $defaultYear . '-s' . $defaultSemester;
@endphp
<div class="dk-table-heading__downloads flex flex-wrap gap-2 items-center justify-end text-right">
    <span class="text-sm font-semibold text-gray-600 whitespace-nowrap leading-tight">Download:</span>
    <span 
        class="js-download-btn inline-flex items-center px-4 py-1.5 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 cursor-pointer select-none"
        data-download-type="chart"
        data-download-format="pdf"
        data-download-url="{{ $downloadUrl }}"
        data-download-label="{{ $downloadLabelBase }}.pdf"
        data-year-default="{{ $defaultYear }}"
        data-semester-default="{{ $defaultSemester }}"
        role="button"
        tabindex="0"
        aria-label="Download PDF">
        <img src="{{ Vite::asset('resources/img/pdf.png') }}" alt="PDF icon" class="w-5 h-5 object-contain">
    </span>
</div>

