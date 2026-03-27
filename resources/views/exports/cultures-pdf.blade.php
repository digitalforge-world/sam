@extends('layouts.export')
@section('title', 'Liste des Cultures')
@section('doc_name', 'Liste des Cultures')
@section('content')

<div style="margin-bottom: 10px; font-weight: bold; color: #1a1a2e; font-size: 11pt; text-align: center;">
    Exporté le {{ now()->format('d/m/Y à H:i') }} — {{ $cultures->count() }} cultures
</div>

<table style="width: 60%; margin: 20px auto;">
    <thead>
        <tr>
            <th style="width: 60px; text-align:center">#</th>
            <th>Nom de la culture</th>
            <th style="width: 120px; text-align:center">Créé le</th>
        </tr>
    </thead>
    <tbody>
        @foreach($cultures as $c)
        <tr>
            <td style="text-align:center">{{ $c->id }}</td>
            <td><strong>{{ $c->nom }}</strong></td>
            <td style="text-align:center; font-size: 8pt">{{ $c->created_at->format('d/m/Y') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
