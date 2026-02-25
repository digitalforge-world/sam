@extends('layouts.app')
@section('title', 'Cantons')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
    <li class="breadcrumb-item current">Cantons</li>
@endsection
@section('content')
<div class="page-header">
    <div><h1 class="page-title">Cantons</h1><p class="page-subtitle">{{ $cantons->total() }} cantons</p></div>
    @can('cantons.create')<a href="{{ route('areas.cantons.create') }}" class="btn-primary-custom"><i data-lucide="plus" style="width:16px;height:16px"></i> Nouveau</a>@endcan
</div>
<div class="card">
    @if($cantons->count())
    <div style="overflow-x:auto">
        <table class="data-table">
            <thead><tr><th>#</th><th>Nom</th><th>Région</th><th>Préfecture</th><th class="numeric">Villages</th><th class="actions">Actions</th></tr></thead>
            <tbody>
                @foreach($cantons as $c)
                <tr>
                    <td class="code">{{ $c->id }}</td><td style="font-weight:600">{{ $c->nom }}</td><td>{{ $c->region->nom }}</td><td>{{ $c->prefecture->nom }}</td><td class="numeric">{{ $c->villages_count }}</td>
                    <td class="actions">
                        @can('cantons.edit')<a href="{{ route('areas.cantons.edit', $c) }}" class="btn-icon-sm btn-icon-warning"><i data-lucide="pencil" style="width:14px;height:14px"></i></a>@endcan
                        @can('cantons.delete')<form method="POST" action="{{ route('areas.cantons.destroy', $c) }}" style="display:inline" onsubmit="return confirm('Supprimer ?')">@csrf @method('DELETE')<button class="btn-icon-sm btn-icon-danger"><i data-lucide="trash-2" style="width:14px;height:14px"></i></button></form>@endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper">{{ $cantons->links() }}</div>
    @else <div class="empty-state"><div class="empty-icon">📍</div><div class="empty-title">Aucun canton</div></div> @endif
</div>
@endsection
