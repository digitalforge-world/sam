@extends('layouts.export')
@section('title', 'Liste des Parcelles')
@section('doc_name', 'Liste des Parcelles')
@section('content')

<div style="margin-bottom: 10px; font-weight: bold; color: #1a1a2e; font-size: 11pt;">
    Exporté le {{ now()->format('d/m/Y à H:i') }} — {{ $parcelles->count() }} parcelles
</div>

<table>
    <thead>
        <tr>
            <th style="width: 70px">Indice</th>
            <th>Producteur</th>
            <th>Village</th>
            <th>Culture</th>
            <th style="text-align:right">Superficie (ha)</th>
            <th style="text-align:center">BIO</th>
            <th style="text-align:center">Approbation</th>
        </tr>
    </thead>
    <tbody>
        @foreach($parcelles as $p)
        <tr>
            <td style="font-family: monospace; font-weight:bold; color: #155724">#{{ $p->indice }}</td>
            <td><strong>{{ $p->producteur->nom ?? '—' }} {{ $p->producteur->prenom ?? '' }}</strong></td>
            <td>{{ $p->village->nom ?? '—' }}</td>
            <td>{{ $p->culture->nom ?? '—' }}</td>
            <td style="text-align:right">{{ $p->superficie ? number_format($p->superficie, 2) : '—' }}</td>
            <td style="text-align:center">
                @if($p->bio)
                    <span class="badge badge-bio">OUI</span>
                @else
                    <span class="badge badge-muted">NON</span>
                @endif
            </td>
            <td style="text-align:center">
                @if($p->approbation_production === 'BIO')
                    <span class="badge badge-bio">BIO</span>
                @elseif($p->approbation_production === 'CONVERSION')
                    <span class="badge" style="background:#fff3cd; color:#856404;">CONV.</span>
                @elseif($p->approbation_production === 'DECLASSIFIED')
                    <span class="badge" style="background:#f8d7da; color:#842029;">DECL.</span>
                @else
                    —
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
