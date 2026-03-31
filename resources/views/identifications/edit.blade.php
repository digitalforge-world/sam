@extends('layouts.app')
@section('title', 'Modifier Identification')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
    <li class="breadcrumb-item"><a href="{{ route('identifications.index') }}">Identifications</a></li>
    <li class="breadcrumb-item current">Modifier</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Modifier l'identification #{{ $identification->numero }}</h1>
        <p class="page-subtitle">Producteur : {{ $identification->producteur->nom }} {{ $identification->producteur->prenom }}</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('identifications.index') }}" class="btn-secondary-custom">
            <i data-lucide="arrow-left" style="width:16px;height:16px"></i> Retour aux identifications
        </a>
    </div>
</div>

<div class="card">
    <div class="empty-state">
        <div class="empty-icon"><i data-lucide="edit-3" style="width:24px;height:24px"></i></div>
        <div class="empty-title">Édition en cours de développement</div>
        <p>La modification du formulaire complet de cette identification depuis le back-office sera disponible prochainement.</p>
    </div>
</div>
@endsection
