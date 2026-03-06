# Guide de Style UI/UX — Système de Certification Agricole Biologique
### Design System · Metronic · Laravel Blade

---

## Table des matières

1. [Identité visuelle](#1-identité-visuelle)
2. [Typographie](#2-typographie)
3. [Palette de couleurs](#3-palette-de-couleurs)
4. [Layout & Navigation](#4-layout--navigation)
5. [Composants de base](#5-composants-de-base)
6. [Pages Liste (Index)](#6-pages-liste-index)
7. [Pages Formulaire](#7-pages-formulaire)
8. [Page Carte Mapbox](#8-page-carte-mapbox)
9. [Dashboard](#9-dashboard)
10. [États & Feedback](#10-états--feedback)
11. [Responsive & Mobile](#11-responsive--mobile)
12. [Checklist de validation](#12-checklist-de-validation)

---

## 1. Identité visuelle

### Concept

L'application suit une esthétique **"Institutional Clean"** — sobre, lisible, professionnel. Elle s'adresse à des agents terrain et superviseurs. L'interface doit inspirer **confiance**, réduire la **charge cognitive** et permettre une saisie rapide sur le terrain, parfois depuis un smartphone.

### Principes directeurs

| Principe | Application |
|----------|-------------|
| **Clarté** | Un seul objectif par page, hiérarchie visuelle nette |
| **Cohérence** | Même composant = même style partout |
| **Efficacité** | 3 clics max pour atteindre n'importe quelle donnée |
| **Feedback immédiat** | Toute action utilisateur reçoit une réponse visuelle |
| **Accessibilité** | Contraste WCAG AA minimum, zones de clic ≥ 44px |

### Nom & logo (placeholder)

```html
<!-- Logo dans la sidebar -->
<div class="app-sidebar-logo px-6 py-4">
    <a href="/" class="d-flex align-items-center gap-3 text-decoration-none">
        <!-- Icône feuille + écusson certification -->
        <div class="logo-icon d-flex align-items-center justify-content-center"
             style="width:40px;height:40px;background:var(--color-primary);border-radius:10px;">
            <span style="font-size:20px;">🌿</span>
        </div>
        <div>
            <div class="fw-bolder text-dark fs-5 lh-1">BIO</div>
            <div class="text-muted fs-8 fw-semibold">SAM</div>
        </div>
    </a>
</div>
```

---

## 2. Typographie

### Familles de polices

```html
<!-- Dans le <head> du layout principal -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
```

```css
:root {
    --font-base:  'Plus Jakarta Sans', sans-serif;
    --font-mono:  'JetBrains Mono', monospace;
}

body {
    font-family: var(--font-base);
    font-size: 14px;
    line-height: 1.6;
    color: var(--color-text-primary);
    -webkit-font-smoothing: antialiased;
}
```

### Échelle typographique

| Rôle | Classe Metronic | Taille | Poids | Usage |
|------|-----------------|--------|-------|-------|
| Titre de page | `fs-1 fw-bolder` | 28px | 800 | H1 de chaque page |
| Titre de section | `fs-3 fw-bold` | 20px | 700 | Titres de cartes |
| Titre de carte | `fs-5 fw-bold` | 16px | 700 | En-tête de formulaire |
| Label de champ | `fs-5 fw-bold mb-2` | 14px | 700 | Labels formulaire |
| Corps de texte | `fs-6` | 14px | 400 | Contenu standard |
| Texte secondaire | `fs-7 text-muted` | 12px | 400 | Descriptions, aides |
| Code / ID | `font-mono fs-7` | 12px | 500 | Codes producteur, IDs |

### Règles typographiques

- **Jamais** d'UPPERCASE sur les labels de formulaire
- Les titres de page sont en **casse de titre** (première lettre majuscule)
- Les codes producteur et identifiants techniques utilisent la **police monospace**
- Les nombres (superficies, rendements) utilisent `font-variant-numeric: tabular-nums` pour l'alignement dans les tableaux

```css
/* Nombres dans les tableaux */
.table td.numeric {
    font-variant-numeric: tabular-nums;
    text-align: right;
    font-family: var(--font-mono);
    font-size: 13px;
}
```

---

## 3. Palette de couleurs

### Variables CSS globales

```css
/* resources/css/theme.css */
:root {
    /* ── Couleurs primaires ──────────────────────────── */
    --color-primary:        #1B6B4A;   /* Vert forêt — couleur principale */
    --color-primary-light:  #E8F5EE;   /* Vert très clair — fonds, hover */
    --color-primary-dark:   #124832;   /* Vert foncé — hover boutons */

    /* ── Couleurs d'accentuation ─────────────────────── */
    --color-secondary:      #2D6A8F;   /* Bleu ardoise — liens, secondaire */
    --color-accent:         #F59E0B;   /* Ambre — alertes, badges actifs */

    /* ── Statuts ─────────────────────────────────────── */
    --color-success:        #22C55E;   /* Vert — bio / approuvé */
    --color-warning:        #EAB308;   /* Jaune — OK / en attente */
    --color-danger:         #EF4444;   /* Rouge — déclassifié / erreur */
    --color-info:           #3B82F6;   /* Bleu — information */

    /* ── Carte (Mapbox) ──────────────────────────────── */
    --color-map-bio:        #22C55E;
    --color-map-ok:         #EAB308;
    --color-map-declasse:   #EF4444;
    --color-map-neutre:     #94A3B8;
    --color-map-draw:       #FF9F43;   /* Orange dessin */

    /* ── Neutrals ────────────────────────────────────── */
    --color-text-primary:   #1A1D23;
    --color-text-secondary: #6B7280;
    --color-text-muted:     #9CA3AF;
    --color-border:         #E5E7EB;
    --color-border-strong:  #D1D5DB;
    --color-bg-page:        #F8FAFC;
    --color-bg-card:        #FFFFFF;
    --color-bg-sidebar:     #1A1D23;   /* Sidebar sombre */

    /* ── Ombres ──────────────────────────────────────── */
    --shadow-sm:   0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
    --shadow-md:   0 4px 6px rgba(0,0,0,.07), 0 2px 4px rgba(0,0,0,.05);
    --shadow-lg:   0 10px 15px rgba(0,0,0,.08), 0 4px 6px rgba(0,0,0,.05);

    /* ── Rayons ──────────────────────────────────────── */
    --radius-sm:   6px;
    --radius-md:   10px;
    --radius-lg:   16px;
    --radius-xl:   20px;
}
```

### Utilisation des couleurs par contexte

| Contexte | Couleur | Variable |
|----------|---------|----------|
| Bouton principal (Créer, Soumettre) | Vert forêt | `--color-primary` |
| Bouton secondaire (Voir, Filtrer) | Bleu ardoise | `--color-secondary` |
| Badge Bio / Approuvé | Vert | `--color-success` |
| Badge OK / En attente | Jaune | `--color-warning` |
| Badge Déclassifié / Erreur | Rouge | `--color-danger` |
| Liens de navigation | Vert forêt | `--color-primary` |
| Fond de page | Gris très clair | `--color-bg-page` |
| Fond de carte / panneau | Blanc | `--color-bg-card` |
| Sidebar | Gris anthracite | `--color-bg-sidebar` |

---

## 4. Layout & Navigation

### Structure générale

```
┌─────────────────────────────────────────────────────────┐
│  SIDEBAR (260px, fixe, sombre)                          │
│  ┌───────────────────────────────────────────────────┐  │
│  │  Logo                                             │  │
│  │  ─────────────────────────────                   │  │
│  │  🏠 Dashboard                                    │  │
│  │  📍 Subdivisions  ▾                              │  │
│  │     › Régions                                    │  │
│  │     › Préfectures                                │  │
│  │     › Cantons                                    │  │
│  │     › Villages                                   │  │
│  │     › Zones                                      │  │
│  │  🏘 Organisations                                │  │
│  │  👨‍🌾 Producteurs                                  │  │
│  │  🌿 Cultures                                     │  │
│  │  🗂 Parcelles    ▾                               │  │
│  │     › Parcelles                                  │  │
│  │     › Identifications                            │  │
│  │     › Contrôles                                  │  │
│  │  🗺 Carte                                        │  │
│  │  ─────────────────────────────                   │  │
│  │  ⚙ Utilisateurs                                  │  │
│  │  🔧 Paramètres                                   │  │
│  └───────────────────────────────────────────────────┘  │
│                                                         │
│  CONTENU PRINCIPAL                                      │
│  ┌───────────────────────────────────────────────────┐  │
│  │  TOPBAR : Breadcrumb — Notifications — Profil     │  │
│  │  ─────────────────────────────────────────────    │  │
│  │  PAGE TITLE + BOUTON D'ACTION PRINCIPAL           │  │
│  │                                                   │  │
│  │  CONTENU (listes, formulaires, carte...)          │  │
│  └───────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────┘
```

### Sidebar — style CSS

```css
/* Sidebar sombre avec accent vert */
.app-sidebar {
    background-color: var(--color-bg-sidebar);
    border-right: none;
    width: 260px;
}

/* Items de menu */
.menu-item .menu-link {
    color: #9CA3AF;
    border-radius: var(--radius-sm);
    margin: 1px 8px;
    padding: 9px 12px;
    transition: all 0.15s ease;
}

.menu-item .menu-link:hover,
.menu-item.active > .menu-link {
    background-color: rgba(27, 107, 74, 0.15);
    color: #6EE7B7;
}

.menu-item.active > .menu-link .menu-icon {
    color: var(--color-primary);
}

/* Séparateur de groupe */
.menu-section .menu-section-text {
    color: #4B5563;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    padding: 16px 20px 6px;
}
```

### Topbar — structure

```blade
{{-- resources/views/layouts/partials/topbar.blade.php --}}
<div class="app-navbar flex-shrink-0">

    {{-- Breadcrumb --}}
    <div class="d-flex align-items-center">
        <ol class="breadcrumb text-muted fs-7 fw-semibold">
            <li class="breadcrumb-item"><a href="/" class="text-muted">Accueil</a></li>
            <li class="breadcrumb-item text-dark">@yield('breadcrumb')</li>
        </ol>
    </div>

    {{-- Actions topbar --}}
    <div class="d-flex align-items-center gap-2">

        {{-- Notifications --}}
        <button class="btn btn-icon btn-sm btn-light-secondary position-relative">
            <i class="bi bi-bell fs-4"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:9px;">3</span>
        </button>

        {{-- Profil --}}
        <div class="d-flex align-items-center gap-2 cursor-pointer" data-kt-menu-trigger="click">
            <div class="d-flex flex-column align-items-end">
                <span class="fs-7 fw-bold text-dark">{{ auth()->user()->name }}</span>
                <span class="badge badge-light-success fs-8">{{ auth()->user()->getRoleNames()->first() }}</span>
            </div>
            <div class="symbol symbol-35px">
                <div class="symbol-label fs-7 fw-bold"
                     style="background:var(--color-primary-light);color:var(--color-primary)">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
            </div>
        </div>
    </div>
</div>
```

### En-tête de page — standard

```blade
{{-- À placer en haut de chaque page --}}
<div class="d-flex align-items-center justify-content-between mb-7">
    <div>
        <h1 class="fs-1 fw-bolder text-dark mb-1">@yield('page-title')</h1>
        <p class="text-muted fs-7 mb-0">@yield('page-subtitle')</p>
    </div>
    <div class="d-flex gap-2">
        @yield('page-actions')
    </div>
</div>
```

---

## 5. Composants de base

### Boutons

```html
<!-- Primaire (Créer, Soumettre) -->
<button class="btn btn-sm fw-bold px-4"
        style="background:var(--color-primary);color:#fff;border-radius:var(--radius-sm);">
    <i class="bi bi-plus-lg me-1"></i> Nouveau producteur
</button>

<!-- Secondaire (Voir, Filtrer) -->
<button class="btn btn-sm btn-light-primary fw-semibold px-4">
    <i class="bi bi-funnel me-1"></i> Filtrer
</button>

<!-- Danger (Supprimer) -->
<button class="btn btn-sm btn-light-danger fw-semibold">
    <i class="bi bi-trash3 me-1"></i> Supprimer
</button>

<!-- Icône seule -->
<button class="btn btn-icon btn-sm btn-light-primary">
    <i class="bi bi-pencil fs-5"></i>
</button>
```

> **Règle** : ne jamais utiliser de bouton `btn-primary` Metronic par défaut (bleu). Surcharger avec `--color-primary` vert forêt.

### Badges de statut

```html
<!-- Bio / Approuvé -->
<span class="badge py-2 px-3 fs-8 fw-bold"
      style="background:#DCFCE7;color:#16A34A;border-radius:20px;">
    <span class="me-1">●</span> Bio
</span>

<!-- OK / En attente -->
<span class="badge py-2 px-3 fs-8 fw-bold"
      style="background:#FEF9C3;color:#CA8A04;border-radius:20px;">
    <span class="me-1">●</span> En attente
</span>

<!-- Déclassifié / Erreur -->
<span class="badge py-2 px-3 fs-8 fw-bold"
      style="background:#FEE2E2;color:#DC2626;border-radius:20px;">
    <span class="me-1">●</span> Déclassifié
</span>

<!-- Actif -->
<span class="badge py-2 px-3 fs-8 fw-bold"
      style="background:#DBEAFE;color:#1D4ED8;border-radius:20px;">
    <span class="me-1">●</span> Actif
</span>
```

### Cards

```html
<!-- Carte standard -->
<div class="card border-0 shadow-sm mb-6" style="border-radius:var(--radius-lg);">
    <div class="card-header border-0 pt-6 pb-0 px-6">
        <div class="card-title">
            <h3 class="fw-bold fs-4 text-dark mb-0">Titre de la carte</h3>
            <span class="text-muted fs-7 ms-2">Sous-titre</span>
        </div>
        <div class="card-toolbar gap-2">
            <!-- Actions de la carte -->
        </div>
    </div>
    <div class="card-body px-6 pt-4">
        <!-- Contenu -->
    </div>
</div>
```

```css
/* Style des cartes */
.card {
    border-radius: var(--radius-lg) !important;
    border: 1px solid var(--color-border) !important;
    box-shadow: var(--shadow-sm) !important;
    transition: box-shadow 0.2s ease;
}

.card:hover {
    box-shadow: var(--shadow-md) !important;
}

.card-header {
    background: transparent !important;
    border-bottom: 1px solid var(--color-border) !important;
}
```

### Toggle Switch (champs booléens)

```html
<!-- Toggle professionnel pour Bio, Cours d'eau, etc. -->
<div class="d-flex align-items-center justify-content-between p-4 rounded-3 border mb-4"
     style="border-color:var(--color-border)!important;background:#FAFAFA;">
    <div>
        <div class="fw-semibold fs-6 text-dark">Bio ?</div>
        <div class="text-muted fs-7">Cette parcelle est-elle certifiée biologique</div>
    </div>
    <div class="form-check form-switch form-check-custom form-check-solid">
        <input type="checkbox" name="bio" class="form-check-input h-30px w-50px" id="id_bio"
               style="cursor:pointer;">
    </div>
</div>
```

### Champs de formulaire

```html
<!-- Input standard -->
<div class="fv-row mb-6">
    <label class="fs-6 fw-semibold mb-2 required" for="id_superficie">Superficie</label>
    <div class="input-group">
        <input type="number" name="superficie" step="0.0001"
               class="form-control form-control-solid border-0 bg-light"
               style="border-radius:var(--radius-sm) 0 0 var(--radius-sm)!important;"
               placeholder="0.0000" id="id_superficie">
        <span class="input-group-text bg-light border-0 fw-semibold text-muted"
              style="border-radius:0 var(--radius-sm) var(--radius-sm) 0!important;">ha</span>
    </div>
    <div class="form-text text-muted fs-7">Superficie totale de la parcelle en hectares</div>
</div>

<!-- Select stylé -->
<div class="fv-row mb-6">
    <label class="fs-6 fw-semibold mb-2 required" for="id_niveau_pente">Niveau de pente</label>
    <select name="niveau_pente" class="form-select form-select-solid border-0 bg-light" id="id_niveau_pente">
        <option value="">Sélectionner...</option>
        <option value="WITHOUT">Sans pente</option>
        <option value="SMALL">Faible</option>
        <option value="MEDIUM">Moyen</option>
        <option value="HIGH">Forte</option>
    </select>
</div>
```

```css
/* Champs de formulaire */
.form-control-solid,
.form-select-solid {
    background-color: #F8FAFC !important;
    border: 1.5px solid var(--color-border) !important;
    border-radius: var(--radius-sm) !important;
    font-size: 14px;
    padding: 10px 14px;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.form-control-solid:focus,
.form-select-solid:focus {
    border-color: var(--color-primary) !important;
    box-shadow: 0 0 0 3px rgba(27, 107, 74, 0.12) !important;
    background-color: #fff !important;
    outline: none;
}

/* Label requis */
label.required::after {
    content: ' *';
    color: var(--color-danger);
    font-weight: 400;
}
```

---

## 6. Pages Liste (Index)

### Structure standard

```blade
{{-- resources/views/producteurs/index.blade.php --}}
@extends('layouts.app')

@section('page-title', 'Producteurs')
@section('page-subtitle', 'Gestion de l\'ensemble des producteurs enregistrés')

@section('page-actions')
    @can('producteurs.create')
    <a href="{{ route('producteurs.create') }}" class="btn btn-sm fw-bold px-5"
       style="background:var(--color-primary);color:#fff;border-radius:var(--radius-sm);">
        <i class="bi bi-person-plus me-2"></i>Nouveau producteur
    </a>
    @endcan
@endsection

@section('content')
<div class="card border-0" style="border-radius:var(--radius-lg);border:1px solid var(--color-border)!important;">

    {{-- Barre de filtre --}}
    <div class="card-header border-0 pt-6 pb-4">
        <div class="card-title">
            {{-- Recherche --}}
            <div class="d-flex align-items-center position-relative">
                <i class="bi bi-search position-absolute ms-3 text-muted"></i>
                <input type="text" id="search-input" placeholder="Rechercher..."
                       class="form-control form-control-sm ps-9 border-0 bg-light"
                       style="width:250px;border-radius:var(--radius-sm);">
            </div>
        </div>
        <div class="card-toolbar gap-2">
            <select class="form-select form-select-sm border-0 bg-light fw-semibold" style="width:auto;">
                <option value="">Toutes les zones</option>
                @foreach($zones as $zone)
                    <option value="{{ $zone->id }}">{{ $zone->nom }}</option>
                @endforeach
            </select>

            <select class="form-select form-select-sm border-0 bg-light fw-semibold" style="width:auto;">
                <option value="">Tous les statuts</option>
                <option value="1">Actif</option>
                <option value="0">Inactif</option>
            </select>

            <button class="btn btn-sm btn-light-secondary">
                <i class="bi bi-download me-1"></i>Export
            </button>
        </div>
    </div>

    {{-- Tableau --}}
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-3 mb-0">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0"
                        style="background:#F8FAFC;border-bottom:1px solid var(--color-border);">
                        <th class="ps-6 min-w-100px">Code</th>
                        <th class="min-w-180px">Nom & Prénom</th>
                        <th class="min-w-100px">Zone</th>
                        <th class="min-w-120px">Village</th>
                        <th class="min-w-80px text-center">Parcelles</th>
                        <th class="min-w-80px">Statut</th>
                        <th class="text-end pe-6 min-w-100px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($producteurs as $producteur)
                    <tr>
                        {{-- Code --}}
                        <td class="ps-6">
                            <span class="fw-semibold fs-7 text-muted"
                                  style="font-family:var(--font-mono);">
                                {{ $producteur->code }}
                            </span>
                        </td>

                        {{-- Nom --}}
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="symbol symbol-35px">
                                    <div class="symbol-label fw-bold fs-7"
                                         style="background:var(--color-primary-light);color:var(--color-primary);">
                                        {{ strtoupper(substr($producteur->nom, 0, 1)) }}
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('producteurs.show', $producteur) }}"
                                       class="text-dark fw-bold fs-6 text-hover-primary">
                                        {{ $producteur->nom }} {{ $producteur->prenom }}
                                    </a>
                                    <div class="text-muted fs-7">{{ $producteur->organisation?->nom ?? '—' }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- Zone --}}
                        <td>
                            <span class="badge badge-light-secondary fw-semibold">
                                {{ $producteur->zone->nom }}
                            </span>
                        </td>

                        {{-- Village --}}
                        <td class="text-muted fs-7">{{ $producteur->village->nom }}</td>

                        {{-- Parcelles --}}
                        <td class="text-center">
                            <span class="fw-bold fs-6" style="color:var(--color-primary);">
                                {{ $producteur->parcelles_count }}
                            </span>
                        </td>

                        {{-- Statut --}}
                        <td>
                            @if($producteur->est_actif)
                                <span class="badge py-2 px-3 fs-8 fw-bold"
                                      style="background:#DCFCE7;color:#16A34A;border-radius:20px;">
                                    ● Actif
                                </span>
                            @else
                                <span class="badge py-2 px-3 fs-8 fw-bold"
                                      style="background:#F3F4F6;color:#6B7280;border-radius:20px;">
                                    ● Inactif
                                </span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="text-end pe-6">
                            <a href="{{ route('producteurs.show', $producteur) }}"
                               class="btn btn-icon btn-sm btn-light-primary me-1">
                                <i class="bi bi-eye fs-5"></i>
                            </a>
                            @can('producteurs.edit')
                            <a href="{{ route('producteurs.edit', $producteur) }}"
                               class="btn btn-icon btn-sm btn-light-warning me-1">
                                <i class="bi bi-pencil fs-5"></i>
                            </a>
                            @endcan
                            @can('producteurs.delete')
                            <button class="btn btn-icon btn-sm btn-light-danger"
                                    data-confirm-delete="{{ route('producteurs.destroy', $producteur) }}">
                                <i class="bi bi-trash3 fs-5"></i>
                            </button>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-10">
                            <div class="text-muted fs-6">Aucun producteur trouvé</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="card-footer border-0 pt-4 pb-5 px-6 d-flex justify-content-between align-items-center">
        <div class="text-muted fs-7">
            Affichage de {{ $producteurs->firstItem() }} à {{ $producteurs->lastItem() }}
            sur {{ $producteurs->total() }} résultats
        </div>
        {{ $producteurs->links('vendor.pagination.custom') }}
    </div>
</div>
@endsection
```

### CSS tableau

```css
/* Lignes de tableau */
.table-row-dashed tr {
    border-bottom: 1px dashed var(--color-border);
    transition: background 0.1s ease;
}

.table-row-dashed tbody tr:hover {
    background-color: var(--color-primary-light);
}

.table th {
    font-size: 11px !important;
    letter-spacing: 0.05em;
    color: var(--color-text-muted) !important;
    padding: 14px 10px !important;
    white-space: nowrap;
}

.table td {
    padding: 14px 10px;
    vertical-align: middle;
}
```

---

## 7. Pages Formulaire

### Structure d'un formulaire standard

```blade
{{-- Formulaire en colonne centrée, largeur max 720px --}}
<div class="row justify-content-center">
<div class="col-12 col-xl-8">

    <div class="card border-0" style="border:1px solid var(--color-border)!important;border-radius:var(--radius-lg);">
        <div class="card-header border-0 pt-6 pb-4 px-7">
            <div class="card-title">
                <h3 class="fw-bold fs-4 mb-0">Informations de la parcelle</h3>
            </div>
        </div>
        <div class="card-body px-7 pt-4 pb-7">
            <form id="form-parcelle" action="{{ route('parcelles.store') }}" method="POST">
                @csrf

                {{-- SECTION : Producteur & Localisation --}}
                <div class="mb-7">
                    <div class="d-flex align-items-center gap-3 mb-5">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white fs-7"
                             style="width:28px;height:28px;background:var(--color-primary);flex-shrink:0;">1</div>
                        <h4 class="fw-bold fs-5 mb-0 text-dark">Producteur & Localisation</h4>
                    </div>

                    <div class="row g-4">
                        <div class="col-12">
                            {{-- Select dynamique producteur --}}
                        </div>
                        <div class="col-12">
                            {{-- Select dynamique village --}}
                        </div>
                    </div>
                </div>

                <div class="separator my-6" style="border-color:var(--color-border);"></div>

                {{-- SECTION : Contour (mini-carte) --}}
                <div class="mb-7">
                    <div class="d-flex align-items-center gap-3 mb-5">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white fs-7"
                             style="width:28px;height:28px;background:var(--color-primary);flex-shrink:0;">2</div>
                        <h4 class="fw-bold fs-5 mb-0 text-dark">Localisation sur la carte</h4>
                    </div>
                    {{-- Mini-carte Mapbox --}}
                    <div id="mini-map" style="height:320px;border-radius:var(--radius-md);border:2px solid var(--color-border);"></div>
                    <input type="hidden" name="contour_geojson" id="contour_geojson">
                    <div id="contour-status" class="mt-2 text-muted fs-7">
                        Aucun contour dessiné — optionnel.
                    </div>
                </div>

                <div class="separator my-6" style="border-color:var(--color-border);"></div>

                {{-- SECTION : Superficie --}}
                <div class="mb-7">
                    <div class="d-flex align-items-center gap-3 mb-5">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white fs-7"
                             style="width:28px;height:28px;background:var(--color-primary);flex-shrink:0;">3</div>
                        <h4 class="fw-bold fs-5 mb-0 text-dark">Superficie & Rendement</h4>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            {{-- Superficie totale --}}
                        </div>
                        <div class="col-md-6">
                            {{-- Type de culture --}}
                        </div>
                    </div>
                </div>

                {{-- ... autres sections ... --}}

                {{-- BOUTONS --}}
                <div class="d-flex justify-content-end gap-3 pt-4 mt-4"
                     style="border-top:1px solid var(--color-border);">
                    <a href="{{ route('parcelles.index') }}" class="btn btn-light fw-semibold px-6">
                        Annuler
                    </a>
                    <button type="submit" class="btn fw-bold px-8"
                            style="background:var(--color-primary);color:#fff;border-radius:var(--radius-sm);">
                        <i class="bi bi-check2 me-2"></i>Enregistrer la parcelle
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>
</div>
```

### Erreurs de validation

```blade
{{-- Afficher les erreurs --}}
@if($errors->any())
<div class="alert border-0 mb-6 d-flex align-items-center gap-3"
     style="background:#FEE2E2;border-radius:var(--radius-md)!important;border-left:4px solid var(--color-danger)!important;">
    <i class="bi bi-exclamation-triangle-fill text-danger fs-4"></i>
    <div>
        <div class="fw-bold text-danger fs-6">Veuillez corriger les erreurs suivantes :</div>
        <ul class="mb-0 mt-1 text-danger fs-7">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

{{-- Champ invalide --}}
<input class="form-control @error('superficie') is-invalid @enderror ...">
@error('superficie')
    <div class="invalid-feedback fw-semibold">{{ $message }}</div>
@enderror
```

---

## 8. Page Carte Mapbox

### Principes de style pour la carte

```css
/* Pleine hauteur sous la topbar */
#carte-wrapper {
    height: calc(100vh - 65px);
    display: flex;
    flex-direction: column;
}

/* Barre de filtres */
#carte-filtres {
    background: #fff;
    border-bottom: 1px solid var(--color-border);
    padding: 10px 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    flex-shrink: 0;
    box-shadow: var(--shadow-sm);
    z-index: 10;
}

/* Panneau latéral info */
#panneau-info {
    width: 300px;
    background: #fff;
    border-right: 1px solid var(--color-border);
    overflow-y: auto;
    flex-shrink: 0;
    box-shadow: var(--shadow-md);
}

/* Carte */
#map {
    flex-grow: 1;
}

/* Bouton flottant Dessiner */
#btn-nouvelle-parcelle {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 999;
    background: var(--color-primary);
    color: #fff;
    border: none;
    border-radius: 50px;
    padding: 12px 22px;
    font-weight: 600;
    font-size: 14px;
    box-shadow: 0 4px 14px rgba(27, 107, 74, 0.4);
    transition: all 0.2s ease;
}

#btn-nouvelle-parcelle:hover {
    background: var(--color-primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(27, 107, 74, 0.45);
}
```

### Légende de la carte

```html
<!-- Légende flottante dans la barre de filtres -->
<div class="d-flex align-items-center gap-3 ms-auto fs-7 fw-semibold text-muted">
    <span>Légende :</span>
    <span class="d-flex align-items-center gap-1">
        <span style="width:10px;height:10px;border-radius:3px;background:#22C55E;display:inline-block;"></span> Bio
    </span>
    <span class="d-flex align-items-center gap-1">
        <span style="width:10px;height:10px;border-radius:3px;background:#EAB308;display:inline-block;"></span> OK
    </span>
    <span class="d-flex align-items-center gap-1">
        <span style="width:10px;height:10px;border-radius:3px;background:#EF4444;display:inline-block;"></span> Déclassifié
    </span>
    <span class="d-flex align-items-center gap-1">
        <span style="width:10px;height:10px;border-radius:3px;background:#94A3B8;display:inline-block;"></span> Indéfini
    </span>
</div>
```

---

## 9. Dashboard

### Structure des stats

```blade
{{-- 4 cartes de statistiques --}}
<div class="row g-5 mb-8">

    {{-- Producteurs actifs --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 h-100" style="border:1px solid var(--color-border)!important;border-radius:var(--radius-lg);">
            <div class="card-body p-6">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="rounded-2 d-flex align-items-center justify-content-center"
                         style="width:44px;height:44px;background:var(--color-primary-light);">
                        <i class="bi bi-people-fill fs-3" style="color:var(--color-primary);"></i>
                    </div>
                    <span class="badge badge-light-success fs-8 fw-bold">+12% ce mois</span>
                </div>
                <div class="fs-1 fw-bolder text-dark mb-1">{{ $stats['producteurs'] }}</div>
                <div class="text-muted fs-7 fw-semibold">Producteurs actifs</div>
                <div class="mt-3">
                    <div class="progress h-4px rounded" style="background:#F0FDF4;">
                        <div class="progress-bar rounded" style="width:72%;background:var(--color-primary);"></div>
                    </div>
                    <div class="text-muted fs-8 mt-1">72% de l'objectif annuel</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Parcelles certifiées --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 h-100" style="border:1px solid var(--color-border)!important;border-radius:var(--radius-lg);">
            <div class="card-body p-6">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="rounded-2 d-flex align-items-center justify-content-center"
                         style="width:44px;height:44px;background:#DCFCE7;">
                        <i class="bi bi-patch-check-fill fs-3" style="color:#16A34A;"></i>
                    </div>
                </div>
                <div class="fs-1 fw-bolder text-dark mb-1">{{ $stats['parcelles_bio'] }}</div>
                <div class="text-muted fs-7 fw-semibold">Parcelles bio certifiées</div>
            </div>
        </div>
    </div>

    {{-- Superficie totale --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 h-100" style="border:1px solid var(--color-border)!important;border-radius:var(--radius-lg);">
            <div class="card-body p-6">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="rounded-2 d-flex align-items-center justify-content-center"
                         style="width:44px;height:44px;background:#DBEAFE;">
                        <i class="bi bi-map-fill fs-3" style="color:#1D4ED8;"></i>
                    </div>
                </div>
                <div class="fs-1 fw-bolder text-dark mb-1">{{ number_format($stats['superficie_totale'], 1) }}</div>
                <div class="text-muted fs-7 fw-semibold">Hectares certifiés</div>
            </div>
        </div>
    </div>

    {{-- Contrôles en attente --}}
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 h-100" style="border:1px solid var(--color-border)!important;border-radius:var(--radius-lg);">
            <div class="card-body p-6">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="rounded-2 d-flex align-items-center justify-content-center"
                         style="width:44px;height:44px;background:#FEF9C3;">
                        <i class="bi bi-hourglass-split fs-3" style="color:#CA8A04;"></i>
                    </div>
                </div>
                <div class="fs-1 fw-bolder text-dark mb-1">{{ $stats['en_attente'] }}</div>
                <div class="text-muted fs-7 fw-semibold">Identifications en attente</div>
            </div>
        </div>
    </div>

</div>
```

---

## 10. États & Feedback

### Messages flash (succès / erreur)

```blade
{{-- resources/views/layouts/partials/flash.blade.php --}}
@if(session('success'))
<div class="alert border-0 mb-6 d-flex align-items-center gap-3 alert-dismissible"
     style="background:#DCFCE7;border-radius:var(--radius-md)!important;border-left:4px solid #16A34A!important;"
     role="alert">
    <i class="bi bi-check-circle-fill fs-4" style="color:#16A34A;flex-shrink:0;"></i>
    <div class="fw-semibold" style="color:#15803D;">{{ session('success') }}</div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert border-0 mb-6 d-flex align-items-center gap-3 alert-dismissible"
     style="background:#FEE2E2;border-radius:var(--radius-md)!important;border-left:4px solid var(--color-danger)!important;"
     role="alert">
    <i class="bi bi-x-circle-fill fs-4 text-danger" style="flex-shrink:0;"></i>
    <div class="fw-semibold text-danger">{{ session('error') }}</div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif
```

### État vide (empty state)

```html
<!-- Quand une liste est vide -->
<div class="text-center py-16 px-4">
    <div class="mb-4" style="font-size:48px; opacity:0.3;">🌿</div>
    <h5 class="fw-bold text-dark mb-2">Aucun producteur trouvé</h5>
    <p class="text-muted fs-7 mb-5">Commencez par ajouter un premier producteur dans cette zone.</p>
    @can('producteurs.create')
    <a href="{{ route('producteurs.create') }}" class="btn btn-sm fw-bold px-5"
       style="background:var(--color-primary);color:#fff;border-radius:var(--radius-sm);">
        <i class="bi bi-plus-lg me-2"></i>Ajouter un producteur
    </a>
    @endcan
</div>
```

### Loading state

```html
<!-- Skeleton loader pour les tableaux -->
<div class="skeleton-row" style="height:56px;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);
    background-size:200% 100%;border-radius:var(--radius-sm);animation:skeleton 1.5s infinite;margin-bottom:2px;">
</div>

<style>
@keyframes skeleton {
    0%   { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
</style>
```

### Modal de confirmation suppression

```html
<div class="modal fade" id="modalConfirmSuppression" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0" style="border-radius:var(--radius-lg);">
            <div class="modal-body text-center p-8">
                <div class="mb-4" style="font-size:40px;">⚠️</div>
                <h5 class="fw-bolder text-dark mb-2">Confirmer la suppression</h5>
                <p class="text-muted fs-7 mb-6">Cette action est irréversible. Toutes les données associées seront supprimées.</p>
                <div class="d-flex gap-3 justify-content-center">
                    <button class="btn btn-light fw-semibold px-6" data-bs-dismiss="modal">Annuler</button>
                    <button id="btn-confirmer-suppression" class="btn btn-danger fw-bold px-6">Supprimer</button>
                </div>
            </div>
        </div>
    </div>
</div>
```

---

## 11. Responsive & Mobile

### Breakpoints utilisés

| Breakpoint | Largeur | Usage |
|-----------|---------|-------|
| `xs` | < 576px | Téléphone portrait — formulaires terrain |
| `sm` | ≥ 576px | Téléphone paysage |
| `md` | ≥ 768px | Tablette portrait |
| `lg` | ≥ 992px | Tablette paysage / petit laptop |
| `xl` | ≥ 1200px | Laptop standard |
| `xxl` | ≥ 1400px | Grand écran |

### Règles mobile importantes

```css
/* Sidebar masquée sur mobile → drawer */
@media (max-width: 991px) {
    .app-sidebar { transform: translateX(-100%); transition: transform 0.3s ease; }
    .app-sidebar.open { transform: translateX(0); }
}

/* Tableaux : scroll horizontal sur mobile */
@media (max-width: 767px) {
    .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }

    /* Masquer colonnes secondaires */
    .table .col-hide-mobile { display: none; }

    /* Agrandir zones de clic */
    .btn-icon { min-width: 44px; min-height: 44px; }
}

/* Formulaires : pleine largeur sur mobile */
@media (max-width: 767px) {
    .row.justify-content-center > .col-xl-8 { padding: 0 10px; }
    .card-body { padding: 16px !important; }
    .fv-row { margin-bottom: 16px !important; }
}

/* Carte : masquer panneau latéral → drawer bas sur mobile */
@media (max-width: 767px) {
    #panneau-info {
        width: 100%;
        max-height: 50vh;
        border-right: none;
        border-top: 1px solid var(--color-border);
        position: fixed;
        bottom: 0;
        left: 0;
        z-index: 100;
        border-radius: var(--radius-lg) var(--radius-lg) 0 0;
    }
}
```

---

## 12. Checklist de validation

Avant de soumettre une page pour validation, vérifier **chaque point** :

### ✅ Design général
- [ ] Police `Plus Jakarta Sans` chargée et appliquée
- [ ] Variable CSS `--color-primary` utilisée (pas de bleu Metronic par défaut)
- [ ] Fond de page `--color-bg-page` (#F8FAFC), pas blanc pur
- [ ] Sidebar sombre avec items actifs en vert
- [ ] Toutes les cards ont `border-radius: var(--radius-lg)`
- [ ] Ombres cohérentes (`--shadow-sm` sur les cards)

### ✅ Typographie
- [ ] Titre de page : `fs-1 fw-bolder`
- [ ] Labels de formulaire : `fs-6 fw-semibold`
- [ ] Texte secondaire : `text-muted fs-7`
- [ ] Codes et IDs en police monospace
- [ ] Nombres dans les tableaux alignés à droite avec `tabular-nums`

### ✅ Composants
- [ ] Badges de statut avec point coloré (●) et fond pastel
- [ ] Bouton principal en `--color-primary` (vert), pas bleu
- [ ] Toggles avec description textuelle à gauche, toggle à droite
- [ ] Empty state défini sur toutes les listes
- [ ] Messages flash intégrés dans le layout principal

### ✅ Formulaires
- [ ] Sections numérotées avec cercles verts
- [ ] Séparateurs entre sections
- [ ] Champs requis marqués avec `*` rouge
- [ ] Messages d'erreur de validation affichés sous chaque champ
- [ ] Bouton Annuler + Bouton Soumettre côte à côte en bas à droite
- [ ] `$request->boolean()` utilisé pour les checkboxes dans le controller

### ✅ Tableau
- [ ] En-têtes en uppercase, petite taille, couleur muted
- [ ] Hover sur les lignes en `--color-primary-light`
- [ ] Actions alignées à droite
- [ ] Pagination avec compte de résultats
- [ ] Barre de filtre dans le `card-header`

### ✅ Carte Mapbox
- [ ] Style satellite `satellite-streets-v12`
- [ ] Couleurs des polygones correspondant aux statuts
- [ ] Légende visible en permanence
- [ ] Panneau info masqué par défaut
- [ ] Bouton flottant vert avec ombre colorée
- [ ] Responsive : panneau en drawer bas sur mobile

### ✅ Accessibilité
- [ ] Contraste texte/fond ≥ 4.5:1 (WCAG AA)
- [ ] Tous les inputs ont un `<label>` avec `for`
- [ ] Boutons icônes ont un `title` ou `aria-label`
- [ ] Modals ont `tabindex="-1"` et gestion du focus
- [ ] Formulaires navigables au clavier (Tab order logique)

### ✅ Performance
- [ ] Images compressées (WebP si possible)
- [ ] GeoJSON de la carte filtré côté serveur (pas tout charger)
- [ ] Selects dynamiques chargés via Ajax, pas en dur dans le HTML
- [ ] `pagination()->withQueryString()` pour conserver les filtres