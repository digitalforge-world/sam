@extends('layouts.app')
@section('title', $producteur->nom . ' ' . $producteur->prenom)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
    <li class="breadcrumb-item"><a href="{{ route('producteurs.index') }}">Producteurs</a></li>
    <li class="breadcrumb-item current">{{ $producteur->code }}</li>
@endsection
@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">{{ $producteur->nom }} {{ $producteur->prenom }}</h1>
        <p class="page-subtitle">
            <span class="code" style="font-family:var(--font-mono);font-size:13px">{{ $producteur->code }}</span> ·
            @if($producteur->est_actif)<span class="badge-status badge-bio">Actif</span>@else<span class="badge-status badge-muted">Inactif</span>@endif
        </p>
    </div>
    <div style="display:flex;gap:8px">
        @can('producteurs.edit')<a href="{{ route('producteurs.edit', $producteur) }}" class="btn-secondary-custom"><i data-lucide="pencil" style="width:14px;height:14px"></i> Modifier</a>@endcan
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
    {{-- Info --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title">Informations</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Zone</span><span class="detail-value">{{ $producteur->zone->nom ?? '—' }}</span></div>
            <div class="detail-row"><span class="detail-label">Village</span><span class="detail-value">{{ $producteur->village->nom ?? '—' }}</span></div>
            <div class="detail-row"><span class="detail-label">Organisation</span><span class="detail-value">{{ $producteur->organisation->nom ?? '—' }}</span></div>
            <div class="detail-row"><span class="detail-label">Contrôleur</span><span class="detail-value">{{ $producteur->controleur->name ?? '—' }}</span></div>
            <div class="detail-row"><span class="detail-label">Inscrit le</span><span class="detail-value">{{ $producteur->created_at->format('d/m/Y') }}</span></div>
        </div>
    </div>

    {{-- Parcelles --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Parcelles ({{ $producteur->parcelles->count() }})</h3>
            @can('parcelles.create')<a href="{{ route('parcelles.create', ['producteur_id' => $producteur->id]) }}" class="btn-primary-custom" style="font-size:12px;padding:6px 12px"><i data-lucide="plus" style="width:14px;height:14px"></i></a>@endcan
        </div>
        <div class="card-body" style="padding:0">
            @if($producteur->parcelles->count())
            <table class="data-table">
                <thead><tr><th>Indice</th><th>Culture</th><th class="numeric">Superficie</th><th>Statut</th><th class="actions"></th></tr></thead>
                <tbody>
                    @foreach($producteur->parcelles as $parc)
                    <tr>
                        <td class="code">#{{ $parc->indice }}</td>
                        <td>{{ $parc->culture->nom ?? '—' }}</td>
                        <td class="numeric">{{ $parc->superficie ? number_format($parc->superficie, 2) . ' ha' : '—' }}</td>
                        <td>
                            @if($parc->bio)<span class="badge-status badge-bio">BIO</span>
                            @elseif($parc->approbation_production === 'OK')<span class="badge-status badge-ok">OK</span>
                            @elseif($parc->approbation_production === 'DECLASSIFIED')<span class="badge-status badge-error">Déclassée</span>
                            @else<span class="badge-status badge-muted">—</span>@endif
                        </td>
                        <td class="actions"><a href="{{ route('parcelles.show', $parc) }}" class="btn-icon-sm btn-icon-primary"><i data-lucide="eye" style="width:14px;height:14px"></i></a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="empty-state" style="padding:30px"><div class="empty-icon">🌾</div><div class="empty-text">Aucune parcelle</div></div>
            @endif
        </div>
    </div>
</div>

{{-- Identifications --}}
<div class="card" style="margin-top:20px">
    <div class="card-header"><h3 class="card-title">Identifications ({{ $producteur->identifications->count() }})</h3></div>
    <div class="card-body" style="padding:0">
        @if($producteur->identifications->count())
        <table class="data-table">
            <thead><tr><th>Numéro</th><th>Campagne</th><th>Superficie</th><th>Statut</th><th>Date</th></tr></thead>
            <tbody>
                @foreach($producteur->identifications as $ident)
                <tr>
                    <td class="code">{{ $ident->numero }}</td><td>{{ $ident->campagne }}</td><td class="numeric">{{ $ident->superficie ? number_format($ident->superficie, 2) . ' ha' : '—' }}</td>
                    <td>
                        @if($ident->statut === 'APPROUVE')<span class="badge-status badge-bio">Approuvée</span>
                        @elseif($ident->statut === 'REJETE')<span class="badge-status badge-error">Rejetée</span>
                        @else<span class="badge-status badge-ok">En attente</span>@endif
                    </td>
                    <td>{{ $ident->created_at->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state" style="padding:30px"><div class="empty-text">Aucune identification</div></div>
        @endif
    </div>
</div>
@endsection
