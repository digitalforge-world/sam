@extends('layouts.app')
@section('title', isset($canton) ? 'Modifier le canton' : 'Nouveau canton')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
    <li class="breadcrumb-item"><a href="{{ route('areas.cantons.index') }}">Cantons</a></li>
    <li class="breadcrumb-item current">{{ isset($canton) ? 'Modifier' : 'Nouveau' }}</li>
@endsection
@section('content')
<div class="page-header"><h1 class="page-title">{{ isset($canton) ? 'Modifier le canton' : 'Nouveau canton' }}</h1></div>
<div class="card" style="max-width:600px"><div class="card-body">
    <form method="POST" action="{{ isset($canton) ? route('areas.cantons.update', $canton) : route('areas.cantons.store') }}">
        @csrf @if(isset($canton)) @method('PUT') @endif
        <div class="form-row">
            <div class="form-group">
                <label class="form-label required">Région</label>
                <select name="region_id" class="form-select" required><option value="">— Sélectionner —</option>@foreach($regions as $r)<option value="{{ $r->id }}" {{ old('region_id', $canton->region_id ?? '') == $r->id ? 'selected' : '' }}>{{ $r->nom }}</option>@endforeach</select>
            </div>
            <div class="form-group">
                <label class="form-label required">Préfecture</label>
                <select name="prefecture_id" class="form-select" required><option value="">— Sélectionner —</option>@foreach($prefectures as $p)<option value="{{ $p->id }}" {{ old('prefecture_id', $canton->prefecture_id ?? '') == $p->id ? 'selected' : '' }}>{{ $p->nom }}</option>@endforeach</select>
            </div>
        </div>
        <div class="form-group"><label class="form-label required">Nom</label><input type="text" name="nom" class="form-input" value="{{ old('nom', $canton->nom ?? '') }}" required>@error('nom')<div class="form-error">{{ $message }}</div>@enderror</div>
        <div class="form-group"><label class="form-label">Zone</label><input type="text" name="zone" class="form-input" value="{{ old('zone', $canton->zone ?? '') }}"></div>
        <div style="display:flex;gap:10px;justify-content:flex-end">
            <a href="{{ route('areas.cantons.index') }}" class="btn-secondary-custom">Annuler</a>
            <button type="submit" class="btn-primary-custom"><i data-lucide="check" style="width:16px;height:16px"></i> {{ isset($canton) ? 'Mettre à jour' : 'Enregistrer' }}</button>
        </div>
    </form>
</div></div>
@endsection
