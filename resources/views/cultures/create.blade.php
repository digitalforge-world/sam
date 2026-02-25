@extends('layouts.app')
@section('title', isset($culture) ? 'Modifier' : 'Nouvelle culture')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li><li class="breadcrumb-item"><a href="{{ route('cultures.index') }}">Cultures</a></li><li class="breadcrumb-item current">{{ isset($culture) ? 'Modifier' : 'Nouvelle' }}</li>@endsection
@section('content')
<div class="page-header"><h1 class="page-title">{{ isset($culture) ? 'Modifier la culture' : 'Nouvelle culture' }}</h1></div>
<div class="card" style="max-width:500px"><div class="card-body">
    <form method="POST" action="{{ isset($culture) ? route('cultures.update', $culture) : route('cultures.store') }}">@csrf @if(isset($culture)) @method('PUT') @endif
        <div class="form-group"><label class="form-label required">Nom</label><input type="text" name="nom" class="form-input" value="{{ old('nom', $culture->nom ?? '') }}" required>@error('nom')<div class="form-error">{{ $message }}</div>@enderror</div>
        <div style="display:flex;gap:10px;justify-content:flex-end"><a href="{{ route('cultures.index') }}" class="btn-secondary-custom">Annuler</a><button type="submit" class="btn-primary-custom"><i data-lucide="check" style="width:16px;height:16px"></i> {{ isset($culture) ? 'Mettre à jour' : 'Enregistrer' }}</button></div>
    </form></div></div>
@endsection
