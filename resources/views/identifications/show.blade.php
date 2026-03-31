@extends('layouts.app')
@section('title', 'Détails de l\'identification')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
    <li class="breadcrumb-item"><a href="{{ route('identifications.index') }}">Identifications</a></li>
    <li class="breadcrumb-item current">Détails</li>
@endsection

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
