@extends('layouts.app')
@section('title', 'Paramètres')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li><li class="breadcrumb-item current">Paramètres</li>@endsection
@section('content')
<div class="page-header">
    <div><h1 class="page-title">Paramètres système</h1><p class="page-subtitle">Configuration globale de l'application</p></div>
</div>
<div class="card" style="max-width:600px"><div class="card-body">
    <form method="POST" action="{{ route('parametres.update') }}">
        @csrf @method('PUT')
        @foreach($parametres as $p)
        <div class="form-group">
            <label class="form-label" style="text-transform:capitalize">{{ str_replace('_', ' ', $p->nom) }}</label>
            <input type="text" name="parametres[{{ $p->id }}]" class="form-input" value="{{ old('parametres.'.$p->id, $p->valeur) }}">
        </div>
        @endforeach
        <div style="display:flex;gap:10px;justify-content:flex-end">
            <button type="submit" class="btn-primary-custom"><i data-lucide="check" style="width:16px;height:16px"></i> Enregistrer</button>
        </div>
    </form>
</div></div>
@endsection
