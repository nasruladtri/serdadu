@php
    $axis = (isset($axis) && is_array($axis)) ? $axis : [];
    $horizontal = $horizontal ?? ($axis['horizontal'] ?? null);
    $vertical = $vertical ?? ($axis['vertical'] ?? 'Jumlah penduduk (jiwa)');
    $flipAxes = !empty($flipAxes);

    if ($flipAxes) {
        [$horizontal, $vertical] = [$vertical, $horizontal];
    }
@endphp

@if ($horizontal || $vertical)
    <div class="chart-axis-info text-center text-xs sm:text-sm text-gray-500 leading-relaxed mt-3 space-y-1">
        @if ($vertical)
            <p class="m-0">
                <span class="font-semibold text-gray-700">Sumbu Vertikal:</span> {{ $vertical }}
            </p>
        @endif
        @if ($horizontal)
            <p class="m-0">
                <span class="font-semibold text-gray-700">Sumbu Horizontal:</span> {{ $horizontal }}
            </p>
        @endif
    </div>
@endif
