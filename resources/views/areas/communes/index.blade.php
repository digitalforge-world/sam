@extends('layouts.app')
@section('title', 'Communes')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
    <li class="breadcrumb-item current">Communes</li>
@endsection
@section('content')
<div class="page-header">
    <div><h1 class="page-title">Communes</h1><p class="page-subtitle">{{ $communes->total() }} communes</p></div>
    <a href="{{ route('areas.communes.create') }}" class="btn-primary-custom"><i data-lucide="plus" style="width:16px;height:16px"></i> Nouveau</a>
</div>
<div class="card">
    @if($communes->count())
    <div style="overflow-x:auto">
        <table class="data-table">
            <thead><tr><th>#</th><th>Nom</th><th>Région</th><th>Préfecture</th><th class="actions">Actions</th></tr></thead>
            <tbody>
                @foreach($communes as $c)
                <tr>
                    <td class="code">{{ $c->id }}</td><td style="font-weight:600">{{ $c->nom }}</td><td>{{ $c->region->nom ?? '-' }}</td><td>{{ $c->prefecture->nom ?? '-' }}</td>
                    <td class="actions">
                        <a href="{{ route('areas.communes.edit', $c) }}" class="btn-icon-sm btn-icon-warning"><i data-lucide="pencil" style="width:14px;height:14px"></i></a>
                        <form method="POST" action="{{ route('areas.communes.destroy', $c) }}" style="display:inline" onsubmit="return confirm('Supprimer ?')">@csrf @method('DELETE')<button class="btn-icon-sm btn-icon-danger"><i data-lucide="trash-2" style="width:14px;height:14px"></i></button></form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper">{{ $communes->links() }}</div>
    @else <div class="empty-state"><div class="empty-icon">📍</div><div class="empty-title">Aucune commune</div></div> @endif
</div>
@endsection
