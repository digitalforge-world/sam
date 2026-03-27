@extends('layouts.app')
@section('title', 'Parcelles')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
    <li class="breadcrumb-item current">Parcelles</li>
@endsection
@section('content')
<div class="page-header">
    <div><h1 class="page-title">Parcelles</h1><p class="page-subtitle">{{ $parcelles->total() }} parcelles</p></div>
    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
        <a href="{{ route('export.parcelles.pdf') }}" class="btn-export btn-export-pdf" title="Télécharger PDF">
            <i data-lucide="file-text" style="width:15px;height:15px"></i> PDF
        </a>
        <a href="{{ route('export.parcelles.excel') }}" class="btn-export btn-export-excel" title="Télécharger Excel">
            <i data-lucide="table-2" style="width:15px;height:15px"></i> Excel
        </a>
        @can('parcelles.create')<a href="{{ route('parcelles.create') }}" class="btn-primary-custom"><i data-lucide="plus" style="width:16px;height:16px"></i> Nouvelle</a>@endcan
    </div>
</div>
<div class="card">
    @if($parcelles->count())
    <div style="overflow-x:auto">
        <table class="data-table">
            <thead><tr><th>Indice</th><th>Producteur</th><th>Village</th><th>Culture</th><th class="numeric">Superficie</th><th>BIO</th><th>Approbation</th><th class="actions">Actions</th></tr></thead>
            <tbody>
                @foreach($parcelles as $p)
                <tr>
                    <td class="code">#{{ $p->indice }}</td>
                    <td style="font-weight:600">{{ $p->producteur->nom }} {{ $p->producteur->prenom }}</td>
                    <td>{{ $p->village->nom ?? '—' }}</td>
                    <td>{{ $p->culture->nom ?? '—' }}</td>
                    <td class="numeric">{{ $p->superficie ? number_format($p->superficie, 2) : '—' }}</td>
                    <td>@if($p->bio)<span class="badge-status badge-bio">Oui</span>@else<span class="badge-status badge-muted">Non</span>@endif</td>
                    <td>
                        @if($p->approbation_production === 'BIO')<span class="badge-status badge-bio">BIO</span>
                        @elseif($p->approbation_production === 'CONVERSION')<span class="badge-status badge-ok">En conversion</span>
                        @elseif($p->approbation_production === 'DECLASSIFIED')<span class="badge-status badge-error">Déclassée</span>
                        @else —
                        @endif
                    </td>
                    <td class="actions">
                        <a href="{{ route('parcelles.show', $p) }}" class="btn-icon-sm btn-icon-primary"><i data-lucide="eye" style="width:14px;height:14px"></i></a>
                        @can('parcelles.edit')<a href="{{ route('parcelles.edit', $p) }}" class="btn-icon-sm btn-icon-warning"><i data-lucide="pencil" style="width:14px;height:14px"></i></a>@endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper">{{ $parcelles->links() }}</div>
    @else <div class="empty-state"><div class="empty-icon">🌾</div><div class="empty-title">Aucune parcelle</div></div> @endif
</div>
@endsection
