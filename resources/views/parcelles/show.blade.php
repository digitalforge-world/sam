@extends('layouts.app')
@section('title', 'Parcelle #' . $parcelle->indice)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
    <li class="breadcrumb-item"><a href="{{ route('parcelles.index') }}">Parcelles</a></li>
    <li class="breadcrumb-item current">#{{ $parcelle->indice }}</li>
@endsection
@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Parcelle #{{ $parcelle->indice }}</h1>
        <p class="page-subtitle">{{ $parcelle->producteur->nom }} {{ $parcelle->producteur->prenom }} · {{ $parcelle->producteur->code }}</p>
    </div>
    <div style="display:flex;gap:8px">
        @can('parcelles.edit')<a href="{{ route('parcelles.edit', $parcelle) }}" class="btn-secondary-custom"><i data-lucide="pencil" style="width:14px;height:14px"></i> Modifier</a>@endcan
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
    <div class="card">
        <div class="card-header"><h3 class="card-title">Détails</h3></div>
        <div class="card-body">
            <div class="detail-row"><span class="detail-label">Culture</span><span class="detail-value">{{ $parcelle->culture->nom ?? '—' }}</span></div>
            <div class="detail-row"><span class="detail-label">Village</span><span class="detail-value">{{ $parcelle->village->nom ?? '—' }}</span></div>
            <div class="detail-row"><span class="detail-label">Superficie</span><span class="detail-value">{{ $parcelle->superficie ? number_format($parcelle->superficie, 2) . ' ha' : '—' }}</span></div>
            <div class="detail-row"><span class="detail-label">Superficie BIO</span><span class="detail-value">{{ $parcelle->superficie_bio ? number_format($parcelle->superficie_bio, 3) . ' ha' : '—' }}</span></div>
            <div class="detail-row"><span class="detail-label">Approbation</span><span class="detail-value">
                @if($parcelle->approbation_production === 'BIO')<span class="badge-status badge-bio">BIO</span>
                @elseif($parcelle->approbation_production === 'OK')<span class="badge-status badge-ok">OK</span>
                @elseif($parcelle->approbation_production === 'DECLASSIFIED')<span class="badge-status badge-error">Déclassée</span>
                @else — @endif
            </span></div>
            <div class="detail-row"><span class="detail-label">BIO</span><span class="detail-value">{{ $parcelle->bio ? '✅ Oui' : '❌ Non' }}</span></div>
            <div class="detail-row"><span class="detail-label">Cours d'eau</span><span class="detail-value">{{ $parcelle->a_cours_eau ? '✅' : '❌' }}</span></div>
            <div class="detail-row"><span class="detail-label">Maisons à prox.</span><span class="detail-value">{{ $parcelle->maisons_proximite ? '✅' : '❌' }}</span></div>
        </div>
    </div>

    {{-- Arbres --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title">Arbres</h3></div>
        <div class="card-body" style="padding:0">
            @if($parcelle->arbres->count())
            <table class="data-table">
                <thead><tr><th>Culture</th><th class="numeric">Nombre</th><th class="actions"></th></tr></thead>
                <tbody>
                    @foreach($parcelle->arbres as $a)
                    <tr>
                        <td>{{ $a->culture->nom }}</td><td class="numeric">{{ number_format($a->nombre) }}</td>
                        <td class="actions"><form method="POST" action="{{ route('arbres.destroy', $a) }}" style="display:inline" onsubmit="return confirm('Supprimer ?')">@csrf @method('DELETE')<button class="btn-icon-sm btn-icon-danger"><i data-lucide="trash-2" style="width:14px;height:14px"></i></button></form></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else <div class="empty-state" style="padding:20px"><div class="empty-text">Aucun arbre enregistré</div></div> @endif
        </div>
        @can('parcelles.edit')
        <div class="card-footer">
            <form method="POST" action="{{ route('parcelles.arbres.store', $parcelle) }}" style="display:flex;gap:8px;align-items:end">
                @csrf
                <div style="flex:1"><label class="form-label" style="font-size:12px">Culture</label><select name="culture_id" class="form-select" required style="font-size:12px;padding:6px 10px"><option value="">—</option>@foreach(App\Models\Culture::orderBy('nom')->get() as $c)<option value="{{ $c->id }}">{{ $c->nom }}</option>@endforeach</select></div>
                <div style="width:80px"><label class="form-label" style="font-size:12px">Nombre</label><input type="number" name="nombre" class="form-input" style="font-size:12px;padding:6px 10px" required min="1"></div>
                <button type="submit" class="btn-primary-custom" style="font-size:12px;padding:6px 10px"><i data-lucide="plus" style="width:14px;height:14px"></i></button>
            </form>
        </div>
        @endcan
    </div>
</div>
@endsection
