@extends('layouts.app')
@section('title', 'Producteurs')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
    <li class="breadcrumb-item current">Producteurs</li>
@endsection
@section('content')
<div class="page-header">
    <div><h1 class="page-title">Producteurs</h1><p class="page-subtitle">{{ $producteurs->total() }} producteurs enregistrés</p></div>
    @can('producteurs.create')
    <a href="{{ route('producteurs.create') }}" class="btn-primary-custom"><i data-lucide="plus" style="width:16px;height:16px"></i> Nouveau producteur</a>
    @endcan
</div>

{{-- Filtres --}}
<div class="card" style="margin-bottom:20px">
    <div class="card-body" style="padding:14px 20px">
        <form method="GET" style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
            <div class="search-input-wrapper">
                <span class="search-icon"><i data-lucide="search" style="width:14px;height:14px"></i></span>
                <input type="text" name="search" class="form-input" placeholder="Rechercher..." value="{{ request('search') }}">
            </div>
            <select name="zone_id" class="form-select" style="width:180px">
                <option value="">Toutes les zones</option>
                @foreach($zones as $z)
                <option value="{{ $z->id }}" {{ request('zone_id') == $z->id ? 'selected' : '' }}>{{ $z->nom }}</option>
                @endforeach
            </select>
            <select name="est_actif" class="form-select" style="width:140px">
                <option value="">Tous</option>
                <option value="1" {{ request('est_actif') === '1' ? 'selected' : '' }}>Actifs</option>
                <option value="0" {{ request('est_actif') === '0' ? 'selected' : '' }}>Inactifs</option>
            </select>
            <button type="submit" class="btn-primary-custom">Filtrer</button>
            <a href="{{ route('producteurs.index') }}" class="btn-secondary-custom">Réinitialiser</a>
        </form>
    </div>
</div>

<div class="card">
    @if($producteurs->count())
    <div style="overflow-x:auto">
        <table class="data-table">
            <thead><tr><th>Code</th><th>Nom</th><th>Zone</th><th>Village</th><th>Organisation</th><th class="numeric">Parcelles</th><th>Statut</th><th class="actions">Actions</th></tr></thead>
            <tbody>
                @foreach($producteurs as $p)
                <tr>
                    <td class="code">{{ $p->code }}</td>
                    <td style="font-weight:600">{{ $p->nom }} {{ $p->prenom }}</td>
                    <td>{{ $p->zone->nom ?? '—' }}</td>
                    <td>{{ $p->village->nom ?? '—' }}</td>
                    <td>{{ $p->organisation->nom ?? '—' }}</td>
                    <td class="numeric">{{ $p->parcelles_count }}</td>
                    <td>
                        @if($p->est_actif)
                            <span class="badge-status badge-bio">● Actif</span>
                        @else
                            <span class="badge-status badge-muted">● Inactif</span>
                        @endif
                    </td>
                    <td class="actions">
                        <a href="{{ route('producteurs.show', $p) }}" class="btn-icon-sm btn-icon-primary"><i data-lucide="eye" style="width:14px;height:14px"></i></a>
                        @can('producteurs.edit')<a href="{{ route('producteurs.edit', $p) }}" class="btn-icon-sm btn-icon-warning"><i data-lucide="pencil" style="width:14px;height:14px"></i></a>@endcan
                        @can('producteurs.delete')
                        <form method="POST" action="{{ route('producteurs.destroy', $p) }}" style="display:inline" onsubmit="return confirm('Supprimer ce producteur ?')">@csrf @method('DELETE')<button class="btn-icon-sm btn-icon-danger"><i data-lucide="trash-2" style="width:14px;height:14px"></i></button></form>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper">{{ $producteurs->links() }}</div>
    @else
    <div class="empty-state"><div class="empty-icon">👤</div><div class="empty-title">Aucun producteur trouvé</div><div class="empty-text">Ajoutez un nouveau producteur pour commencer.</div>
    @can('producteurs.create')<a href="{{ route('producteurs.create') }}" class="btn-primary-custom"><i data-lucide="plus" style="width:16px;height:16px"></i> Ajouter</a>@endcan</div>
    @endif
</div>
@endsection
