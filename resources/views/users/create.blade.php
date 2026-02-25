@extends('layouts.app')
@section('title', isset($user) ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li><li class="breadcrumb-item"><a href="{{ route('users.index') }}">Utilisateurs</a></li><li class="breadcrumb-item current">{{ isset($user) ? 'Modifier' : 'Nouveau' }}</li>@endsection
@section('content')
<div class="page-header"><h1 class="page-title">{{ isset($user) ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur' }}</h1></div>
<div class="card" style="max-width:600px"><div class="card-body">
    <form method="POST" action="{{ isset($user) ? route('users.update', $user) : route('users.store') }}">@csrf @if(isset($user)) @method('PUT') @endif
        <div class="form-group"><label class="form-label required">Nom</label><input type="text" name="name" class="form-input" value="{{ old('name', $user->name ?? '') }}" required>@error('name')<div class="form-error">{{ $message }}</div>@enderror</div>
        <div class="form-group"><label class="form-label required">Email</label><input type="email" name="email" class="form-input" value="{{ old('email', $user->email ?? '') }}" required>@error('email')<div class="form-error">{{ $message }}</div>@enderror</div>
        <div class="form-group"><label class="form-label {{ isset($user) ? '' : 'required' }}">Mot de passe{{ isset($user) ? ' (laisser vide pour ne pas changer)' : '' }}</label><input type="password" name="password" class="form-input" {{ isset($user) ? '' : 'required' }}>@error('password')<div class="form-error">{{ $message }}</div>@enderror</div>
        <div class="form-row">
            <div class="form-group"><label class="form-label required">Rôle</label><select name="role" class="form-select" required><option value="">—</option>@foreach($roles as $r)<option value="{{ $r->name }}" {{ old('role', isset($user) ? $user->roles->first()?->name : '') === $r->name ? 'selected' : '' }}>{{ ucfirst($r->name) }}</option>@endforeach</select>@error('role')<div class="form-error">{{ $message }}</div>@enderror</div>
            <div class="form-group"><label class="form-label">Zone</label><select name="zone_id" class="form-select"><option value="">— Aucune —</option>@foreach($zones as $z)<option value="{{ $z->id }}" {{ old('zone_id', $user->zone_id ?? '') == $z->id ? 'selected' : '' }}>{{ $z->nom }}</option>@endforeach</select></div>
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end"><a href="{{ route('users.index') }}" class="btn-secondary-custom">Annuler</a><button type="submit" class="btn-primary-custom"><i data-lucide="check" style="width:16px;height:16px"></i> {{ isset($user) ? 'Mettre à jour' : 'Créer' }}</button></div>
    </form></div></div>
@endsection
