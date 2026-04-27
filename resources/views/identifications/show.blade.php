@extends('layouts.app')
@section('title', 'Détails de l\'identification')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
    <li class="breadcrumb-item"><a href="{{ route('identifications.index') }}">Identifications</a></li>
    <li class="breadcrumb-item current">Détails</li>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    #map { height: 400px; width: 100%; border-radius: 8px; z-index: 1; border: 1px solid #e2e8f0; }
    .superficie-label {
        background: rgba(255, 255, 255, 0.4);
        padding: 5px 10px;
        border-radius: 4px;
        color: #1a1a1a;
        font-weight: 800;
        font-size: 1.8rem;
        text-align: center;
        width: auto !important;
        height: auto !important;
        white-space: nowrap;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        transform: translate(-50%, -50%);
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Identification #{{ $identification->numero }}</h1>
        <p class="page-subtitle">Producteur : {{ $identification->producteur->nom }} {{ $identification->producteur->prenom }}</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('identifications.index') }}" class="btn-secondary-custom">
            <i data-lucide="arrow-left" style="width:16px;height:16px"></i> Retour aux identifications
        </a>
    </div>
</div>

@can('identifications.approve')
@if($identification->statut === 'EN_ATTENTE')
<div class="card" style="grid-column: 1 / -1; background-color: #f0f7ff; border: 1px solid #cce3ff; margin-bottom: 2rem; padding: 20px;">
    <h3 class="card-title" style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px;">
        <i data-lucide="shield-check" style="width:20px;height:20px; color: var(--primary);"></i>
        Actions de Validation
    </h3>
    <form action="{{ route('identifications.approve', $identification) }}" method="POST">
        @csrf
        @method('PATCH')
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label for="approbation" class="form-label" style="font-weight: 600; margin-bottom: 0.5rem; display: block;">Type d'approbation (si approuvée)</label>
                <select name="approbation" id="approbation" class="form-control" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #d1d5db;">
                    <option value="OK">OK - Conforme</option>
                    <option value="BIO">BIO - Agriculture Biologique</option>
                    <option value="DECLASSIFIED">Déclassé</option>
                </select>
            </div>

            <div>
                <label for="commentaire" class="form-label" style="font-weight: 600; margin-bottom: 0.5rem; display: block;">Commentaire / Motif de rejet</label>
                <input type="text" name="commentaire" id="commentaire" class="form-control" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #d1d5db;" placeholder="Expliquez votre décision si nécessaire...">
            </div>
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end;">
            <button type="submit" name="statut" value="APPROUVE" class="btn-primary-custom" style="background-color: var(--success); border: none; padding: 10px 20px; color: white; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="check" style="width:18px;height:18px"></i> Approuver l'identification
            </button>
            <button type="submit" name="statut" value="REJETE" class="btn-primary-custom" style="background-color: var(--error); border: none; padding: 10px 20px; color: white; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 8px;" onclick="return confirm('Voulez-vous vraiment rejeter cette identification ? Elle sera renvoyée au contrôleur.')">
                <i data-lucide="x" style="width:18px;height:18px"></i> Rejeter / Renvoyer
            </button>
        </div>
    </form>
</div>
@endif
@endcan

<div class="grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem;">
    <!-- Général -->
    <div class="card">
        <h3 class="card-title">Informations Générales</h3>
        <table class="data-table" style="margin-top: 15px;">
            <tr><th>Numéro ID</th><td>{{ $identification->numero }}</td></tr>
            <tr><th>Campagne</th><td>{{ $identification->campagne }}</td></tr>
            <tr><th>Statut</th>
                <td>
                    @if($identification->statut === 'APPROUVE')<span class="badge-status badge-bio">Approuvée</span>
                    @elseif($identification->statut === 'REJETE')<span class="badge-status badge-error">Rejetée</span>
                    @else<span class="badge-status badge-ok">En attente</span>@endif
                </td>
            </tr>
            <tr><th>Village</th><td>{{ $identification->village ?? '—' }}</td></tr>
            <tr><th>Organisation</th><td>{{ $identification->organisation_paysanne ?? '—' }}</td></tr>
            <tr><th>Superficie</th><td>{{ $identification->superficie ? number_format($identification->superficie, 2) . ' ha' : '—' }}</td></tr>
            <tr><th>Type de culture</th><td>{{ $identification->type_culture ?? '—' }}</td></tr>
            <tr><th>Contrôleur</th><td>{{ $identification->controleur?->name ?? '—' }}</td></tr>
            <tr><th>Date d'identification</th><td>{{ $identification->created_at->format('d/m/Y H:i') }}</td></tr>
        </table>
    </div>

    <!-- Médias -->
    <div class="card">
        <h3 class="card-title">Photo et Signature</h3>
        <div style="margin-top: 15px;">
            @if($identification->photo_parcelle)
                @php
                    $photo = $identification->photo_parcelle;
                    if (strlen($photo) > 500 && !Str::startsWith($photo, 'data:image')) {
                        $photoSrc = 'data:image/jpeg;base64,' . $photo;
                    } elseif (Str::startsWith($photo, ['http', 'data:image'])) {
                        $photoSrc = $photo;
                    } else {
                        $photoSrc = Storage::url($photo);
                    }
                @endphp
                <div style="margin-bottom: 20px;">
                    <strong>Photo de l'identification :</strong><br>
                    <img src="{{ $photoSrc }}" alt="Photo" style="max-width: 100%; max-height: 250px; border-radius: 8px; margin-top: 10px; border: 1px solid #ccc; object-fit: cover;">
                </div>
            @endif

            @if($identification->signature_producteur)
                @php
                    $sig = $identification->signature_producteur;
                    if (strlen($sig) > 500 && !Str::startsWith($sig, 'data:image')) {
                        $sigSrc = 'data:image/png;base64,' . $sig;
                    } elseif (Str::startsWith($sig, ['http', 'data:image'])) {
                        $sigSrc = $sig;
                    } else {
                        $sigSrc = Storage::url($sig);
                    }
                @endphp
                <div>
                    <strong>Signature du producteur :</strong><br>
                    <img src="{{ $sigSrc }}" alt="Signature" style="max-width: 100%; max-height: 150px; border-radius: 4px; border: 1px solid #eee; margin-top: 10px; background: #fdfdfd; object-fit: contain;">
                </div>
            @endif

            @if(!$identification->photo_parcelle && !$identification->signature_producteur)
                <div class="empty-state">
                    <div class="empty-icon"><i data-lucide="image-off" style="width:24px;height:24px"></i></div>
                    <div class="empty-title" style="font-size: 0.9rem;">Aucune photo ni signature enregistrée.</div>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Localisation et Coordonnées GPS -->
    <div class="card" style="grid-column: 1 / -1;">
        <h3 class="card-title">Localisation et Coordonnées GPS</h3>
        <div style="margin-top: 15px; display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            <div>
                <table class="data-table">
                    <tr><th>Village / Localité</th><td>{{ $identification->village ?? '—' }}</td></tr>
                    <tr><th>Nom de la parcelle</th><td>{{ $identification->nom_parcelle ?? '—' }}</td></tr>
                    <tr><th>Niveau de pente</th><td>{{ $identification->niveau_pente ?? '—' }}</td></tr>
                    <tr><th>Maisons environnantes</th><td>{{ $identification->maisons_environnantes ? 'Oui' : 'Non' }}</td></tr>
                    <tr><th>Cultures à proximité</th><td>{{ $identification->cultures_proximite ?? '—' }}</td></tr>
                </table>
            </div>
            <div>
                <strong>Coordonnées GPS (Polygone) :</strong>
                <div style="margin-top: 10px; max-height: 200px; overflow-y: auto; background: #f8f9fa; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px;">
                    @if(is_array($identification->coordonnees_polygon) && count($identification->coordonnees_polygon) > 0)
                        <ul style="list-style-type: none; padding-left: 0; margin: 0; font-family: monospace; font-size: 0.9rem;">
                            @foreach($identification->coordonnees_polygon as $index => $point)
                                <li style="padding: 4px 0; border-bottom: 1px solid #eee;">
                                    Point {{ $index + 1 }}: Lat {{ rtrim(number_format($point['latitude'] ?? $point['lat'] ?? 0, 6, '.', ''), '0') }}, Lng {{ rtrim(number_format($point['longitude'] ?? $point['lng'] ?? $point['lon'] ?? 0, 6, '.', ''), '0') }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div style="color: #666; font-size: 0.9rem;">Aucune coordonnée enregistrée pour les limites de cette parcelle.</div>
                    @endif
                </div>
            </div>
        </div>

        @if(is_array($identification->coordonnees_polygon) && count($identification->coordonnees_polygon) > 0)
        <div style="margin-top: 20px;">
            <div id="map"></div>
        </div>
        @endif
    </div>
    
    <!-- Historique -->
    <div class="card" style="grid-column: 1 / -1;">
        <h3 class="card-title">Historique de l'identification / Questionnaire</h3>
        <div style="margin-top: 15px; overflow-x: auto;">
            <table class="data-table">
                <tbody>
                    <tr><td style="width:40%;"><strong>Préparation du sol :</strong></td><td>{{ $identification->preparation_sol ? 'Oui' : 'Non' }} (Date: {{ $identification->date_preparation_sol ?? '-' }})</td></tr>
                    <tr><td><strong>Semences :</strong></td><td>{{ $identification->semences ? 'Oui' : 'Non' }} (Date: {{ $identification->date_semis ?? '-' }})</td></tr>
                    <tr><td><strong>Dates des sarclages :</strong></td><td>S1: {{ $identification->date_sarclage_1 ?? '-' }} | S2: {{ $identification->date_sarclage_2 ?? '-' }}</td></tr>
                    <tr><td><strong>Fertilisation :</strong></td><td>{{ $identification->fertilisation ? 'Oui' : 'Non' }} (Date: {{ $identification->date_fertilisation ?? '-' }})</td></tr>
                    <tr><td><strong>Gestion adventices / ravageurs :</strong></td><td>A: {{ $identification->gestion_adventices ? 'Oui' : 'Non' }} | R: {{ $identification->gestion_ravageurs ? 'Oui' : 'Non' }}</td></tr>
                    <tr><td><strong>Récolte et Stockage :</strong></td><td>Récolte: {{ $identification->recolte ? 'Oui' : 'Non' }} (Date: {{ $identification->date_recolte ?? '-' }}) | Stockage: {{ $identification->stockage ? 'Oui' : 'Non' }}</td></tr>
                    <tr><td><strong>Isolement des parcelles :</strong></td><td>{{ $identification->isolement_parcelles ? 'Oui' : 'Non' }}</td></tr>
                    <tr><td><strong>Rotation des cultures :</strong></td><td>{{ $identification->rotation_cultures ? 'Oui' : 'Non' }}</td></tr>
                    <tr><td><strong>Diversité biologique :</strong></td><td>{{ $identification->diversite_biologique ? 'Oui' : 'Non' }}</td></tr>
                    <tr><td><strong>Présence de cours d'eau / arbres :</strong></td><td>Eau: {{ $identification->a_cours_eau ? 'Oui' : 'Non' }} | Arbres: {{ is_array($identification->arbres) ? count($identification->arbres) . ' type(s)' : 'Non' }}</td></tr>
                    <tr><td><strong>Gestion des déchets :</strong></td><td>{{ $identification->gestion_dechets ? 'Oui' : 'Non' }}</td></tr>
                    <tr><td><strong>Emballage non conforme :</strong></td><td>{{ $identification->emballage_non_conforme ? 'Oui' : 'Non' }}</td></tr>
                    <tr><td><strong>Participation formations :</strong></td><td>{{ $identification->participation_formations ? 'Oui' : 'Non' }}</td></tr>
                    <tr><td><strong>Production parallèle :</strong></td><td>{{ $identification->production_parallele ? 'Oui' : 'Non' }}</td></tr>
                    <tr><td><strong>Commentaire / Remarques :</strong></td><td>{{ $identification->commentaire ?? '—' }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(is_array($identification->coordonnees_polygon) && count($identification->coordonnees_polygon) > 0)
        const coords = @json($identification->coordonnees_polygon);
        
        // Transform coords to Leaflet format [lat, lng]
        const leafletCoords = coords.map(p => [
            p.latitude ?? p.lat ?? 0,
            p.longitude ?? p.lng ?? p.lon ?? 0
        ]);

        const map = L.map('map', {
            zoomControl: true,
            scrollWheelZoom: false
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Satellite layer option
        const satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: '&copy; Esri'
        });
        
        const baseMaps = {
            "Standard": L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'),
            "Satellite": satellite
        };
        L.control.layers(baseMaps).addTo(map);

        const polygon = L.polygon(leafletCoords, {
            color: '#1B6B4A',
            fillColor: '#22c55e',
            fillOpacity: 0.3,
            weight: 3
        }).addTo(map);

        map.fitBounds(polygon.getBounds(), { padding: [20, 20] });

        // Add superficie label in center
        const center = polygon.getBounds().getCenter();
        L.marker(center, {
            icon: L.divIcon({
                className: 'superficie-label',
                html: '{{ number_format($identification->superficie, 4, ",", " ") }} ha',
                iconAnchor: [50, 10]
            })
        }).addTo(map);
    @endif
});
</script>
@endpush
