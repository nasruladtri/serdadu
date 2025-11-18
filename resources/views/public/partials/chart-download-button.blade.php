@php
    $category = $category ?? 'gender';
    $queryParams = array_merge(request()->query(), ['category' => $category]);
    $downloadUrl = route('public.charts.download.pdf', $queryParams);
    $defaultYear = request()->query('year', now()->year);
    $defaultSemester = request()->query('semester', 1);
    $downloadLabelBase = 'chart-' . $category . '-' . $defaultYear . '-s' . $defaultSemester;
@endphp
<div class="dk-table-heading__downloads flex flex-wrap gap-2 items-center justify-end text-right">
    <span 
        class="js-download-btn inline-flex items-center justify-center w-12 h-12 p-1.5 border border-black/70 rounded-xl shadow-sm bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 cursor-pointer select-none transition"
        style="width:2.25rem;height:2.25rem;padding:0.55rem;"
        data-download-type="chart"
        data-download-format="pdf"
        data-download-url="{{ $downloadUrl }}"
        data-download-label="{{ $downloadLabelBase }}.pdf"
        data-year-default="{{ $defaultYear }}"
        data-semester-default="{{ $defaultSemester }}"
        role="button"
        tabindex="0"
        aria-label="Download PDF">
        <img src="{{ asset('img/pdf.png') }}" alt="PDF icon" class="w-7 h-7 md:w-8 md:h-8 object-contain" style="width:1.3rem;height:1.3rem;">
    </span>
</div>
