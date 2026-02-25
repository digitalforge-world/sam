@extends('layouts.app')
@section('title', isset($zone) ? 'Modifier la zone' : 'Nouvelle zone')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
    <li class="breadcrumb-item"><a href="{{ route('areas.zones.index') }}">Zones</a></li>
    <li class="breadcrumb-item current">{{ isset($zone) ? 'Modifier' : 'Nouvelle' }}</li>
@endsection
@section('content')
<div class="page-header"><h1 class="page-title">{{ isset($zone) ? 'Modifier la zone' : 'Nouvelle zone' }}</h1></div>
<div class="card" style="max-width:600px"><div class="card-body">
    <form method="POST" action="{{ isset($zone) ? route('areas.zones.update', $zone) : route('areas.zones.store') }}">
        @csrf @if(isset($zone)) @method('PUT') @endif
        <div class="form-group"><label class="form-label required">Nom</label><input type="text" name="nom" class="form-input" value="{{ old('nom', $zone->nom ?? '') }}" required>@error('nom')<div class="form-error">{{ $message }}</div>@enderror</div>
        <div class="form-group"><label class="form-label {{ isset($zone) ? '' : 'required' }}">Mot de passe{{ isset($zone) ? ' (laisser vide pour ne pas changer)' : '' }}</label><input type="password" name="mot_de_passe" class="form-input" {{ isset($zone) ? '' : 'required' }}>@error('mot_de_passe')<div class="form-error">{{ $message }}</div>@enderror</div>
        <div style="display:flex;gap:10px;justify-content:flex-end">
            <a href="{{ route('areas.zones.index') }}" class="btn-secondary-custom">Annuler</a>
            <button type="submit" class="btn-primary-custom"><i data-lucide="check" style="width:16px;height:16px"></i> {{ isset($zone) ? 'Mettre à jour' : 'Enregistrer' }}</button>
        </div>
    </form>
</div></div>
@endsection
