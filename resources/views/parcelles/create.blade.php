@extends('layouts.app')
@section('title', isset($parcelle) ? 'Modifier la parcelle' : 'Nouvelle parcelle')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
    <li class="breadcrumb-item"><a href="{{ route('parcelles.index') }}">Parcelles</a></li>
    <li class="breadcrumb-item current">{{ isset($parcelle) ? 'Modifier' : 'Nouvelle' }}</li>
@endsection
@section('content')
<div class="page-header"><h1 class="page-title">{{ isset($parcelle) ? 'Modifier la parcelle' : 'Nouvelle parcelle' }}</h1></div>
<div class="card" style="max-width:800px"><div class="card-body">
    <form method="POST" action="{{ isset($parcelle) ? route('parcelles.update', $parcelle) : route('parcelles.store') }}">
        @csrf @if(isset($parcelle)) @method('PUT') @endif

        <div class="section-header"><span class="section-number">1</span><h4 class="section-title">Producteur & Localisation</h4></div>
        @unless(isset($parcelle))
        <div class="form-group"><label class="form-label required">Producteur</label>
            <select name="producteur_id" class="form-select" required><option value="">—</option>@foreach($producteurs as $pr)<option value="{{ $pr->id }}" {{ old('producteur_id', request('producteur_id')) == $pr->id ? 'selected' : '' }}>{{ $pr->code }} — {{ $pr->nom }} {{ $pr->prenom }}</option>@endforeach</select>
            @error('producteur_id')<div class="form-error">{{ $message }}</div>@enderror
        </div>
        @endunless
        <div class="form-row">
            <div class="form-group"><label class="form-label">Village</label><select name="village_id" class="form-select"><option value="">—</option>@foreach($villages as $v)<option value="{{ $v->id }}" {{ old('village_id', $parcelle->village_id ?? '') == $v->id ? 'selected' : '' }}>{{ $v->nom }}</option>@endforeach</select></div>
            <div class="form-group"><label class="form-label">Culture</label><select name="culture_id" class="form-select"><option value="">—</option>@foreach($cultures as $c)<option value="{{ $c->id }}" {{ old('culture_id', $parcelle->culture_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->nom }}</option>@endforeach</select></div>
        </div>

        <hr class="section-divider">
        <div class="section-header"><span class="section-number">2</span><h4 class="section-title">Superficies & Production</h4></div>
        <div class="form-row">
            <div class="form-group"><label class="form-label">Superficie (ha)</label><input type="number" step="0.0001" name="superficie" class="form-input" value="{{ old('superficie', $parcelle->superficie ?? '') }}"></div>
            <div class="form-group"><label class="form-label">Superficie bio (ha)</label><input type="number" step="0.001" name="superficie_bio" class="form-input" value="{{ old('superficie_bio', $parcelle->superficie_bio ?? '') }}"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label class="form-label">Rendement bio</label><input type="number" step="0.001" name="rendement_bio" class="form-input" value="{{ old('rendement_bio', $parcelle->rendement_bio ?? '') }}"></div>
            <div class="form-group"><label class="form-label">Volume production</label><input type="number" name="volume_production" class="form-input" value="{{ old('volume_production', $parcelle->volume_production ?? '') }}"></div>
        </div>

        <hr class="section-divider">
        <div class="section-header"><span class="section-number">3</span><h4 class="section-title">Caractéristiques</h4></div>
        <div class="form-row">
            <div class="form-group"><label class="form-label">Niveau de pente</label><select name="niveau_pente" class="form-select"><option value="">—</option>@foreach(['WITHOUT'=>'Sans','SMALL'=>'Faible','MEDIUM'=>'Moyenne','HIGH'=>'Forte'] as $k=>$l)<option value="{{ $k }}" {{ old('niveau_pente', $parcelle->niveau_pente ?? '') === $k ? 'selected' : '' }}>{{ $l }}</option>@endforeach</select></div>
            <div class="form-group"><label class="form-label">Type de culture</label><select name="type_culture" class="form-select"><option value="">—</option>@foreach(['SINGLE'=>'Simple','ASSOCIATIVE'=>'Associative','SPACER'=>'Intercalaire','PURE'=>'Pure','STOLEN'=>'Dérobée'] as $k=>$l)<option value="{{ $k }}" {{ old('type_culture', $parcelle->type_culture ?? '') === $k ? 'selected' : '' }}>{{ $l }}</option>@endforeach</select></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label class="form-label">Type employés</label><select name="type_employes" class="form-select"><option value="">—</option>@foreach(['SEASONAL'=>'Saisonniers','PERMANENT'=>'Permanents'] as $k=>$l)<option value="{{ $k }}" {{ old('type_employes', $parcelle->type_employes ?? '') === $k ? 'selected' : '' }}>{{ $l }}</option>@endforeach</select></div>
            <div class="form-group"><label class="form-label">Approbation</label><select name="approbation_production" class="form-select"><option value="">—</option>@foreach(['BIO'=>'BIO','OK'=>'OK','DECLASSIFIED'=>'Déclassée'] as $k=>$l)<option value="{{ $k }}" {{ old('approbation_production', $parcelle->approbation_production ?? '') === $k ? 'selected' : '' }}>{{ $l }}</option>@endforeach</select></div>
        </div>

        <hr class="section-divider">
        <div class="section-header"><span class="section-number">4</span><h4 class="section-title">Environnement</h4></div>
        @foreach([['bio','Certifié BIO','Cette parcelle est certifiée biologique'],['a_cours_eau','Cours d\'eau','Présence de cours d\'eau à proximité'],['maisons_proximite','Maisons à proximité','Habitations proches de la parcelle'],['transformation_ferme','Transformation à la ferme','Transformation réalisée sur place']] as [$name,$label,$help])
        <div class="toggle-row">
            <div><div class="toggle-label">{{ $label }}</div><div class="toggle-help">{{ $help }}</div></div>
            <label class="toggle-switch"><input type="hidden" name="{{ $name }}" value="0"><input type="checkbox" name="{{ $name }}" value="1" {{ old($name, $parcelle->$name ?? false) ? 'checked' : '' }}><span class="slider"></span></label>
        </div>
        @endforeach

        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:24px">
            <a href="{{ route('parcelles.index') }}" class="btn-secondary-custom">Annuler</a>
            <button type="submit" class="btn-primary-custom"><i data-lucide="check" style="width:16px;height:16px"></i> {{ isset($parcelle) ? 'Mettre à jour' : 'Enregistrer' }}</button>
        </div>
    </form>
</div></div>
@endsection
