@extends('layouts.dukcapil', ['title' => 'Serdadu'])

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
            <div class="col-12">
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
        </div>

        <div class="row g-4 mt-1">
            <div class="col-12">
                <div class="dk-card">
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
    <!-- Load pre-generated GeoJSON data from public/map/peta-madiun -->
    <script src="{{ asset('map/peta-madiun/kab.js') }}"></script>
    <script src="{{ asset('map/peta-madiun/kec.js') }}"></script>
    <script src="{{ asset('map/peta-madiun/kel.js') }}"></script>

    <script>
        (function(){
            // Basemap providers
            var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '© OpenStreetMap' });
            var carto = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap contributors &copy; CARTO' });
            var esri = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19, attribution: 'Tiles © Esri' });
            var stamen = L.tileLayer('https://stamen-tiles-{s}.a.ssl.fastly.net/terrain/{z}/{x}/{y}.jpg', { maxZoom: 18, subdomains: 'abcd', attribution: 'Map tiles by Stamen Design' });

            var map = L.map('landing-map', { center: [-7.629, 111.515], zoom: 11, layers: [carto] });
            L.control.scale({imperial:false, maxWidth: 160}).addTo(map);

            // Higher-contrast styles
            function styleKab(feature){ return { color:'#9b2c2b', weight:3, dashArray: '', fill:false }; }
            function styleKec(feature){ return { color:'#c75a11', weight:2, fillColor:'#f39c12', fillOpacity:0.14 }; }
            function styleKel(feature){ return { color:'#1f6fa6', weight:1, fillColor:'#2d9cdb', fillOpacity:0.08 }; }
            function highlightStyle(){ return { weight:4, color:'#ffeb3b', fillOpacity:0.25 }; }

            // helper to format props as table and try to find population fields
            function buildPopupHtml(name, props){
                var p = props || {};
                var html = '<div style="max-width:320px"><strong>' + (name||'Feature') + '</strong><table style="margin-top:6px">';
                var pop = null;
                for(var k in p){ if(!p.hasOwnProperty(k)) continue; var v = p[k];
                    html += '<tr><td style="padding:2px 6px;vertical-align:top;color:#555;font-size:13px"><strong>'+k+'</strong></td>' +
                           '<td style="padding:2px 6px;vertical-align:top;color:#222;font-size:13px">' + (v===null? '': String(v)) + '</td></tr>';
                    var lk = k.toLowerCase();
                    if(lk.indexOf('pop')>=0 || lk.indexOf('penduduk')>=0 || lk.indexOf('jumlah')>=0) { pop = v; }
                }
                if(pop!==null){ html += '<tr><td style="padding:4px 6px"><strong>Estimasi Penduduk</strong></td><td style="padding:4px 6px">' + String(pop) + '</td></tr>'; }
                html += '</table></div>';
                return html;
            }

            function makeOnEachFeatureFactory(name){
                return function(feature, layer){
                    var props = feature && feature.properties ? feature.properties : {};
                    layer.bindPopup(buildPopupHtml(name, props));

                    layer.on({
                        mouseover: function(e){
                            var lyr = e.target;
                            lyr.setStyle(highlightStyle());
                            if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) { lyr.bringToFront(); }
                        },
                        mouseout: function(e){
                            try { geojsonResetStyle(e.target); } catch(err){}
                        },
                        click: function(e){
                            var b = e.target.getBounds ? e.target.getBounds() : null;
                            if(b) map.fitBounds(b.pad(0.12));
                            e.target.openPopup();
                        }
                    });
                };
            }

            // Create layer groups (will populate later)
            var kabLayer = L.layerGroup().addTo(map);
            var kecLayer = L.layerGroup();
            var kelLayer = L.layerGroup();

            // Label layers
            var kecLabelLayer = L.layerGroup();
            var kelLabelLayer = L.layerGroup();

            // keep reference to geojson objects to reset styles
            var geojsonKec = null, geojsonKel = null, geojsonKab = null;

            // Load geo data if present
            if(window.kab){
                geojsonKab = L.geoJSON(window.kab, { style: styleKab, onEachFeature: makeOnEachFeatureFactory('Kabupaten') }).addTo(kabLayer);
            }
            if(window.kec){
                geojsonKec = L.geoJSON(window.kec, { style: styleKec, onEachFeature: makeOnEachFeatureFactory('Kecamatan') }).addTo(kecLayer);
            }
            if(window.kel){
                // we'll create numbered labels for kelurahan/desa
                var kelIndex = 0;
                geojsonKel = L.geoJSON(window.kel, { style: styleKel, onEachFeature: function(feature, layer){
                    makeOnEachFeatureFactory('Kelurahan/Desa')(feature, layer);
                    // create numeric label at centroid
                    try {
                        kelIndex++;
                        var c = layer.getBounds().getCenter();
                        var number = kelIndex;
                        var icon = L.divIcon({ className: 'kel-label', html: '<div style="background:rgba(255,255,255,0.9);padding:2px 6px;border-radius:4px;border:1px solid #ccc;font-weight:600;font-size:12px">'+number+'</div>' });
                        var marker = L.marker(c, { icon: icon, interactive:false });
                        marker._kelNumber = number;
                        marker._kelName = (feature.properties && (feature.properties.NAMA||feature.properties.nama||feature.properties.NAME||feature.properties.Name||feature.properties.KELURAHAN)) ? (feature.properties.NAMA||feature.properties.nama||feature.properties.NAME||feature.properties.Name||feature.properties.KELURAHAN) : ('Desa ' + number);
                        kelLabelLayer.addLayer(marker);
                        layer._kelNumber = number;
                    } catch(e){}
                }}).addTo(kelLayer);
            }

            // create alphabet labels for kecamatan
            var kecLetters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('');
            var kecMapping = []; // {letter, name}
            if(geojsonKec){
                var i = 0;
                geojsonKec.eachLayer(function(layer){
                    try{
                        var center = layer.getBounds().getCenter();
                        var name = (layer.feature && layer.feature.properties) ? (layer.feature.properties.NAMA||layer.feature.properties.nama||layer.feature.properties.NAME||layer.feature.properties.Name||layer.feature.properties.KECAMATAN||layer.feature.properties.kecamatan) : ('Kec ' + (i+1));
                        var letter = kecLetters[i % kecLetters.length];
                        var icon = L.divIcon({ className: 'kec-label', html: '<div style="background:rgba(255,255,255,0.95);padding:4px 7px;border-radius:4px;border:1px solid #ccc;font-weight:700">'+letter+'</div>' });
                        var marker = L.marker(center, { icon: icon, interactive:false });
                        marker._kecLetter = letter; marker._kecName = name;
                        kecLabelLayer.addLayer(marker);
                        kecMapping.push({ letter: letter, name: name });
                        i++;
                    }catch(e){}
                });
            }

            // helper to reset style for geojson layers
            function geojsonResetStyle(layer){
                if(geojsonKec && geojsonKec.resetStyle && layer.feature && layer.feature.geometry && layer.feature.geometry.type){ geojsonKec.resetStyle(layer); }
                if(geojsonKel && geojsonKel.resetStyle && layer.feature && layer.feature.geometry && layer.feature.geometry.type){ geojsonKel.resetStyle(layer); }
                if(geojsonKab && geojsonKab.resetStyle && layer.feature && layer.feature.geometry && layer.feature.geometry.type){ geojsonKab.resetStyle(layer); }
            }

            // Fit to kab bounds if available
            try{ var bounds = kabLayer.getBounds && kabLayer.getBounds(); if(bounds && bounds.isValid && bounds.isValid()) map.fitBounds(bounds.pad(0.05)); }catch(e){}

            var baseLayers = { 'Carto Light': carto, 'OpenStreetMap': osm, 'Esri Imagery': esri, 'Stamen Terrain': stamen };
            var overlays = { 'Kabupaten (batas)': kabLayer, 'Kecamatan': kecLayer, 'Kelurahan/Desa': kelLayer };
            L.control.layers(baseLayers, overlays, { collapsed:false, position:'topright' }).addTo(map);

            // Add label layers to overlays (but don't show by default)
            overlays['Label Kecamatan (A)'] = kecLabelLayer;
            overlays['Label Kelurahan (1)'] = kelLabelLayer;

            // custom control: checkboxes for toggles and kec filtering
            var controlHtml = '<div style="padding:8px;max-width:240px;font-size:13px">' +
                '<div style="margin-bottom:6px"><strong>Peta - Opsi</strong></div>' +
                '<div><label><input type="checkbox" id="chk-kab" checked> Kabupaten</label></div>' +
                '<div><label><input type="checkbox" id="chk-kec" checked> Kecamatan</label></div>' +
                '<div><label><input type="checkbox" id="chk-kel" checked> Kelurahan/Desa</label></div>' +
                '<hr style="margin:6px 0">' +
                '<div><label><input type="checkbox" id="chk-kec-label"> Tampilkan Label Kecamatan (huruf)</label></div>' +
                '<div><label><input type="checkbox" id="chk-kel-label"> Tampilkan Label Desa (angka)</label></div>' +
                '<hr style="margin:6px 0">' +
                '<div><strong>Filter Kecamatan</strong></div>' +
                '<div id="kec-filter-list" style="max-height:160px;overflow:auto;margin-top:6px"></div>' +
                '</div>';

            var customControl = L.control({position:'topleft'});
            customControl.onAdd = function(){ var div = L.DomUtil.create('div','leaflet-bar'); div.innerHTML = controlHtml; L.DomEvent.disableClickPropagation(div); return div; };
            customControl.addTo(map);

            // wire up checkboxes
            function $(id){ return document.getElementById(id); }
            function setLayerVisible(layer, visible){ if(visible){ if(!map.hasLayer(layer)) map.addLayer(layer); } else { if(map.hasLayer(layer)) map.removeLayer(layer); } }

            // initial states
            setLayerVisible(kabLayer, true);
            setLayerVisible(kecLayer, true);
            setLayerVisible(kelLayer, true);

            // populate kec filter list
            var kecNames = [];
            if(geojsonKec){
                geojsonKec.eachLayer(function(layer){
                    var name = (layer.feature && layer.feature.properties) ? (layer.feature.properties.NAMA||layer.feature.properties.nama||layer.feature.properties.NAME||layer.feature.properties.Name||layer.feature.properties.KECAMATAN||layer.feature.properties.kecamatan) : 'Unknown';
                    if(kecNames.indexOf(name)===-1) kecNames.push(name);
                });
            }
            var kecFilterList = $('kec-filter-list');
            kecNames.forEach(function(name){
                var id = 'kf-' + name.replace(/[^a-z0-9]/gi,'_');
                var wrapper = document.createElement('div');
                wrapper.innerHTML = '<label style="display:flex;align-items:center;gap:8px"><input type="checkbox" class="kec-filter-chk" id="'+id+'" checked> <span style="flex:1">'+name+'</span></label>';
                kecFilterList.appendChild(wrapper);
            });

            function applyKecFilter(){
                var checked = Array.from(document.querySelectorAll('.kec-filter-chk')).filter(c=>c.checked).map(c=>c.nextElementSibling ? c.nextElementSibling.textContent.trim() : '');
                if(!geojsonKel) return;
                geojsonKel.eachLayer(function(layer){
                    var p = layer.feature && layer.feature.properties ? layer.feature.properties : {};
                    var kname = p.NAMA_KEC || p.NAMA_KC || p.KECAMATAN || p.kecamatan || p.kec || p.nama_kecamatan || p.kelurahan || p.kel || p.KELURAHAN || p.Kecamatan || p.NAMA || p.nama || p.NAME || '';
                    // normalize
                    kname = String(kname||'').trim();
                    if(checked.indexOf(kname) >= 0) { if(!map.hasLayer(layer)) map.addLayer(layer); } else { if(map.hasLayer(layer)) map.removeLayer(layer); }
                });
            }

            // event listeners
            $('chk-kab').addEventListener('change', function(e){ setLayerVisible(kabLayer, e.target.checked); });
            $('chk-kec').addEventListener('change', function(e){ setLayerVisible(kecLayer, e.target.checked); });
            $('chk-kel').addEventListener('change', function(e){ setLayerVisible(kelLayer, e.target.checked); });
            $('chk-kec-label').addEventListener('change', function(e){ setLayerVisible(kecLabelLayer, e.target.checked); });
            $('chk-kel-label').addEventListener('change', function(e){ setLayerVisible(kelLabelLayer, e.target.checked); });

            // wire filter checkboxes
            Array.from(document.querySelectorAll('.kec-filter-chk')).forEach(function(chk){ chk.addEventListener('change', applyKecFilter); });

            // Add a compact legend control for color meanings and basic label toggle info
            var legend = L.control({position:'bottomright'});
            legend.onAdd = function(){
                var div = L.DomUtil.create('div','legend');
                div.style.padding = '8px';
                div.style.maxWidth = '260px';
                div.innerHTML = '<div style="font-weight:700;margin-bottom:6px">Legenda</div>' +
                    '<div class="legend-item"><span class="legend-color" style="background:#9b2c2b"></span> Kabupaten</div>' +
                    '<div class="legend-item" style="margin-top:6px"><span class="legend-color" style="background:#c75a11"></span> Kecamatan</div>' +
                    '<div class="legend-item" style="margin-top:6px"><span class="legend-color" style="background:#1f6fa6"></span> Kelurahan/Desa</div>' +
                    '<hr style="margin:8px 0">' +
                    '<div style="font-size:12px">Label Huruf: Kecamatan · Label Angka: Desa<br><small>Gunakan opsi di kiri atas untuk menyalakan/mematikan.</small></div>';
                return div;
            };
            legend.addTo(map);

            // Add mapping panel (scrollable) for labels (kec and kel sample)
            var mappingControl = L.control({position:'bottomleft'});
            mappingControl.onAdd = function(){
                var div = L.DomUtil.create('div','leaflet-bar');
                div.style.padding = '8px'; div.style.maxWidth='320px'; div.style.maxHeight='220px'; div.style.overflow='auto';
                var html = '<div style="font-weight:700;margin-bottom:6px">Label Mapping</div>';
                if(kecMapping.length){ html += '<div style="font-size:13px;margin-bottom:6px"><strong>Huruf - Kecamatan</strong><div style="max-height:100px;overflow:auto;margin-top:6px">';
                    kecMapping.forEach(function(m){ html += '<div><strong>'+m.letter+':</strong> '+m.name+'</div>'; }); html += '</div></div>';
                }
                if(kelLabelLayer.getLayers().length){ html += '<div style="font-size:13px;margin-top:6px"><strong>Angka - Desa (contoh pertama 50)</strong><div style="max-height:100px;overflow:auto;margin-top:6px">';
                    var count = 0; kelLabelLayer.eachLayer(function(l){ if(count<200){ html += '<div><strong>'+ (l._kelNumber || '?') +':</strong> '+ (l._kelName || '') +'</div>'; } count++; }); html += '</div></div>';
                }
                div.innerHTML = html;
                return div;
            };
            mappingControl.addTo(map);

            // Performance: keep kelLabelLayer off by default if many labels present
            if(kelLabelLayer.getLayers().length > 150){ /* keep off */ }

        })();
    </script>
@endpush
