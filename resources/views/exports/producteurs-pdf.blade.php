<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Liste des Producteurs</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #1a1a2e; }

    .header { background: #1A1A2E; color: #fff; padding: 14px 20px; margin-bottom: 16px; }
    .header h1 { font-size: 18px; font-weight: bold; letter-spacing: 0.5px; }
    .header p  { font-size: 10px; opacity: 0.85; margin-top: 4px; }

    .meta { display: flex; justify-content: space-between; padding: 0 20px; margin-bottom: 14px; }
    .meta span { font-size: 9px; color: #555; }

    table { width: 100%; border-collapse: collapse; font-size: 9px; }
    thead tr { background: #1A1A2E; color: #fff; }
    thead th { padding: 6px 8px; text-align: left; font-weight: bold; }
    tbody tr:nth-child(even) { background: #f5f5f5; }
    tbody tr:nth-child(odd)  { background: #ffffff; }
    tbody td { padding: 5px 8px; border-bottom: 1px solid #e8e8e8; vertical-align: middle; }

    .badge { display: inline-block; padding: 2px 5px; border-radius: 3px; font-size: 8px; font-weight: bold; }
    .badge-actif   { background: #d8f3dc; color: #2D6A4F; }
    .badge-inactif { background: #f5f5f5; color: #888; }
    .code { font-family: monospace; color: #2D6A4F; font-weight: bold; }

    .footer { margin-top: 18px; padding: 6px 20px; border-top: 1px solid #ccc; text-align: right; font-size: 8px; color: #888; }
</style>
</head>
<body>

<div class="header">
    <h1>👤 Liste des Producteurs</h1>
    <p>Exporté le {{ now()->format('d/m/Y à H:i') }} — {{ $producteurs->count() }} producteurs</p>
</div>

<div class="meta">
    <span>OFCA — SAM-BIO</span>
    <span>Document généré automatiquement</span>
</div>

<table>
    <thead>
        <tr>
            <th>Code</th>
            <th>Nom & Prénom</th>
            <th>Sexe</th>
            <th>Téléphone</th>
            <th>Zone</th>
            <th>Village</th>
            <th>Organisation</th>
            <th>Statut</th>
            <th style="text-align:center">Parcelles</th>
            <th style="text-align:center">Actif</th>
        </tr>
    </thead>
    <tbody>
        @foreach($producteurs as $p)
        <tr>
            <td class="code">{{ $p->code }}</td>
            <td><strong>{{ $p->nom }} {{ $p->prenom }}</strong></td>
            <td>{{ $p->sexe ?? '—' }}</td>
            <td>{{ $p->telephone ?? '—' }}</td>
            <td>{{ $p->zone->nom ?? '—' }}</td>
            <td>{{ $p->village->nom ?? '—' }}</td>
            <td>{{ $p->organisation->nom ?? '—' }}</td>
            <td>{{ $p->statut ?? '—' }}</td>
            <td style="text-align:center">{{ $p->parcelles_count }}</td>
            <td style="text-align:center">
                @if($p->est_actif)
                    <span class="badge badge-actif">Actif</span>
                @else
                    <span class="badge badge-inactif">Inactif</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    Page <span class="page">1</span> — OFCA / SAM-BIO &copy; {{ now()->year }}
</div>

</body>
</html>
