@extends('layouts.app')
@section('title', isset($commune) ? 'Modifier la commune' : 'Nouvelle commune')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
    <li class="breadcrumb-item"><a href="{{ route('areas.communes.index') }}">Communes</a></li>
    <li class="breadcrumb-item current">{{ isset($commune) ? 'Modifier' : 'Nouvelle' }}</li>
@endsection
@section('content')
<div class="page-header"><h1 class="page-title">{{ isset($commune) ? 'Modifier la commune' : 'Nouvelle commune' }}</h1></div>
<div class="card" style="max-width:600px"><div class="card-body">
    <form method="POST" action="{{ isset($commune) ? route('areas.communes.update', $commune) : route('areas.communes.store') }}">
        @csrf @if(isset($commune)) @method('PUT') @endif
        <div class="form-row">
            <div class="form-group">
                <label class="form-label required">Région</label>
                <select name="region_id" class="form-select" id="region-select" required><option value="">— Sélectionner —</option>@foreach($regions as $r)<option value="{{ $r->id }}" {{ old('region_id', $commune->region_id ?? '') == $r->id ? 'selected' : '' }}>{{ $r->nom }}</option>@endforeach</select>
            </div>
            <div class="form-group">
                <label class="form-label required">Préfecture</label>
                <select name="prefecture_id" class="form-select" id="prefecture-select" required>
                    <option value="">— Sélectionner —</option>
                    @if(isset($prefectures))
                        @foreach($prefectures as $p)
                            <option value="{{ $p->id }}" {{ old('prefecture_id', $commune->prefecture_id ?? '') == $p->id ? 'selected' : '' }}>{{ $p->nom }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
        <div class="form-group"><label class="form-label required">Nom</label><input type="text" name="nom" class="form-input" value="{{ old('nom', $commune->nom ?? '') }}" required>@error('nom')<div class="form-error">{{ $message }}</div>@enderror</div>
        
        <div style="display:flex;gap:10px;justify-content:flex-end">
            <a href="{{ route('areas.communes.index') }}" class="btn-secondary-custom">Annuler</a>
            <button type="submit" class="btn-primary-custom"><i data-lucide="check" style="width:16px;height:16px"></i> {{ isset($commune) ? 'Mettre à jour' : 'Enregistrer' }}</button>
        </div>
    </form>
</div></div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const regionSelect = document.getElementById('region-select');
    const prefectureSelect = document.getElementById('prefecture-select');

    regionSelect.addEventListener('change', async (e) => {
        const regionId = e.target.value;
        prefectureSelect.innerHTML = '<option value="">— Sélectionner —</option>';
        prefectureSelect.disabled = true;

        if (regionId) {
            try {
                const response = await fetch(`/api/prefectures?region_id=${regionId}`);
                const data = await response.json();
                
                data.forEach(p => {
                    const option = document.createElement('option');
                    option.value = p.id;
                    option.textContent = p.nom;
                    prefectureSelect.appendChild(option);
                });
                prefectureSelect.disabled = false;
            } catch (error) {
                console.error('Erreur lors du chargement des préfectures:', error);
            }
        }
    });

    // Optionnel : Déclencher le chargement si une région est déjà sélectionnée (ex: après un échec de validation)
    if (regionSelect.value && prefectureSelect.options.length <= 1) {
        regionSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
@endsection
