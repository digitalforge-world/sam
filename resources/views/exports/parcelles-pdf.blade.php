<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Liste des Parcelles</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 8.5px; color: #1a1a2e; }

    .header { background: #2D6A4F; color: #fff; padding: 14px 20px; margin-bottom: 16px; }
    .header h1 { font-size: 18px; font-weight: bold; letter-spacing: 0.5px; }
    .header p  { font-size: 10px; opacity: 0.85; margin-top: 4px; }

    .meta { display: flex; justify-content: space-between; padding: 0 20px; margin-bottom: 14px; }
    .meta span { font-size: 9px; color: #555; }

    table { width: 100%; border-collapse: collapse; font-size: 8.5px; }
    thead tr { background: #2D6A4F; color: #fff; }
    thead th { padding: 6px 8px; text-align: left; font-weight: bold; }
    tbody tr:nth-child(even) { background: #f0f7f4; }
    tbody tr:nth-child(odd)  { background: #ffffff; }
    tbody td { padding: 5px 8px; border-bottom: 1px solid #e0e0e0; vertical-align: middle; }

    .badge { display: inline-block; padding: 2px 5px; border-radius: 3px; font-size: 7.5px; font-weight: bold; }
    .badge-bio        { background: #d8f3dc; color: #2D6A4F; }
    .badge-conversion { background: #fff3cd; color: #856404; }
    .badge-declasse   { background: #f8d7da; color: #842029; }
    .badge-non        { background: #f5f5f5; color: #888; }
    .indice           { font-family: monospace; font-weight: bold; color: #2D6A4F; }

    .footer { margin-top: 18px; padding: 6px 20px; border-top: 1px solid #ccc; text-align: right; font-size: 8px; color: #888; }
</style>
</head>
<body>

<div class="header">
    <h1>🌾 Liste des Parcelles</h1>
    <p>Exporté le {{ now()->format('d/m/Y à H:i') }} — {{ $parcelles->count() }} parcelles</p>
</div>

<div class="meta">
    <span>OFCA — SAM-BIO</span>
    <span>Document généré automatiquement</span>
</div>

<table>
    <thead>
        <tr>
            <th>Indice</th>
            <th>Producteur</th>
            <th>Village</th>
            <th>Culture</th>
            <th style="text-align:right">Superficie (ha)</th>
            <th style="text-align:right">BIO (ha)</th>
            <th style="text-align:center">BIO</th>
            <th style="text-align:center">Approbation</th>
        </tr>
    </thead>
    <tbody>
        @foreach($parcelles as $p)
        <tr>
            <td class="indice">#{{ $p->indice }}</td>
            <td><strong>{{ $p->producteur->nom ?? '—' }} {{ $p->producteur->prenom ?? '' }}</strong></td>
            <td>{{ $p->village->nom ?? '—' }}</td>
            <td>{{ $p->culture->nom ?? '—' }}</td>
            <td style="text-align:right">{{ $p->superficie ? number_format($p->superficie, 2) : '—' }}</td>
            <td style="text-align:right">{{ $p->superficie_bio ? number_format($p->superficie_bio, 2) : '—' }}</td>
            <td style="text-align:center">
                @if($p->bio)
                    <span class="badge badge-bio">Oui</span>
                @else
                    <span class="badge badge-non">Non</span>
                @endif
            </td>
            <td style="text-align:center">
                @if($p->approbation_production === 'BIO')
                    <span class="badge badge-bio">BIO</span>
                @elseif($p->approbation_production === 'CONVERSION')
                    <span class="badge badge-conversion">Conversion</span>
                @elseif($p->approbation_production === 'DECLASSIFIED')
                    <span class="badge badge-declasse">Déclassée</span>
                @else
                    —
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    OFCA / SAM-BIO &copy; {{ now()->year }}
</div>

</body>
</html>
