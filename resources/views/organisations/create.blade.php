@extends('layouts.app')
@section('title', isset($organisation) ? 'Modifier' : 'Nouvelle organisation')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
    <li class="breadcrumb-item"><a href="{{ route('organisations.index') }}">Organisations</a></li>
    <li class="breadcrumb-item current">{{ isset($organisation) ? 'Modifier' : 'Nouvelle' }}</li>
@endsection
@section('content')
<div class="page-header"><h1 class="page-title">{{ isset($organisation) ? 'Modifier l\'organisation' : 'Nouvelle organisation' }}</h1></div>
<div class="card" style="max-width:600px"><div class="card-body">
    <form method="POST" action="{{ isset($organisation) ? route('organisations.update', $organisation) : route('organisations.store') }}">
        @csrf @if(isset($organisation)) @method('PUT') @endif
        <div class="form-group"><label class="form-label required">Nom</label><input type="text" name="nom" class="form-input" value="{{ old('nom', $organisation->nom ?? '') }}" required>@error('nom')<div class="form-error">{{ $message }}</div>@enderror</div>
        <div class="form-row">
            <div class="form-group"><label class="form-label required">Zone</label><select name="zone_id" class="form-select" required><option value="">—</option>@foreach($zones as $z)<option value="{{ $z->id }}" {{ old('zone_id', $organisation->zone_id ?? '') == $z->id ? 'selected' : '' }}>{{ $z->nom }}</option>@endforeach</select></div>
            <div class="form-group"><label class="form-label required">Village</label><select name="village_id" class="form-select" required><option value="">—</option>@foreach($villages as $v)<option value="{{ $v->id }}" {{ old('village_id', $organisation->village_id ?? '') == $v->id ? 'selected' : '' }}>{{ $v->nom }}</option>@endforeach</select></div>
        </div>
        <div class="form-group"><label class="form-label">Contrôleur</label><select name="controleur_id" class="form-select"><option value="">— Aucun —</option>@foreach($controleurs as $u)<option value="{{ $u->id }}" {{ old('controleur_id', $organisation->controleur_id ?? '') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>@endforeach</select></div>
        <div style="display:flex;gap:10px;justify-content:flex-end">
            <a href="{{ route('organisations.index') }}" class="btn-secondary-custom">Annuler</a>
            <button type="submit" class="btn-primary-custom"><i data-lucide="check" style="width:16px;height:16px"></i> {{ isset($organisation) ? 'Mettre à jour' : 'Enregistrer' }}</button>
        </div>
    </form>
</div></div>
@endsection
