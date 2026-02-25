@extends('layouts.app')

@section('title', 'Régions')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
    <li class="breadcrumb-item current">Régions</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Régions</h1>
        <p class="page-subtitle">{{ $regions->total() }} régions enregistrées</p>
    </div>
    @can('regions.create')
    <a href="{{ route('areas.regions.create') }}" class="btn-primary-custom">
        <i data-lucide="plus" style="width:16px;height:16px"></i> Nouvelle région
    </a>
    @endcan
</div>

<div class="card">
    @if($regions->count())
    <div style="overflow-x:auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nom</th>
                    <th class="numeric">Préfectures</th>
                    <th class="numeric">Cantons</th>
                    <th class="numeric">Villages</th>
                    <th class="actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($regions as $region)
                <tr>
                    <td class="code">{{ $region->id }}</td>
                    <td style="font-weight:600">{{ $region->nom }}</td>
                    <td class="numeric">{{ $region->prefectures_count }}</td>
                    <td class="numeric">{{ $region->cantons_count }}</td>
                    <td class="numeric">{{ $region->villages_count }}</td>
                    <td class="actions">
                        @can('regions.edit')
                        <a href="{{ route('areas.regions.edit', $region) }}" class="btn-icon-sm btn-icon-warning" title="Modifier">
                            <i data-lucide="pencil" style="width:14px;height:14px"></i>
                        </a>
                        @endcan
                        @can('regions.delete')
                        <form method="POST" action="{{ route('areas.regions.destroy', $region) }}" style="display:inline" onsubmit="return confirm('Supprimer cette région ?')">
                            @csrf @method('DELETE')
                            <button class="btn-icon-sm btn-icon-danger" title="Supprimer">
                                <i data-lucide="trash-2" style="width:14px;height:14px"></i>
                            </button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper">
        {{ $regions->links() }}
    </div>
    @else
    <div class="empty-state">
        <div class="empty-icon">🌍</div>
        <div class="empty-title">Aucune région</div>
        <div class="empty-text">Commencez par ajouter une région géographique.</div>
        @can('regions.create')
        <a href="{{ route('areas.regions.create') }}" class="btn-primary-custom">
            <i data-lucide="plus" style="width:16px;height:16px"></i> Ajouter une région
        </a>
        @endcan
    </div>
    @endif
</div>
@endsection
