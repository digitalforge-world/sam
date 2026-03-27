<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Liste des Organisations Paysannes</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1a1a2e; }

    .header { background: #2D6A4F; color: #fff; padding: 14px 20px; margin-bottom: 16px; }
    .header h1 { font-size: 18px; font-weight: bold; letter-spacing: 0.5px; }
    .header p  { font-size: 10px; opacity: 0.85; margin-top: 4px; }

    .meta { display: flex; justify-content: space-between; padding: 0 20px; margin-bottom: 14px; }
    .meta span { font-size: 9px; color: #555; }

    table { width: 100%; border-collapse: collapse; font-size: 9.5px; }
    thead tr { background: #2D6A4F; color: #fff; }
    thead th { padding: 7px 10px; text-align: left; font-weight: bold; }
    tbody tr:nth-child(even) { background: #f0f7f4; }
    tbody tr:nth-child(odd)  { background: #ffffff; }
    tbody td { padding: 6px 10px; border-bottom: 1px solid #e0e0e0; vertical-align: middle; }

    .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 8px; font-weight: bold; }
    .badge-green { background: #d8f3dc; color: #2D6A4F; }

    .footer { margin-top: 18px; padding: 6px 20px; border-top: 1px solid #ccc; text-align: right; font-size: 8px; color: #888; }
</style>
</head>
<body>

<div class="header">
    <h1>🌿 Liste des Organisations Paysannes</h1>
    <p>Exporté le {{ now()->format('d/m/Y à H:i') }} — {{ $organisations->count() }} organisations</p>
</div>

<div class="meta">
    <span>OFCA — SAM-BIO</span>
    <span>Document généré automatiquement</span>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Nom</th>
            <th>Zone</th>
            <th>Village</th>
            <th>Contrôleur</th>
            <th style="text-align:center">Producteurs</th>
        </tr>
    </thead>
    <tbody>
        @foreach($organisations as $o)
        <tr>
            <td>{{ $o->id }}</td>
            <td><strong>{{ $o->nom }}</strong></td>
            <td>{{ $o->zone->nom ?? '—' }}</td>
            <td>{{ $o->village->nom ?? '—' }}</td>
            <td>{{ $o->controleur ? $o->controleur->name . ' ' . $o->controleur->prenom : '—' }}</td>
            <td style="text-align:center"><span class="badge badge-green">{{ $o->producteurs_count }}</span></td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    Page <span class="page">1</span> — OFCA / SAM-BIO &copy; {{ now()->year }}
</div>

</body>
</html>
