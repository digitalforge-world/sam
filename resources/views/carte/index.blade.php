@extends('layouts.app')
@section('title', 'Carte')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li><li class="breadcrumb-item current">Carte</li>@endsection

@push('styles')
<link href='https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.css' rel='stylesheet' />
<style>
    .map-container { position:relative; width:100%; height: calc(100vh - 160px); border-radius:12px; overflow:hidden; }
    #map { width:100%; height:100%; }
    .map-sidebar { position:absolute; top:12px; left:12px; z-index:10; background:var(--bg-card); border-radius:12px; padding:16px; width:280px; box-shadow:var(--shadow-lg); max-height:calc(100% - 24px); overflow-y:auto; }
    .map-sidebar h3 { font-size:14px; font-weight:700; margin-bottom:12px; color:var(--text-primary); }
    .map-legend { padding:0; margin:16px 0 0; list-style:none; }
    .legend-item { display:flex; align-items:center; gap:8px; padding:4px 0; font-size:12px; color:var(--text-secondary); }
    .legend-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; }
    .map-toolbar { position:absolute; top:12px; right:12px; z-index:10; display:flex; flex-direction:column; gap:6px; }
    .map-toolbar button { width:36px; height:36px; border:none; border-radius:8px; background:var(--bg-card); box-shadow:var(--shadow-md); display:flex;align-items:center;justify-content:center; cursor:pointer; transition: all .2s; }
    .map-toolbar button:hover { background:var(--color-primary); color:#fff; }
    .map-toolbar button.active { background:var(--color-primary); color:#fff; }
    .map-popup-header { font-weight:700; font-size:14px; margin-bottom:6px; }
    .map-popup-row { display:flex; justify-content:space-between; font-size:12px; padding:2px 0; }
    .map-popup-label { color:#6b7280; }
    .map-popup-value { font-weight:600; }
</style>
@endpush

@section('content')
<div class="page-header" style="margin-bottom:12px;">
    <div><h1 class="page-title">Carte des parcelles</h1></div>
</div>

<div class="map-container">
    {{-- Sidebar Filters --}}
    <div class="map-sidebar">
        <h3><i data-lucide="filter" style="width:14px;height:14px;display:inline"></i> Filtres</h3>
        <div class="form-group" style="margin-bottom:10px">
            <label class="form-label" style="font-size:12px">Zone</label>
            <select id="filter-zone" class="form-select" style="font-size:12px;padding:6px 10px">
                <option value="">Toutes les zones</option>
                @foreach($zones as $z)<option value="{{ $z->id }}">{{ $z->nom }}</option>@endforeach
            </select>
        </div>
        <div class="form-group" style="margin-bottom:10px">
            <label class="form-label" style="font-size:12px">Producteur</label>
            <select id="filter-producteur" class="form-select" style="font-size:12px;padding:6px 10px">
                <option value="">Tous</option>
            </select>
        </div>
        <div class="toggle-row" style="padding:8px 0">
            <div style="font-size:12px;font-weight:600">BIO uniquement</div>
            <label class="toggle-switch" style="transform:scale(.8)"><input type="checkbox" id="filter-bio"><span class="slider"></span></label>
        </div>
        <ul class="map-legend">
            <li class="legend-item"><span class="legend-dot" style="background:#22c55e"></span> BIO certifié</li>
            <li class="legend-item"><span class="legend-dot" style="background:#eab308"></span> OK / En cours</li>
            <li class="legend-item"><span class="legend-dot" style="background:#ef4444"></span> Déclassée</li>
            <li class="legend-item"><span class="legend-dot" style="background:#94a3b8"></span> Non évalué</li>
        </ul>
    </div>

    {{-- Toolbar --}}
    <div class="map-toolbar">
        <button onclick="resetView()" title="Recentrer"><i data-lucide="locate" style="width:16px;height:16px"></i></button>
        <button id="btn-draw" onclick="toggleDraw()" title="Dessiner"><i data-lucide="pencil" style="width:16px;height:16px"></i></button>
        <button onclick="map.setStyle('mapbox://styles/mapbox/satellite-streets-v12')" title="Satellite"><i data-lucide="image" style="width:16px;height:16px"></i></button>
        <button onclick="map.setStyle('mapbox://styles/mapbox/light-v11')" title="Clair"><i data-lucide="sun" style="width:16px;height:16px"></i></button>
    </div>

    <div id="map"></div>
</div>
@endsection

@push('scripts')
<script src='https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.js'></script>
<script src='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.4.3/mapbox-gl-draw.js'></script>
<link rel='stylesheet' href='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.4.3/mapbox-gl-draw.css' />
<script>
document.addEventListener('DOMContentLoaded', function() {
    mapboxgl.accessToken = '{{ config("services.mapbox.token", "") }}';
    const map = window.map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/light-v11',
        center: [1.1, 6.7],   // Togo center
        zoom: 7,
    });
    map.addControl(new mapboxgl.NavigationControl(), 'bottom-right');

    // Draw
    const draw = new MapboxDraw({ displayControlsDefault: false, controls: {} });
    let drawActive = false;

    window.toggleDraw = function() {
        drawActive = !drawActive;
        if (drawActive) { map.addControl(draw); draw.changeMode('draw_polygon'); document.getElementById('btn-draw').classList.add('active'); }
        else { map.removeControl(draw); document.getElementById('btn-draw').classList.remove('active'); }
    };
    window.resetView = function() { map.flyTo({ center: [1.1, 6.7], zoom: 7 }); };

    function loadGeoJSON() {
        const params = new URLSearchParams();
        const zone = document.getElementById('filter-zone').value;
        const prod = document.getElementById('filter-producteur').value;
        const bio  = document.getElementById('filter-bio').checked;
        if (zone) params.set('zone_id', zone);
        if (prod) params.set('producteur_id', prod);
        if (bio)  params.set('bio', '1');

        fetch('{{ route("carte.geojson") }}?' + params.toString())
            .then(r => r.json())
            .then(data => {
                if (map.getSource('parcelles')) {
                    map.getSource('parcelles').setData(data);
                } else {
                    map.addSource('parcelles', { type: 'geojson', data: data });
                    map.addLayer({
                        id: 'parcelles-fill',
                        type: 'fill',
                        source: 'parcelles',
                        paint: { 'fill-color': ['get', 'couleur'], 'fill-opacity': 0.35 }
                    });
                    map.addLayer({
                        id: 'parcelles-border',
                        type: 'line',
                        source: 'parcelles',
                        paint: { 'line-color': ['get', 'couleur'], 'line-width': 2 }
                    });

                    // Popup
                    map.on('click', 'parcelles-fill', function(e) {
                        const p = e.features[0].properties;
                        new mapboxgl.Popup()
                            .setLngLat(e.lngLat)
                            .setHTML(`<div class="map-popup-header">${p.producteur || '—'}</div>
                                <div class="map-popup-row"><span class="map-popup-label">Culture</span><span class="map-popup-value">${p.culture || '—'}</span></div>
                                <div class="map-popup-row"><span class="map-popup-label">Village</span><span class="map-popup-value">${p.village || '—'}</span></div>
                                <div class="map-popup-row"><span class="map-popup-label">Superficie</span><span class="map-popup-value">${p.superficie || '—'} ha</span></div>
                                <div class="map-popup-row"><span class="map-popup-label">Statut</span><span class="map-popup-value">${p.approbation || '—'}</span></div>`)
                            .addTo(map);
                    });
                    map.on('mouseenter', 'parcelles-fill', () => map.getCanvas().style.cursor = 'pointer');
                    map.on('mouseleave', 'parcelles-fill', () => map.getCanvas().style.cursor = '');
                }
            });
    }

    map.on('load', loadGeoJSON);
    document.getElementById('filter-zone').addEventListener('change', loadGeoJSON);
    document.getElementById('filter-producteur').addEventListener('change', loadGeoJSON);
    document.getElementById('filter-bio').addEventListener('change', loadGeoJSON);

    // Save drawn contour
    map.on('draw.create', function(e) {
        const coords = e.features[0].geometry.coordinates;
        const parcelleId = prompt('Entrez l\'ID de la parcelle pour associer ce contour:');
        if (!parcelleId) return;

        fetch('{{ route("carte.save-contour") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ parcelle_id: parcelleId, contour: JSON.stringify(coords) })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) { loadGeoJSON(); alert('Contour enregistré avec succès !'); }
            else { alert('Erreur: ' + (data.message || 'inconnue')); }
        });
    });

    lucide.createIcons();
});
</script>
@endpush
