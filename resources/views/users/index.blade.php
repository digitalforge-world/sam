@extends('layouts.app')
@section('title', 'Utilisateurs')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li><li class="breadcrumb-item current">Utilisateurs</li>@endsection
@section('content')
<div class="page-header">
    <div><h1 class="page-title">Utilisateurs</h1><p class="page-subtitle">{{ $users->total() }} utilisateurs</p></div>
    <a href="{{ route('users.create') }}" class="btn-primary-custom"><i data-lucide="plus" style="width:16px;height:16px"></i> Nouveau</a>
</div>
<div class="card">
    @if($users->count())
    <div style="overflow-x:auto">
        <table class="data-table">
            <thead><tr><th>#</th><th>Nom</th><th>Email</th><th>Rôle</th><th>Zone</th><th>Date</th><th class="actions">Actions</th></tr></thead>
            <tbody>
                @foreach($users as $u)
                <tr>
                    <td class="code">{{ $u->id }}</td>
                    <td style="font-weight:600">{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td>
                        @foreach($u->roles as $r)
                        <span class="badge-status badge-bio" style="text-transform:capitalize">{{ $r->name }}</span>
                        @endforeach
                    </td>
                    <td>{{ $u->zone?->nom ?? '—' }}</td>
                    <td>{{ $u->created_at->format('d/m/Y') }}</td>
                    <td class="actions">
                        <a href="{{ route('users.edit', $u) }}" class="btn-icon-sm btn-icon-warning"><i data-lucide="pencil" style="width:14px;height:14px"></i></a>
                        @if($u->id !== auth()->id())
                        <form method="POST" action="{{ route('users.destroy', $u) }}" style="display:inline" onsubmit="return confirm('Supprimer cet utilisateur ?')">@csrf @method('DELETE')<button class="btn-icon-sm btn-icon-danger"><i data-lucide="trash-2" style="width:14px;height:14px"></i></button></form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper">{{ $users->links() }}</div>
    @else <div class="empty-state"><div class="empty-icon">👥</div><div class="empty-title">Aucun utilisateur</div></div> @endif
</div>
@endsection
