<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Liste des Cultures</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1a1a2e; }

    .header { background: #4A7C59; color: #fff; padding: 14px 20px; margin-bottom: 16px; }
    .header h1 { font-size: 18px; font-weight: bold; letter-spacing: 0.5px; }
    .header p  { font-size: 10px; opacity: 0.85; margin-top: 4px; }

    .meta { display: flex; justify-content: space-between; padding: 0 20px; margin-bottom: 14px; }
    .meta span { font-size: 9px; color: #555; }

    table { width: 60%; margin: 0 auto; border-collapse: collapse; font-size: 10px; }
    thead tr { background: #4A7C59; color: #fff; }
    thead th { padding: 8px 16px; text-align: left; font-weight: bold; }
    tbody tr:nth-child(even) { background: #f0f7f4; }
    tbody tr:nth-child(odd)  { background: #ffffff; }
    tbody td { padding: 7px 16px; border-bottom: 1px solid #e0e0e0; }

    .footer { margin-top: 18px; padding: 6px 20px; border-top: 1px solid #ccc; text-align: right; font-size: 8px; color: #888; }
</style>
</head>
<body>

<div class="header">
    <h1>🌱 Liste des Cultures</h1>
    <p>Exporté le {{ now()->format('d/m/Y à H:i') }} — {{ $cultures->count() }} cultures</p>
</div>

<div class="meta">
    <span>OFCA — SAM-BIO</span>
    <span>Document généré automatiquement</span>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Nom de la culture</th>
            <th>Créé le</th>
        </tr>
    </thead>
    <tbody>
        @foreach($cultures as $c)
        <tr>
            <td>{{ $c->id }}</td>
            <td><strong>{{ $c->nom }}</strong></td>
            <td>{{ $c->created_at->format('d/m/Y') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    OFCA / SAM-BIO &copy; {{ now()->year }}
</div>

</body>
</html>
