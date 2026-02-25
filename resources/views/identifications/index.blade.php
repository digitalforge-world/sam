@extends('layouts.app')
@section('title', 'Identifications')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li><li class="breadcrumb-item current">Identifications</li>@endsection
@section('content')
<div class="page-header">
    <div><h1 class="page-title">Identifications</h1><p class="page-subtitle">{{ $identifications->total() }} identifications</p></div>
    @can('identifications.create')<a href="{{ route('identifications.create') }}" class="btn-primary-custom"><i data-lucide="plus" style="width:16px;height:16px"></i> Nouvelle</a>@endcan
</div>
<div class="card">
    @if($identifications->count())
    <div style="overflow-x:auto">
        <table class="data-table">
            <thead><tr><th>Numéro</th><th>Producteur</th><th>Campagne</th><th class="numeric">Superficie</th><th>Statut</th><th>Contrôleur</th><th>Date</th><th class="actions">Actions</th></tr></thead>
            <tbody>
                @foreach($identifications as $i)
                <tr>
                    <td class="code">{{ $i->numero }}</td>
                    <td style="font-weight:600">{{ $i->producteur->nom }} {{ $i->producteur->prenom }}</td>
                    <td>{{ $i->campagne }}</td>
                    <td class="numeric">{{ $i->superficie ? number_format($i->superficie, 2) : '—' }}</td>
                    <td>
                        @if($i->statut === 'APPROUVE')<span class="badge-status badge-bio">Approuvée</span>
                        @elseif($i->statut === 'REJETE')<span class="badge-status badge-error">Rejetée</span>
                        @else<span class="badge-status badge-ok">En attente</span>@endif
                    </td>
                    <td>{{ $i->controleur?->name ?? '—' }}</td>
                    <td>{{ $i->created_at->format('d/m/Y') }}</td>
                    <td class="actions">
                        @can('identifications.approve')
                        @if($i->statut === 'EN_ATTENTE')
                        <form method="POST" action="{{ route('identifications.approve', $i) }}" style="display:inline">
                            @csrf @method('PATCH')
                            <input type="hidden" name="statut" value="APPROUVE">
                            <button class="btn-icon-sm btn-icon-primary" title="Approuver"><i data-lucide="check" style="width:14px;height:14px"></i></button>
                        </form>
                        <form method="POST" action="{{ route('identifications.approve', $i) }}" style="display:inline" onsubmit="return confirm('Rejeter ?')">
                            @csrf @method('PATCH')
                            <input type="hidden" name="statut" value="REJETE">
                            <button class="btn-icon-sm btn-icon-danger" title="Rejeter"><i data-lucide="x" style="width:14px;height:14px"></i></button>
                        </form>
                        @endif
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper">{{ $identifications->links() }}</div>
    @else <div class="empty-state"><div class="empty-icon">📋</div><div class="empty-title">Aucune identification</div></div> @endif
</div>
@endsection
