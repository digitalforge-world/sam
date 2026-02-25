@extends('layouts.app')
@section('title', 'Préfectures')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
    <li class="breadcrumb-item current">Préfectures</li>
@endsection
@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Préfectures</h1>
        <p class="page-subtitle">{{ $prefectures->total() }} préfectures enregistrées</p>
    </div>
    @can('prefectures.create')
    <a href="{{ route('areas.prefectures.create') }}" class="btn-primary-custom">
        <i data-lucide="plus" style="width:16px;height:16px"></i> Nouvelle
    </a>
    @endcan
</div>
<div class="card">
    @if($prefectures->count())
    <div style="overflow-x:auto">
        <table class="data-table">
            <thead><tr><th>#</th><th>Nom</th><th>Code</th><th>Région</th><th class="numeric">Cantons</th><th class="numeric">Villages</th><th class="actions">Actions</th></tr></thead>
            <tbody>
                @foreach($prefectures as $p)
                <tr>
                    <td class="code">{{ $p->id }}</td>
                    <td style="font-weight:600">{{ $p->nom }}</td>
                    <td class="code">{{ $p->code }}</td>
                    <td>{{ $p->region->nom }}</td>
                    <td class="numeric">{{ $p->cantons_count }}</td>
                    <td class="numeric">{{ $p->villages_count }}</td>
                    <td class="actions">
                        @can('prefectures.edit')
                        <a href="{{ route('areas.prefectures.edit', $p) }}" class="btn-icon-sm btn-icon-warning"><i data-lucide="pencil" style="width:14px;height:14px"></i></a>
                        @endcan
                        @can('prefectures.delete')
                        <form method="POST" action="{{ route('areas.prefectures.destroy', $p) }}" style="display:inline" onsubmit="return confirm('Supprimer ?')">@csrf @method('DELETE')<button class="btn-icon-sm btn-icon-danger"><i data-lucide="trash-2" style="width:14px;height:14px"></i></button></form>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper">{{ $prefectures->links() }}</div>
    @else
    <div class="empty-state"><div class="empty-icon">🏛️</div><div class="empty-title">Aucune préfecture</div></div>
    @endif
</div>
@endsection
