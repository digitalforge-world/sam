@extends('layouts.app')
@section('title', 'Contrôles')
@section('breadcrumb')<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li><li class="breadcrumb-item current">Contrôles</li>@endsection
@section('content')
<div class="page-header">
    <div><h1 class="page-title">Contrôles</h1><p class="page-subtitle">{{ $controles->total() }} contrôles</p></div>
    @can('controles.create')<a href="{{ route('controles.create') }}" class="btn-primary-custom"><i data-lucide="plus" style="width:16px;height:16px"></i> Nouveau</a>@endcan
</div>
<div class="card">
    @if($controles->count())
    <div style="overflow-x:auto">
        <table class="data-table">
            <thead><tr><th>Numéro</th><th>Producteur</th><th>Parcelle</th><th>Culture</th><th class="numeric">Sup. parcelle</th><th class="numeric">Sup. bio</th><th>Campagne</th><th>Contrôleur</th></tr></thead>
            <tbody>
                @foreach($controles as $c)
                <tr>
                    <td class="code">{{ $c->numero }}</td>
                    <td style="font-weight:600">{{ $c->producteur->nom }} {{ $c->producteur->prenom }}</td>
                    <td class="code">#{{ $c->parcelle->indice }}</td>
                    <td>{{ $c->culture->nom }}</td>
                    <td class="numeric">{{ $c->superficie_parcelle ? number_format($c->superficie_parcelle, 2) : '—' }}</td>
                    <td class="numeric">{{ $c->superficie_bio ? number_format($c->superficie_bio, 3) : '—' }}</td>
                    <td>{{ $c->campagne }}</td>
                    <td>{{ $c->controleur?->name ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper">{{ $controles->links() }}</div>
    @else <div class="empty-state"><div class="empty-icon">✅</div><div class="empty-title">Aucun contrôle</div></div> @endif
</div>
@endsection
