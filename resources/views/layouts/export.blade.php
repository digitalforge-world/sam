<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>@yield('title')</title>
<style>
    @page { margin: 1cm; }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; color: #333; }

    /* ── Header ───────────────────────────────────────── */
    .export-header {
        position: relative;
        padding-bottom: 20px;
        margin-bottom: 30px;
        border-bottom: 2px solid #28a745;
        min-height: 80px;
    }

    .header-logo {
        position: absolute;
        top: 0;
        left: 0;
        width: 180px;
    }

    .header-logo img { height: 50px; width: auto; }
    .header-logo .logo-subtitle {
        font-size: 5pt;
        color: #444;
        margin-top: 2px;
        line-height: 1.2;
        text-transform: uppercase;
    }

    .header-center {
        text-align: center;
        padding-top: 10px;
    }

    .header-center h1 {
        font-size: 20pt;
        color: #28a745;
        font-weight: bold;
        letter-spacing: 1px;
        margin-bottom: 4px;
    }

    .header-center p {
        font-size: 9pt;
        color: #000;
        font-weight: bold;
    }

    /* ── Meta info ─────────────────────────────────────── */
    .export-meta {
        display: flex;
        justify-content: space-between;
        font-size: 8pt;
        color: #666;
        margin-bottom: 10px;
        text-transform: uppercase;
        border-bottom: 1px solid #eee;
        padding-bottom: 4px;
    }

    /* ── Table Design ──────────────────────────────────── */
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    thead tr { background: #28a745; color: #fff; }
    thead th { padding: 8px 10px; text-align: left; font-size: 8pt; text-transform: uppercase; border: 1px solid #28a745; }
    tbody td { padding: 6px 10px; border: 1px solid #eee; font-size: 8.5pt; vertical-align: middle; }
    tbody tr:nth-child(even) { background: #f9f9f9; }

    .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 7.5pt; font-weight: bold; text-align: center; }
    .badge-bio { background: #d4edda; color: #155724; }
    .badge-muted { background: #e2e3e5; color: #383d41; }
    
    .footer {
        position: fixed;
        bottom: 0px;
        left: 0;
        right: 0;
        height: 30px;
        border-top: 1px solid #eee;
        text-align: center;
        font-size: 8pt;
        color: #888;
        padding-top: 6px;
    }

    .page-number:after { content: counter(page); }
</style>
</head>
<body>

<div class="export-header">
    <div class="header-logo">
        <img src="{{ public_path('assets/img/logo-ofca.png') }}" alt="Logo">
        <div class="logo-subtitle">
            <strong>SCOOPS OFCA</strong><br>
            SOCIETE COOPERATIVE SIMPLIFIEE<br>
            ORGANIC FARMING COOPERATIVE IN AFRICA
        </div>
    </div>
    <div class="header-center">
        <h1>SCOOPS OFCA</h1>
        <p>Production-Commercialisation de produits agricoles biologiques</p>
    </div>
</div>

<div class="export-meta">
    <span>Document: @yield('doc_name')</span>
    <span style="float:right">Date: {{ now()->format('d/m/Y') }}</span>
</div>

@yield('content')

<div class="footer">
    OFCA — SAM-BIO &copy; {{ now()->year }} — Page <span class="page-number"></span>
</div>

</body>
</html>
