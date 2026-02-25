@extends('layouts.app')
@section('title', isset($village) ? 'Modifier le village' : 'Nouveau village')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
    <li class="breadcrumb-item"><a href="{{ route('areas.villages.index') }}">Villages</a></li>
    <li class="breadcrumb-item current">{{ isset($village) ? 'Modifier' : 'Nouveau' }}</li>
@endsection
@section('content')
<div class="page-header"><h1 class="page-title">{{ isset($village) ? 'Modifier le village' : 'Nouveau village' }}</h1></div>
<div class="card" style="max-width:700px"><div class="card-body">
    <form method="POST" action="{{ isset($village) ? route('areas.villages.update', $village) : route('areas.villages.store') }}">
        @csrf @if(isset($village)) @method('PUT') @endif
        <div class="form-row">
            <div class="form-group"><label class="form-label required">Région</label><select name="region_id" class="form-select" required><option value="">—</option>@foreach($regions as $r)<option value="{{ $r->id }}" {{ old('region_id', $village->region_id ?? '') == $r->id ? 'selected' : '' }}>{{ $r->nom }}</option>@endforeach</select></div>
            <div class="form-group"><label class="form-label required">Préfecture</label><select name="prefecture_id" class="form-select" required><option value="">—</option>@foreach($prefectures as $p)<option value="{{ $p->id }}" {{ old('prefecture_id', $village->prefecture_id ?? '') == $p->id ? 'selected' : '' }}>{{ $p->nom }}</option>@endforeach</select></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label class="form-label required">Canton</label><select name="canton_id" class="form-select" required><option value="">—</option>@foreach($cantons as $c)<option value="{{ $c->id }}" {{ old('canton_id', $village->canton_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->nom }}</option>@endforeach</select></div>
            <div class="form-group"><label class="form-label">Contrôleur</label><select name="controleur_id" class="form-select"><option value="">— Aucun —</option>@foreach($controleurs as $u)<option value="{{ $u->id }}" {{ old('controleur_id', $village->controleur_id ?? '') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>@endforeach</select></div>
        </div>
        <div class="form-group"><label class="form-label required">Nom</label><input type="text" name="nom" class="form-input" value="{{ old('nom', $village->nom ?? '') }}" required>@error('nom')<div class="form-error">{{ $message }}</div>@enderror</div>
        <div style="display:flex;gap:10px;justify-content:flex-end">
            <a href="{{ route('areas.villages.index') }}" class="btn-secondary-custom">Annuler</a>
            <button type="submit" class="btn-primary-custom"><i data-lucide="check" style="width:16px;height:16px"></i> {{ isset($village) ? 'Mettre à jour' : 'Enregistrer' }}</button>
        </div>
    </form>
</div></div>
@endsection
