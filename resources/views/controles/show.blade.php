@extends('layouts.app')
@section('title', 'Détails du Contrôle')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
    <li class="breadcrumb-item"><a href="{{ route('controles.index') }}">Contrôles</a></li>
    <li class="breadcrumb-item current">{{ $controle->numero }}</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Contrôle #{{ $controle->numero }}</h1>
        <p class="page-subtitle">Producteur : <a href="{{ route('producteurs.show', $controle->producteur) }}" style="color: var(--primary); text-decoration: none; font-weight: 500;">{{ $controle->producteur->nom }} {{ $controle->producteur->prenom }}</a></p>
    </div>
    <div class="header-actions">
        <!-- Return back button -->
        <a href="{{ route('controles.index') }}" class="btn-secondary-custom">
            <i data-lucide="arrow-left" style="width:16px;height:16px"></i> Liste
        </a>
    </div>
</div>

<div class="grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
    <!-- Infos principales -->
    <div class="card">
        <h3 class="card-title">Informations Générales</h3>
        <div style="margin-top: 15px;">
            <p><strong>Numéro de contrôle :</strong> {{ $controle->numero }}</p>
            <p><strong>Parcelle :</strong> <a href="{{ route('parcelles.show', $controle->parcelle) }}" style="color: var(--primary); text-decoration: none;">#{{ $controle->parcelle->indice ?? 'N/A' }}</a></p>
            <p><strong>Culture :</strong> {{ $controle->culture->nom ?? 'N/A' }}</p>
            <p><strong>Campagne :</strong> <span class="badge-status" style="background-color: var(--primary); color: white;">{{ $controle->campagne }}</span></p>
            <p><strong>Contrôleur :</strong> {{ $controle->controleur->name ?? 'Non assigné' }} (ID : {{ $controle->controleur_id }})</p>
            <p><strong>Date de création :</strong> {{ $controle->created_at->format('d/m/Y à H:i') }}</p>
            <p><strong>Dernière modif :</strong> {{ $controle->updated_at->format('d/m/Y à H:i') }}</p>
        </div>
    </div>

    <!-- Surfaces -->
    <div class="card">
        <h3 class="card-title">Superficies Déclarées</h3>
        <div style="margin-top: 15px;">
            <p><strong>Superficie totale de la parcelle :</strong> {{ $controle->superficie_parcelle ? number_format($controle->superficie_parcelle, 2) . ' ha' : 'Non renseignée' }}</p>
            <p><strong>Superficie dédiée au bio :</strong> {{ $controle->superficie_bio ? number_format($controle->superficie_bio, 3) . ' ha' : 'Non renseignée' }}</p>
        </div>
    </div>

</div>
@endsection
