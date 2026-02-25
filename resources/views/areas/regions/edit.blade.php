@extends('layouts.app')

@section('title', 'Modifier la région')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
    <li class="breadcrumb-item"><a href="{{ route('areas.regions.index') }}">Régions</a></li>
    <li class="breadcrumb-item current">Modifier</li>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title">Modifier la région</h1>
</div>

<div class="card" style="max-width:600px">
    <div class="card-body">
        <form method="POST" action="{{ route('areas.regions.update', $region) }}">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label required">Nom de la région</label>
                <input type="text" name="nom" class="form-input" value="{{ old('nom', $region->nom) }}" required autofocus>
                @error('nom') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end">
                <a href="{{ route('areas.regions.index') }}" class="btn-secondary-custom">Annuler</a>
                <button type="submit" class="btn-primary-custom">
                    <i data-lucide="check" style="width:16px;height:16px"></i> Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
