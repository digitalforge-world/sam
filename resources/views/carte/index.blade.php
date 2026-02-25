@extends('layouts.app')
@section('title', 'Carte')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li><li class="breadcrumb-item current">Carte</li>@endsection

@push('styles')
{{-- Leaflet CSS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" />
<style>
    .map-container { position:relative; width:100%; height: calc(100vh - 160px); border-radius:12px; overflow:hidden; }
    #map { width:100%; height:100%; z-index: 1; }
    .map-sidebar { position:absolute; top:12px; left:12px; z-index:1000; background:var(--bg-card); border-radius:12px; padding:16px; width:280px; box-shadow:var(--shadow-lg); max-height:calc(100% - 24px); overflow-y:auto; }
    .map-sidebar h3 { font-size:14px; font-weight:700; margin-bottom:12px; color:var(--text-primary); }
    .map-legend { padding:0; margin:16px 0 0; list-style:none; }
    .legend-item { display:flex; align-items:center; gap:8px; padding:4px 0; font-size:12px; color:var(--text-secondary); }
    .legend-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; }
    .map-toolbar { position:absolute; top:12px; right:12px; z-index:1000; display:flex; flex-direction:column; gap:6px; }
    .map-toolbar button { width:36px; height:36px; border:none; border-radius:8px; background:var(--bg-card); box-shadow:var(--shadow-md); display:flex;align-items:center;justify-content:center; cursor:pointer; transition: all .2s; }
    .map-toolbar button:hover { background:var(--color-primary); color:#fff; }
    .map-toolbar button.active { background:var(--color-primary); color:#fff; }
    .map-popup-header { font-weight:700; font-size:14px; margin-bottom:6px; }
    .map-popup-row { display:flex; justify-content:space-between; font-size:12px; padding:2px 0; }
    .map-popup-label { color:#6b7280; }
    .map-popup-value { font-weight:600; }

    /* Fix Leaflet controls z-index inside our container */
    .leaflet-control-container .leaflet-top,
    .leaflet-control-container .leaflet-bottom { z-index: 500; }
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
        <button id="btn-satellite" onclick="switchLayer('satellite')" title="Satellite"><i data-lucide="image" style="width:16px;height:16px"></i></button>
        <button id="btn-light" onclick="switchLayer('light')" title="Clair"><i data-lucide="sun" style="width:16px;height:16px"></i></button>
        <button id="btn-dark" onclick="switchLayer('dark')" title="Sombre"><i data-lucide="moon" style="width:16px;height:16px"></i></button>
        <button id="btn-osm" onclick="switchLayer('osm')" title="Standard"><i data-lucide="map" style="width:16px;height:16px"></i></button>
    </div>

    <div id="map"></div>
</div>
@endsection

@push('scripts')
{{-- Leaflet JS --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ═══ Tile layers (all FREE, no API key needed) ═══
    const tileLayers = {
        osm: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 19
        }),
        light: L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; <a href="https://carto.com/">CARTO</a>',
            maxZoom: 20
        }),
        dark: L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; <a href="https://carto.com/">CARTO</a>',
            maxZoom: 20
        }),
        satellite: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: '&copy; Esri, Maxar, Earthstar Geographics',
            maxZoom: 18
        })
    };

    // ═══ Initialize map ═══
    const map = window.map = L.map('map', {
        center: [1.1, 6.7].reverse(),   // Leaflet uses [lat, lng] — Togo center
        zoom: 7,
        layers: [tileLayers.light],
        zoomControl: false
    });

    // Add zoom control bottom-right
    L.control.zoom({ position: 'bottomright' }).addTo(map);

    let currentLayer = 'light';

    // ═══ Switch tile layer ═══
    window.switchLayer = function(name) {
        if (currentLayer === name) return;
        map.removeLayer(tileLayers[currentLayer]);
        map.addLayer(tileLayers[name]);
        currentLayer = name;

        // Update active button states
        document.querySelectorAll('.map-toolbar button[id^="btn-"]').forEach(b => b.classList.remove('active'));
        const btn = document.getElementById('btn-' + name);
        if (btn) btn.classList.add('active');
    };

    // ═══ Drawing ═══
    const drawnItems = new L.FeatureGroup();
    map.addLayer(drawnItems);

    const drawControl = new L.Control.Draw({
        position: 'topright',
        draw: {
            polygon: {
                allowIntersection: false,
                shapeOptions: { color: 'var(--color-primary, #1B6B4A)', weight: 2 }
            },
            polyline: false,
            rectangle: false,
            circle: false,
            marker: false,
            circlemarker: false
        },
        edit: {
            featureGroup: drawnItems
        }
    });

    let drawActive = false;

    window.toggleDraw = function() {
        drawActive = !drawActive;
        if (drawActive) {
            map.addControl(drawControl);
            document.getElementById('btn-draw').classList.add('active');
        } else {
            map.removeControl(drawControl);
            document.getElementById('btn-draw').classList.remove('active');
        }
    };

    window.resetView = function() {
        map.flyTo([6.7, 1.1], 7);
    };

    // ═══ GeoJSON layer for parcelles ═══
    let parcellesLayer = null;

    function getStyle(feature) {
        const color = feature.properties.couleur || '#94a3b8';
        return {
            fillColor: color,
            color: color,
            weight: 2,
            opacity: 1,
            fillOpacity: 0.35
        };
    }

    function onEachFeature(feature, layer) {
        if (feature.properties) {
            const p = feature.properties;
            layer.bindPopup(`
                <div class="map-popup-header">${p.producteur || '—'}</div>
                <div class="map-popup-row"><span class="map-popup-label">Culture</span><span class="map-popup-value">${p.culture || '—'}</span></div>
                <div class="map-popup-row"><span class="map-popup-label">Village</span><span class="map-popup-value">${p.village || '—'}</span></div>
                <div class="map-popup-row"><span class="map-popup-label">Superficie</span><span class="map-popup-value">${p.superficie || '—'} ha</span></div>
                <div class="map-popup-row"><span class="map-popup-label">Statut</span><span class="map-popup-value">${p.approbation || '—'}</span></div>
            `);
        }
        layer.on('mouseover', function() { this.setStyle({ fillOpacity: 0.6, weight: 3 }); });
        layer.on('mouseout',  function() { parcellesLayer.resetStyle(this); });
    }

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
                // Remove existing layer
                if (parcellesLayer) {
                    map.removeLayer(parcellesLayer);
                }

                // Add new GeoJSON layer
                parcellesLayer = L.geoJSON(data, {
                    style: getStyle,
                    onEachFeature: onEachFeature
                }).addTo(map);

                // Fit bounds if there are features
                if (data.features && data.features.length > 0) {
                    map.fitBounds(parcellesLayer.getBounds(), { padding: [50, 50] });
                }
            });
    }

    // Initial load
    loadGeoJSON();

    // ═══ Dynamic Producteur Filter ═══
    document.getElementById('filter-zone').addEventListener('change', function() {
        const zoneId = this.value;
        const prodSelect = document.getElementById('filter-producteur');
        prodSelect.innerHTML = '<option value="">Chargement...</option>';

        fetch('{{ route("producteurs.filter") }}?zone_id=' + zoneId)
            .then(r => r.json())
            .then(data => {
                prodSelect.innerHTML = '<option value="">Tous</option>';
                data.forEach(p => {
                    prodSelect.innerHTML += `<option value="${p.value}">${p.label}</option>`;
                });
                loadGeoJSON();
            });
    });

    document.getElementById('filter-producteur').addEventListener('change', loadGeoJSON);
    document.getElementById('filter-bio').addEventListener('change', loadGeoJSON);

    // ═══ Save drawn contour ═══
    map.on(L.Draw.Event.CREATED, function(event) {
        const layer = event.layer;
        drawnItems.addLayer(layer);

        // Get coordinates in GeoJSON format [lng, lat]
        const coords = layer.toGeoJSON().geometry.coordinates;
        const parcelleId = prompt('Entrez l\'ID de la parcelle pour associer ce contour:');
        if (!parcelleId) {
            drawnItems.removeLayer(layer);
            return;
        }

        fetch('{{ route("carte.save-contour") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ parcelle_id: parcelleId, contour: JSON.stringify(coords) })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                loadGeoJSON();
                drawnItems.clearLayers();
                alert('Contour enregistré avec succès !');
            } else {
                alert('Erreur: ' + (data.message || 'inconnue'));
                drawnItems.removeLayer(layer);
            }
        });
    });

    lucide.createIcons();
});
</script>
@endpush
