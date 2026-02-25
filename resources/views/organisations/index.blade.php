@extends('layouts.app')
@section('title', 'Organisations')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
    <li class="breadcrumb-item current">Organisations</li>
@endsection
@section('content')
<div class="page-header">
    <div><h1 class="page-title">Organisations paysannes</h1><p class="page-subtitle">{{ $organisations->total() }} organisations</p></div>
    @can('organisations.create')<a href="{{ route('organisations.create') }}" class="btn-primary-custom"><i data-lucide="plus" style="width:16px;height:16px"></i> Nouvelle</a>@endcan
</div>
<div class="card">
    @if($organisations->count())
    <div style="overflow-x:auto">
        <table class="data-table">
            <thead><tr><th>#</th><th>Nom</th><th>Zone</th><th>Village</th><th>Contrôleur</th><th class="numeric">Producteurs</th><th class="actions">Actions</th></tr></thead>
            <tbody>
                @foreach($organisations as $o)
                <tr>
                    <td class="code">{{ $o->id }}</td><td style="font-weight:600">{{ $o->nom }}</td><td>{{ $o->zone->nom }}</td><td>{{ $o->village->nom }}</td><td>{{ $o->controleur?->name ?? '—' }}</td><td class="numeric">{{ $o->producteurs_count }}</td>
                    <td class="actions">
                        @can('organisations.edit')<a href="{{ route('organisations.edit', $o) }}" class="btn-icon-sm btn-icon-warning"><i data-lucide="pencil" style="width:14px;height:14px"></i></a>@endcan
                        @can('organisations.delete')<form method="POST" action="{{ route('organisations.destroy', $o) }}" style="display:inline" onsubmit="return confirm('Supprimer ?')">@csrf @method('DELETE')<button class="btn-icon-sm btn-icon-danger"><i data-lucide="trash-2" style="width:14px;height:14px"></i></button></form>@endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper">{{ $organisations->links() }}</div>
    @else <div class="empty-state"><div class="empty-icon">👥</div><div class="empty-title">Aucune organisation</div></div> @endif
</div>
@endsection
