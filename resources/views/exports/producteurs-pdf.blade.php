@extends('layouts.export')
@section('title', 'Liste des Producteurs')
@section('doc_name', 'Liste des Producteurs')
@section('content')

<div style="margin-bottom: 10px; font-weight: bold; color: #1a1a2e; font-size: 11pt;">
    Exporté le {{ now()->format('d/m/Y à H:i') }} — {{ $producteurs->count() }} producteurs
</div>

<table>
    <thead>
        <tr>
            <th style="width: 70px">Code</th>
            <th>Nom & Prénom</th>
            <th style="width: 50px">Sexe</th>
            <th style="width: 80px">Téléphone</th>
            <th>Zone</th>
            <th>Village</th>
            <th>Organisation</th>
            <th style="text-align:center; width: 60px">Parcelles</th>
            <th style="text-align:center; width: 50px">Actif</th>
        </tr>
    </thead>
    <tbody>
        @foreach($producteurs as $p)
        <tr>
            <td style="font-family: monospace; font-weight:bold; color: #155724">{{ $p->code }}</td>
            <td><strong>{{ $p->nom }} {{ $p->prenom }}</strong></td>
            <td>{{ $p->sexe ?? '—' }}</td>
            <td>{{ $p->telephone ?? '—' }}</td>
            <td>{{ $p->zone->nom ?? '—' }}</td>
            <td>{{ $p->village->nom ?? '—' }}</td>
            <td>{{ $p->organisation->nom ?? '—' }}</td>
            <td style="text-align:center">{{ $p->parcelles_count }}</td>
            <td style="text-align:center">
                @if($p->est_actif)
                    <span class="badge badge-bio">OUI</span>
                @else
                    <span class="badge badge-muted">NON</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
