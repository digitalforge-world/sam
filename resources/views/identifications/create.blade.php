@extends('layouts.app')
@section('title', 'Nouvelle identification')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li><li class="breadcrumb-item"><a href="{{ route('identifications.index') }}">Identifications</a></li><li class="breadcrumb-item current">Nouvelle</li>@endsection
@section('content')
<div class="page-header"><h1 class="page-title">Nouvelle identification</h1></div>
<div class="card" style="max-width:600px"><div class="card-body">
    <form method="POST" action="{{ route('identifications.store') }}">@csrf
        <div class="form-group"><label class="form-label required">Numéro</label><input type="text" name="numero" class="form-input" value="{{ old('numero') }}" required>@error('numero')<div class="form-error">{{ $message }}</div>@enderror</div>
        <div class="form-group"><label class="form-label required">Producteur</label><select name="producteur_id" class="form-select" required><option value="">—</option>@foreach($producteurs as $p)<option value="{{ $p->id }}" {{ old('producteur_id') == $p->id ? 'selected' : '' }}>{{ $p->code }} — {{ $p->nom }} {{ $p->prenom }}</option>@endforeach</select>@error('producteur_id')<div class="form-error">{{ $message }}</div>@enderror</div>
        <div class="form-row">
            <div class="form-group"><label class="form-label">Superficie (ha)</label><input type="number" step="0.01" name="superficie" class="form-input" value="{{ old('superficie') }}"></div>
            <div class="form-group"><label class="form-label required">Campagne</label><input type="text" name="campagne" class="form-input" value="{{ old('campagne', date('Y').'/'.date('Y')+1) }}" required></div>
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end"><a href="{{ route('identifications.index') }}" class="btn-secondary-custom">Annuler</a><button type="submit" class="btn-primary-custom"><i data-lucide="check" style="width:16px;height:16px"></i> Enregistrer</button></div>
    </form></div></div>
@endsection
