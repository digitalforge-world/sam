@extends('layouts.app')
@section('title', 'Zones')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
    <li class="breadcrumb-item current">Zones</li>
@endsection
@section('content')
<div class="page-header">
    <div><h1 class="page-title">Zones</h1><p class="page-subtitle">{{ $zones->total() }} zones</p></div>
    @can('zones.create')<a href="{{ route('areas.zones.create') }}" class="btn-primary-custom"><i data-lucide="plus" style="width:16px;height:16px"></i> Nouvelle zone</a>@endcan
</div>
<div class="card">
    @if($zones->count())
    <div style="overflow-x:auto">
        <table class="data-table">
            <thead><tr><th>#</th><th>Nom</th><th class="numeric">Producteurs</th><th class="numeric">Organisations</th><th class="numeric">Utilisateurs</th><th class="actions">Actions</th></tr></thead>
            <tbody>
                @foreach($zones as $z)
                <tr>
                    <td class="code">{{ $z->id }}</td><td style="font-weight:600">{{ $z->nom }}</td><td class="numeric">{{ $z->producteurs_count }}</td><td class="numeric">{{ $z->organisations_count }}</td><td class="numeric">{{ $z->users_count }}</td>
                    <td class="actions">
                        @can('zones.edit')<a href="{{ route('areas.zones.edit', $z) }}" class="btn-icon-sm btn-icon-warning"><i data-lucide="pencil" style="width:14px;height:14px"></i></a>@endcan
                        @can('zones.delete')<form method="POST" action="{{ route('areas.zones.destroy', $z) }}" style="display:inline" onsubmit="return confirm('Supprimer ?')">@csrf @method('DELETE')<button class="btn-icon-sm btn-icon-danger"><i data-lucide="trash-2" style="width:14px;height:14px"></i></button></form>@endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper">{{ $zones->links() }}</div>
    @else <div class="empty-state"><div class="empty-icon">🎯</div><div class="empty-title">Aucune zone</div></div> @endif
</div>
@endsection
