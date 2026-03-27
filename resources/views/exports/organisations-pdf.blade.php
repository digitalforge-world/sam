@extends('layouts.export')
@section('title', 'Liste des Organisations Paysannes')
@section('doc_name', 'Liste des Organisations Paysannes')
@section('content')

<div style="margin-bottom: 10px; font-weight: bold; color: #1a1a2e; font-size: 11pt;">
    Exporté le {{ now()->format('d/m/Y à H:i') }} — {{ $organisations->count() }} organisations
</div>

<table>
    <thead>
        <tr>
            <th style="width: 40px">#</th>
            <th>Nom de l'organisation</th>
            <th>Zone</th>
            <th>Village</th>
            <th>Contrôleur</th>
            <th style="text-align:center; width: 100px">Producteurs</th>
        </tr>
    </thead>
    <tbody>
        @foreach($organisations as $o)
        <tr>
            <td>{{ $o->id }}</td>
            <td style="font-weight:bold">{{ $o->nom }}</td>
            <td>{{ $o->zone->nom ?? '—' }}</td>
            <td>{{ $o->village->nom ?? '—' }}</td>
            <td>{{ $o->controleur ? $o->controleur->name . ' ' . $o->controleur->prenom : '—' }}</td>
            <td style="text-align:center">
                <span class="badge badge-bio">{{ $o->producteurs_count }}</span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
