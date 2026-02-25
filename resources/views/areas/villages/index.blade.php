@extends('layouts.app')
@section('title', 'Villages')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
    <li class="breadcrumb-item current">Villages</li>
@endsection
@section('content')
<div class="page-header">
    <div><h1 class="page-title">Villages</h1><p class="page-subtitle">{{ $villages->total() }} villages</p></div>
    @can('villages.create')<a href="{{ route('areas.villages.create') }}" class="btn-primary-custom"><i data-lucide="plus" style="width:16px;height:16px"></i> Nouveau</a>@endcan
</div>
<div class="card">
    @if($villages->count())
    <div style="overflow-x:auto">
        <table class="data-table">
            <thead><tr><th>#</th><th>Nom</th><th>Région</th><th>Préfecture</th><th>Canton</th><th>Contrôleur</th><th class="numeric">Producteurs</th><th class="actions">Actions</th></tr></thead>
            <tbody>
                @foreach($villages as $v)
                <tr>
                    <td class="code">{{ $v->id }}</td><td style="font-weight:600">{{ $v->nom }}</td><td>{{ $v->region->nom }}</td><td>{{ $v->prefecture->nom }}</td><td>{{ $v->canton->nom }}</td>
                    <td>{{ $v->controleur?->name ?? '—' }}</td><td class="numeric">{{ $v->producteurs_count }}</td>
                    <td class="actions">
                        @can('villages.edit')<a href="{{ route('areas.villages.edit', $v) }}" class="btn-icon-sm btn-icon-warning"><i data-lucide="pencil" style="width:14px;height:14px"></i></a>@endcan
                        @can('villages.delete')<form method="POST" action="{{ route('areas.villages.destroy', $v) }}" style="display:inline" onsubmit="return confirm('Supprimer ?')">@csrf @method('DELETE')<button class="btn-icon-sm btn-icon-danger"><i data-lucide="trash-2" style="width:14px;height:14px"></i></button></form>@endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper">{{ $villages->links() }}</div>
    @else <div class="empty-state"><div class="empty-icon">🏘️</div><div class="empty-title">Aucun village</div></div> @endif
</div>
@endsection
