@extends('layouts.app')
@section('title', 'Cultures')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li><li class="breadcrumb-item current">Cultures</li>@endsection
@section('content')
<div class="page-header">
    <div><h1 class="page-title">Cultures</h1><p class="page-subtitle">{{ $cultures->total() }} cultures</p></div>
    <a href="{{ route('cultures.create') }}" class="btn-primary-custom"><i data-lucide="plus" style="width:16px;height:16px"></i> Nouvelle</a>
</div>
<div class="card">
    @if($cultures->count())
    <table class="data-table"><thead><tr><th>#</th><th>Nom</th><th class="actions">Actions</th></tr></thead><tbody>
        @foreach($cultures as $c)<tr><td class="code">{{ $c->id }}</td><td style="font-weight:600">{{ $c->nom }}</td><td class="actions"><a href="{{ route('cultures.edit', $c) }}" class="btn-icon-sm btn-icon-warning"><i data-lucide="pencil" style="width:14px;height:14px"></i></a><form method="POST" action="{{ route('cultures.destroy', $c) }}" style="display:inline" onsubmit="return confirm('Supprimer ?')">@csrf @method('DELETE')<button class="btn-icon-sm btn-icon-danger"><i data-lucide="trash-2" style="width:14px;height:14px"></i></button></form></td></tr>@endforeach
    </tbody></table>
    <div class="pagination-wrapper">{{ $cultures->links() }}</div>
    @else <div class="empty-state"><div class="empty-icon">🌱</div><div class="empty-title">Aucune culture</div></div> @endif
</div>
@endsection
