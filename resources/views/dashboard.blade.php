@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('breadcrumb')
    <li class="breadcrumb-item current">Tableau de bord</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Bienvenue, {{ auth()->user()->name }} 👋</h1>
        <p class="page-subtitle">Voici un résumé de votre espace de travail</p>
    </div>
</div>

{{-- Stats Grid --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:20px;margin-bottom:32px;">

    {{-- Producteurs --}}
    <div class="card stat-card" style="padding:24px;">
        <div class="stat-icon" style="background:var(--color-primary-light);color:var(--color-primary)">
            <i data-lucide="user" style="width:22px;height:22px"></i>
        </div>
        <div class="stat-value">{{ number_format($stats['producteurs']) }}</div>
        <div class="stat-label">Producteurs actifs</div>
        <div class="stat-progress"><div class="bar" style="width:100%"></div></div>
    </div>

    {{-- Parcelles bio --}}
    <div class="card stat-card" style="padding:24px;">
        <div class="stat-icon" style="background:#DCFCE7;color:#16A34A">
            <i data-lucide="leaf" style="width:22px;height:22px"></i>
        </div>
        <div class="stat-value">{{ number_format($stats['parcelles_bio']) }}</div>
        <div class="stat-label">Parcelles certifiées bio</div>
        <div class="stat-progress"><div class="bar" style="width:{{ $stats['total_parcelles'] > 0 ? round($stats['parcelles_bio'] / $stats['total_parcelles'] * 100) : 0 }}%"></div></div>
    </div>

    {{-- Superficie --}}
    <div class="card stat-card" style="padding:24px;">
        <div class="stat-icon" style="background:#DBEAFE;color:#2563EB">
            <i data-lucide="land-plot" style="width:22px;height:22px"></i>
        </div>
        <div class="stat-value">{{ number_format($stats['superficie_totale'], 1) }}</div>
        <div class="stat-label">Ha de superficie bio</div>
    </div>

    {{-- En attente --}}
    <div class="card stat-card" style="padding:24px;">
        <div class="stat-icon" style="background:#FEF9C3;color:#CA8A04">
            <i data-lucide="clock" style="width:22px;height:22px"></i>
        </div>
        <div class="stat-value">{{ number_format($stats['en_attente']) }}</div>
        <div class="stat-label">Identifications en attente</div>
    </div>

    {{-- Parcelles total --}}
    <div class="card stat-card" style="padding:24px;">
        <div class="stat-icon" style="background:#FEE2E2;color:#DC2626">
            <i data-lucide="map-pin" style="width:22px;height:22px"></i>
        </div>
        <div class="stat-value">{{ number_format($stats['total_parcelles']) }}</div>
        <div class="stat-label">Total parcelles</div>
    </div>

    {{-- Zones --}}
    <div class="card stat-card" style="padding:24px;">
        <div class="stat-icon" style="background:#E0E7FF;color:#4338CA">
            <i data-lucide="target" style="width:22px;height:22px"></i>
        </div>
        <div class="stat-value">{{ number_format($stats['total_zones']) }}</div>
        <div class="stat-label">Zones actives</div>
    </div>
</div>

{{-- Quick actions --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Actions rapides</h3>
    </div>
    <div class="card-body" style="display:flex;flex-wrap:wrap;gap:12px;">
        <a href="{{ route('producteurs.create') }}" class="btn-primary-custom">
            <i data-lucide="plus" style="width:16px;height:16px"></i> Nouveau producteur
        </a>
        <a href="{{ route('parcelles.create') }}" class="btn-secondary-custom">
            <i data-lucide="plus" style="width:16px;height:16px"></i> Nouvelle parcelle
        </a>
        <a href="{{ route('identifications.create') }}" class="btn-secondary-custom">
            <i data-lucide="file-check" style="width:16px;height:16px"></i> Nouvelle identification
        </a>
        <a href="{{ route('carte.index') }}" class="btn-secondary-custom">
            <i data-lucide="map" style="width:16px;height:16px"></i> Ouvrir la carte
        </a>
    </div>
</div>
@endsection
