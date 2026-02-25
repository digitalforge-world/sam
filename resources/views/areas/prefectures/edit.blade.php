@extends('layouts.app')
@section('title', 'Modifier la préfecture')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
    <li class="breadcrumb-item"><a href="{{ route('areas.prefectures.index') }}">Préfectures</a></li>
    <li class="breadcrumb-item current">Modifier</li>
@endsection
@section('content')
<div class="page-header"><h1 class="page-title">Modifier la préfecture</h1></div>
<div class="card" style="max-width:600px">
    <div class="card-body">
        <form method="POST" action="{{ route('areas.prefectures.update', $prefecture) }}">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label required">Région</label>
                <select name="region_id" class="form-select" required>
                    <option value="">— Sélectionner —</option>
                    @foreach($regions as $r)
                    <option value="{{ $r->id }}" {{ old('region_id', $prefecture->region_id) == $r->id ? 'selected' : '' }}>{{ $r->nom }}</option>
                    @endforeach
                </select>
                @error('region_id') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label required">Nom</label>
                <input type="text" name="nom" class="form-input" value="{{ old('nom', $prefecture->nom) }}" required>
                @error('nom') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end">
                <a href="{{ route('areas.prefectures.index') }}" class="btn-secondary-custom">Annuler</a>
                <button type="submit" class="btn-primary-custom"><i data-lucide="check" style="width:16px;height:16px"></i> Mettre à jour</button>
            </div>
        </form>
    </div>
</div>
@endsection
