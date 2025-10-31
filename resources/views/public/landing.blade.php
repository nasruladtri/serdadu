@extends('layouts.dukcapil', ['title' => 'Beranda1'])

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        /* Landing page map tweaks */
        .dk-map .leaflet-container {
            width: 100%;
            height: 100%;
            border-radius: 6px;
        }
    </style>
@endpush

@section('content')
    @if (!$period)
        <div class="alert alert-warning border-0 dk-card">
            <strong>Data belum tersedia.</strong> Silakan unggah dataset terlebih dahulu melalui halaman admin.
        </div>
    @else
        <div class="row g-4">
            <div class="col-xl-4">
                <div class="dk-card h-100">
                    <div class="card-body p-4">
                        <h6 class="dk-card__title mb-3">Data Agregat Kependudukan</h6>
                        <div class="mb-4">
                            <h6 class="text-uppercase text-xs text-muted mb-2">Wilayah</h6>
                            <div class="table-responsive">
                                <table class="table table-sm dk-table mb-3">
                                    <tbody>
                                        <tr>
                                            <td class="fw-semibold">Nama Wilayah</td>
                                            <td class="text-end">Kabupaten Madiun</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Jumlah Kecamatan</td>
                                            <td class="text-end">{{ number_format($districtCount) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold">Jumlah Desa/Kel</td>
                                            <td class="text-end">{{ number_format($villageCount) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="text-uppercase text-xs text-muted mb-2">Jumlah Penduduk</h6>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="p-3 rounded-3 dk-metric--light">
                                        <div class="dk-metric__label text-white-50">Total Penduduk</div>
                                        <div class="dk-metric text-white">{{ number_format($totals['population']) }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light rounded-3 p-3">
                                        <div class="dk-metric__label">Laki-laki</div>
                                        <div class="dk-metric" style="font-size: 1.2rem;">
                                            {{ number_format($totals['male']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light rounded-3 p-3">
                                        <div class="dk-metric__label">Perempuan</div>
                                        <div class="dk-metric" style="font-size: 1.2rem;">
                                            {{ number_format($totals['female']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="bg-light rounded-3 p-3">
                                        <div class="dk-metric__label">Wajib KTP (≥ 17 tahun)</div>
                                        <div class="dk-metric" style="font-size: 1.2rem;">
                                            {{ number_format($wajibKtp['total']) }}
                                        </div>
                                        <small class="text-muted d-block">
                                            L: {{ number_format($wajibKtp['male']) }} ·
                                            P: {{ number_format($wajibKtp['female']) }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-8">
                <div class="dk-card h-100">
                    <div class="card-body p-0">
                        <div class="p-4 pb-0">
                            <h6 class="dk-card__title mb-2">Peta Kabupaten Madiun</h6>
                        </div>
                        <div class="p-4 pt-0">
                            <div class="ratio ratio-16x9 dk-map">
                                <div id="landing-map"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <!-- Load pre-generated GeoJSON data from public/map/Peta Madiun -->
    <script src="{{ asset('map/Peta Madiun/kab.js') }}"></script>
    <script src="{{ asset('map/Peta Madiun/kec.js') }}"></script>
    <script src="{{ asset('map/Peta Madiun/kel.js') }}"></script>

    <script>
        (function(){
            // create map inside landing-map
            var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            });
            var carto = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors &copy; CARTO'
            });

            var map = L.map('landing-map', {
                center: [-7.629, 111.515],
                zoom: 11,
                layers: [carto]
            });

            L.control.scale({imperial:false, maxWidth: 160}).addTo(map);

            function styleKab(feature) { return { color: '#c0392b', weight: 2, fill: false }; }
            function styleKec(feature) { return { color: '#e67e22', weight: 1.2, fillColor: '#f39c12', fillOpacity: 0.12 }; }
            function styleKel(feature) { return { color: '#2980b9', weight: 0.8, fillColor: '#3498db', fillOpacity: 0.08 }; }

            function onEachFeatureFactory(name){
                return function(feature, layer){
                    var props = feature && feature.properties ? feature.properties : {};
                    var html = '<div style="max-width:280px"><strong>' + (name||'Feature') + '</strong><table style="margin-top:6px">';
                    for (var k in props){ if (!props.hasOwnProperty(k)) continue; html += '<tr><td style="padding:2px 6px;vertical-align:top;color:#555;font-size:13px"><strong>'+k+'</strong></td>' +
                        '<td style="padding:2px 6px;vertical-align:top;color:#222;font-size:13px">' + (props[k]===null? '': String(props[k])) + '</td></tr>'; }
                    html += '</table></div>';
                    layer.bindPopup(html);
                };
            }

            // Safely create layers if geo data exists
            var kabLayer = (window.kab) ? L.geoJSON(window.kab, { style: styleKab, onEachFeature: onEachFeatureFactory('Kabupaten') }).addTo(map) : L.layerGroup().addTo(map);
            var kecLayer = (window.kec) ? L.geoJSON(window.kec, { style: styleKec, onEachFeature: onEachFeatureFactory('Kecamatan') }) : L.layerGroup();
            var kelLayer = (window.kel) ? L.geoJSON(window.kel, { style: styleKel, onEachFeature: onEachFeatureFactory('Kelurahan/Desa') }) : L.layerGroup();

            try {
                var bounds = kabLayer.getBounds && kabLayer.getBounds();
                if (bounds && bounds.isValid && bounds.isValid()) {
                    map.fitBounds(bounds.pad(0.05));
                }
            } catch(e){}

            var baseLayers = { 'Carto Light': carto, 'OpenStreetMap': osm };
            var overlays = { 'Kabupaten': kabLayer, 'Kecamatan': kecLayer, 'Kelurahan/Desa': kelLayer };
            L.control.layers(baseLayers, overlays, {collapsed:false, position:'topright'}).addTo(map);

            var legend = L.control({position: 'bottomright'});
            legend.onAdd = function (){
                var div = L.DomUtil.create('div', 'legend');
                div.innerHTML = '<div class="legend-item"><span class="legend-color" style="background:#c0392b"></span> Kabupaten</div>' +
                    '<div class="legend-item" style="margin-top:6px"><span class="legend-color" style="background:#e67e22"></span> Kecamatan</div>' +
                    '<div class="legend-item" style="margin-top:6px"><span class="legend-color" style="background:#2980b9"></span> Kelurahan/Desa</div>';
                return div;
            };
            legend.addTo(map);
        })();
    </script>
@endpush
