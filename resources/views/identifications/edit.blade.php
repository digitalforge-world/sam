@extends('layouts.app')
@section('title', 'Modifier Identification')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
    <li class="breadcrumb-item"><a href="{{ route('identifications.index') }}">Identifications</a></li>
    <li class="breadcrumb-item current">Modifier</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Modifier l'identification #{{ $identification->numero }}</h1>
        <p class="page-subtitle">Producteur : {{ $identification->producteur->nom }} {{ $identification->producteur->prenom }}</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('identifications.index') }}" class="btn-secondary-custom">
            <i data-lucide="arrow-left" style="width:16px;height:16px"></i> Retour
        </a>
    </div>
</div>

<form method="POST" action="{{ route('identifications.update', $identification) }}">
    @csrf
    @method('PUT')

    <div class="grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem;">
        
        <!-- Informations Générales -->
        <div class="card">
            <h3 class="card-title">Informations Administratives</h3>
            
            <div class="form-group" style="margin-top: 15px;">
                <label class="form-label">Statut</label>
                <select name="statut" class="form-control" required>
                    <option value="EN_ATTENTE" {{ $identification->statut === 'EN_ATTENTE' ? 'selected' : '' }}>En attente</option>
                    <option value="APPROUVE" {{ $identification->statut === 'APPROUVE' ? 'selected' : '' }}>Approuvée</option>
                    <option value="REJETE" {{ $identification->statut === 'REJETE' ? 'selected' : '' }}>Rejetée</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Campagne</label>
                <input type="text" name="campagne" class="form-control" value="{{ old('campagne', $identification->campagne) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Village</label>
                <select name="village_id" class="form-control">
                    <option value="">-- Sélectionnez --</option>
                    @foreach($villages as $v)
                        <option value="{{ $v->id }}" {{ $identification->village_id == $v->id ? 'selected' : '' }}>{{ $v->nom }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Organisation Paysanne</label>
                <select name="organisation_id" class="form-control">
                    <option value="">-- Sélectionnez --</option>
                    @foreach($organisations as $org)
                        <option value="{{ $org->id }}" {{ $identification->organisation_id == $org->id ? 'selected' : '' }}>{{ $org->nom }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Culture à certifier</label>
                <select name="culture_id" class="form-control">
                    <option value="">-- Sélectionnez --</option>
                    @foreach($cultures as $c)
                        <option value="{{ $c->id }}" {{ $identification->culture_id == $c->id ? 'selected' : '' }}>{{ $c->nom }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Nom de la parcelle</label>
                <input type="text" name="nom_parcelle" class="form-control" value="{{ old('nom_parcelle', $identification->nom_parcelle) }}">
            </div>

            <div class="form-group">
                <label class="form-label">Superficie (ha)</label>
                <input type="number" step="0.0001" name="superficie" class="form-control" value="{{ old('superficie', $identification->superficie) }}">
            </div>
            
            <div class="form-group">
                <label class="form-label">Commentaire</label>
                <textarea name="commentaire" class="form-control" rows="3">{{ old('commentaire', $identification->commentaire) }}</textarea>
            </div>
        </div>

        <!-- Questionnaire / Historique -->
        <div class="card">
            <h3 class="card-title">Questionnaire Agricole (Oui/Non)</h3>
            <div style="margin-top: 15px;">
                @php
                    $boolFields = [
                        'preparation_sol' => 'Préparation du sol',
                        'semences' => 'Semences biologiques/adaptées',
                        'fertilisation' => 'Fertilisation organique',
                        'gestion_adventices' => 'Gestion des adventices respectée',
                        'gestion_ravageurs' => 'Gestion correcte des ravageurs',
                        'recolte' => 'Récolte effectuée',
                        'stockage' => 'Dispositif de stockage conforme',
                        'isolement_parcelles' => 'Isolement des parcelles assuré',
                        'rotation_cultures' => 'Rotation des cultures appliquée',
                        'diversite_biologique' => 'Diversité biologique encouragée',
                        'gestion_dechets' => 'Gestion des déchets présente',
                        'emballage_non_conforme' => 'Présence d\'emballages non conformes ?',
                        'production_parallele' => 'Production parallèle existante ?',
                        'participation_formations' => 'A participé aux formations ?'
                    ];
                @endphp

                <table class="data-table">
                    <tbody>
                        @foreach($boolFields as $field => $label)
                        <tr>
                            <td><strong>{{ $label }}</strong></td>
                            <td style="text-align: right;">
                                <select name="{{ $field }}" class="form-control" style="width: 100px; display: inline-block;">
                                    <option value="1" {{ old($field, $identification->$field) ? 'selected' : '' }}>Oui</option>
                                    <option value="0" {{ !old($field, $identification->$field) ? 'selected' : '' }}>Non</option>
                                </select>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <div class="form-actions" style="margin-top: 20px; text-align: right;">
        <button type="submit" class="btn-primary-custom">
            <i data-lucide="save" style="width:16px;height:16px"></i> Enregistrer les modifications
        </button>
    </div>
</form>
@endsection
