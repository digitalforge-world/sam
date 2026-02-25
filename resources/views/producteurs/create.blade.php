@extends('layouts.app')
@section('title', isset($producteur) ? 'Modifier le producteur' : 'Nouveau producteur')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
    <li class="breadcrumb-item"><a href="{{ route('producteurs.index') }}">Producteurs</a></li>
    <li class="breadcrumb-item current">{{ isset($producteur) ? 'Modifier' : 'Nouveau' }}</li>
@endsection
@section('content')
<div class="page-header"><h1 class="page-title">{{ isset($producteur) ? 'Modifier le producteur' : 'Nouveau producteur' }}</h1></div>
<div class="card" style="max-width:700px"><div class="card-body">
    <form method="POST" action="{{ isset($producteur) ? route('producteurs.update', $producteur) : route('producteurs.store') }}">
        @csrf @if(isset($producteur)) @method('PUT') @endif
        <div class="section-header"><span class="section-number">1</span><h4 class="section-title">Identité</h4></div>
        <div class="form-row">
            <div class="form-group"><label class="form-label required">Nom</label><input type="text" name="nom" class="form-input" value="{{ old('nom', $producteur->nom ?? '') }}" required>@error('nom')<div class="form-error">{{ $message }}</div>@enderror</div>
            <div class="form-group"><label class="form-label required">Prénom</label><input type="text" name="prenom" class="form-input" value="{{ old('prenom', $producteur->prenom ?? '') }}" required>@error('prenom')<div class="form-error">{{ $message }}</div>@enderror</div>
        </div>

        <hr class="section-divider">
        <div class="section-header"><span class="section-number">2</span><h4 class="section-title">Localisation</h4></div>
        <div class="form-row">
            <div class="form-group"><label class="form-label required">Zone</label><select name="zone_id" class="form-select" required><option value="">—</option>@foreach($zones as $z)<option value="{{ $z->id }}" {{ old('zone_id', $producteur->zone_id ?? '') == $z->id ? 'selected' : '' }}>{{ $z->nom }}</option>@endforeach</select>@error('zone_id')<div class="form-error">{{ $message }}</div>@enderror</div>
            <div class="form-group"><label class="form-label required">Village</label><select name="village_id" class="form-select" required><option value="">—</option>@foreach($villages as $v)<option value="{{ $v->id }}" {{ old('village_id', $producteur->village_id ?? '') == $v->id ? 'selected' : '' }}>{{ $v->nom }}</option>@endforeach</select>@error('village_id')<div class="form-error">{{ $message }}</div>@enderror</div>
        </div>

        <hr class="section-divider">
        <div class="section-header"><span class="section-number">3</span><h4 class="section-title">Affectation</h4></div>
        <div class="form-row">
            <div class="form-group"><label class="form-label">Organisation</label><select name="organisation_paysanne_id" class="form-select"><option value="">— Aucune —</option>@foreach($organisations as $o)<option value="{{ $o->id }}" {{ old('organisation_paysanne_id', $producteur->organisation_paysanne_id ?? '') == $o->id ? 'selected' : '' }}>{{ $o->nom }}</option>@endforeach</select></div>
            <div class="form-group"><label class="form-label">Contrôleur</label><select name="controleur_id" class="form-select"><option value="">— Aucun —</option>@foreach($controleurs as $u)<option value="{{ $u->id }}" {{ old('controleur_id', $producteur->controleur_id ?? '') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>@endforeach</select></div>
        </div>

        @if(isset($producteur))
        <div class="toggle-row">
            <div><div class="toggle-label">Producteur actif</div><div class="toggle-help">Désactiver le producteur le masquera des recherches</div></div>
            <label class="toggle-switch"><input type="hidden" name="est_actif" value="0"><input type="checkbox" name="est_actif" value="1" {{ old('est_actif', $producteur->est_actif) ? 'checked' : '' }}><span class="slider"></span></label>
        </div>
        @endif

        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:24px">
            <a href="{{ route('producteurs.index') }}" class="btn-secondary-custom">Annuler</a>
            <button type="submit" class="btn-primary-custom"><i data-lucide="check" style="width:16px;height:16px"></i> {{ isset($producteur) ? 'Mettre à jour' : 'Enregistrer' }}</button>
        </div>
    </form>
</div></div>
@endsection
