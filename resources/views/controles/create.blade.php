@extends('layouts.app')
@section('title', 'Nouveau contrôle')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li><li class="breadcrumb-item"><a href="{{ route('controles.index') }}">Contrôles</a></li><li class="breadcrumb-item current">Nouveau</li>@endsection
@section('content')
<div class="page-header"><h1 class="page-title">Nouveau contrôle</h1></div>
<div class="card" style="max-width:700px"><div class="card-body">
    <form method="POST" action="{{ route('controles.store') }}">@csrf
        <div class="form-group"><label class="form-label required">Numéro</label><input type="text" name="numero" class="form-input" value="{{ old('numero') }}" required></div>
        <div class="form-row">
            <div class="form-group"><label class="form-label required">Producteur</label><select name="producteur_id" class="form-select" required><option value="">—</option>@foreach($producteurs as $p)<option value="{{ $p->id }}" {{ old('producteur_id') == $p->id ? 'selected' : '' }}>{{ $p->code }} — {{ $p->nom }} {{ $p->prenom }}</option>@endforeach</select></div>
            <div class="form-group"><label class="form-label required">Parcelle</label><select name="parcelle_id" class="form-select" required><option value="">—</option>@foreach($parcelles as $pa)<option value="{{ $pa->id }}" {{ old('parcelle_id') == $pa->id ? 'selected' : '' }}>#{{ $pa->indice }} — {{ $pa->producteur->nom }}</option>@endforeach</select></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label class="form-label required">Culture</label><select name="culture_id" class="form-select" required><option value="">—</option>@foreach($cultures as $c)<option value="{{ $c->id }}" {{ old('culture_id') == $c->id ? 'selected' : '' }}>{{ $c->nom }}</option>@endforeach</select></div>
            <div class="form-group"><label class="form-label required">Campagne</label><input type="text" name="campagne" class="form-input" value="{{ old('campagne', date('Y').'/'.date('Y')+1) }}" required></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label class="form-label">Sup. parcelle</label><input type="number" step="0.01" name="superficie_parcelle" class="form-input" value="{{ old('superficie_parcelle') }}"></div>
            <div class="form-group"><label class="form-label">Sup. bio</label><input type="number" step="0.001" name="superficie_bio" class="form-input" value="{{ old('superficie_bio') }}"></div>
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end"><a href="{{ route('controles.index') }}" class="btn-secondary-custom">Annuler</a><button type="submit" class="btn-primary-custom"><i data-lucide="check" style="width:16px;height:16px"></i> Enregistrer</button></div>
    </form></div></div>
@endsection
