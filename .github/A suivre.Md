# Système de Gestion et Certification Agricole Biologique
### Stack : Laravel 11 · MySQL 8 · Mapbox GL JS · Spatie Permission

---

## Table des matières

1. [Vue d'ensemble](#1-vue-densemble)
2. [Architecture technique](#2-architecture-technique)
3. [Structure du projet](#3-structure-du-projet)
4. [Base de données — Migrations](#4-base-de-données--migrations)
5. [Models Eloquent & Relations](#5-models-eloquent--relations)
6. [Rôles & Permissions](#6-rôles--permissions)
7. [Controllers & Validation](#7-controllers--validation)
8. [Routes](#8-routes)
9. [Carte Interactive Mapbox](#9-carte-interactive-mapbox)
10. [Interface utilisateur](#10-interface-utilisateur)
11. [Déploiement](#11-déploiement)

---

## 1. Vue d'ensemble

### Description

Application web de gestion des producteurs agricoles, des parcelles et de la certification biologique. Elle trace toute la hiérarchie géographique **(Région → Préfecture → Canton → Village → Zone)**, gère les processus de certification bio par campagne, et propose une **carte interactive Mapbox** pour visualiser et dessiner les contours des parcelles terrain.

### Objectifs principaux

- Gérer la hiérarchie géographique des zones d'intervention
- Enregistrer et suivre les producteurs et leurs organisations paysannes
- Gérer les parcelles agricoles et leur certification biologique
- Réaliser les contrôles et identifications par campagne
- Visualiser les parcelles sur une carte satellite avec filtres
- Dessiner et modifier les contours des terrains directement sur la carte
- Administrer les utilisateurs et contrôleurs par zone

### Modules de l'application

| Module | Description |
|--------|-------------|
| **Subdivisions** | Régions, Préfectures, Cantons, Villages, Zones |
| **Organisations** | Organisations paysannes |
| **Producteurs** | Gestion des producteurs et leur suivi |
| **Parcelles** | Parcelles, Arbres, Identifications, Contrôles |
| **Cultures** | Types de cultures certifiables |
| **Carte** | Visualisation et dessin des terrains sur Mapbox |
| **Utilisateurs** | Gestion des comptes et rôles |
| **Paramètres** | Configuration système |

---

## 2. Architecture technique

### Stack complet

| Couche | Technologie | Usage |
|--------|-------------|-------|
| **Backend** | Laravel 11 | Framework principal |
| **Base de données** | MySQL 8 | Stockage + types spatiaux |
| **Auth** | Laravel Breeze / Jetstream | Authentification |
| **Permissions** | Spatie Laravel Permission | Rôles & droits |
| **Frontend** | Blade + Alpine.js | Vues et interactions légères |
| **UI Kit** | Metronic | Composants visuels |
| **Carte** | Mapbox GL JS v3 | Carte satellite interactive |
| **Dessin carte** | Mapbox GL Draw | Polygones terrains |
| **Spatial** | grimzy/laravel-mysql-spatial | Types GEOMETRY MySQL |
| **Select** | Tom Select | Selects dynamiques Ajax |
| **Serveur** | Nginx + PHP-FPM | Production |
| **Conteneurs** | Docker + Laravel Sail | Dev & prod |

### Packages Laravel

```bash
# Obligatoires
composer require spatie/laravel-permission
composer require grimzy/laravel-mysql-spatial
composer require spatie/laravel-query-builder

# Recommandés
composer require maatwebsite/excel          # Export Excel
composer require barryvdh/laravel-dompdf    # Export PDF
composer require spatie/laravel-activitylog # Audit logs
composer require spatie/laravel-medialibrary # Photos parcelles

# Développement
composer require --dev barryvdh/laravel-debugbar
composer require --dev laravel/telescope
```

### CDN Frontend (layout principal)

```html
<!-- Metronic / Bootstrap -->
<link href="/assets/css/style.bundle.css" rel="stylesheet">

<!-- Mapbox GL JS -->
<link href="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.css" rel="stylesheet">
<script src="https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.js"></script>

<!-- Mapbox Draw -->
<link href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.4.3/mapbox-gl-draw.css" rel="stylesheet">
<script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.4.3/mapbox-gl-draw.js"></script>

<!-- Tom Select -->
<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

<!-- Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
```

---

## 3. Structure du projet

```
app/
├── Models/
│   ├── Region.php
│   ├── Prefecture.php
│   ├── Canton.php
│   ├── Village.php
│   ├── Zone.php
│   ├── OrganisationPaysanne.php
│   ├── Producteur.php
│   ├── Culture.php
│   ├── Parcelle.php
│   ├── Arbre.php
│   ├── Identification.php
│   ├── Controle.php
│   └── Parametre.php
├── Models/Scopes/
│   └── ZoneScope.php
├── Http/
│   ├── Controllers/
│   │   ├── DashboardController.php
│   │   ├── CarteController.php
│   │   ├── Areas/
│   │   │   ├── RegionController.php
│   │   │   ├── PrefectureController.php
│   │   │   ├── CantonController.php
│   │   │   ├── VillageController.php
│   │   │   └── ZoneController.php
│   │   ├── OrganisationPaysanneController.php
│   │   ├── ProducteurController.php
│   │   ├── CultureController.php
│   │   ├── ParcelleController.php
│   │   ├── ArbreController.php
│   │   ├── IdentificationController.php
│   │   ├── ControleController.php
│   │   ├── UserController.php
│   │   └── ParametreController.php
│   └── Requests/
│       ├── StoreParcelleRequest.php
│       ├── UpdateParcelleRequest.php
│       ├── StoreProducteurRequest.php
│       ├── StoreIdentificationRequest.php
│       └── StoreControleRequest.php
database/
├── migrations/
├── seeders/
│   ├── DatabaseSeeder.php
│   └── RolesAndPermissionsSeeder.php
└── factories/
resources/
├── views/
│   ├── layouts/
│   │   ├── app.blade.php
│   │   └── auth.blade.php
│   ├── components/
│   │   ├── select-dynamic.blade.php
│   │   ├── toggle-switch.blade.php
│   │   ├── stat-card.blade.php
│   │   └── confirm-delete.blade.php
│   ├── areas/
│   │   ├── regions/
│   │   ├── prefectures/
│   │   ├── cantons/
│   │   ├── villages/
│   │   └── zones/
│   ├── organisations/
│   ├── producteurs/
│   ├── parcelles/
│   ├── cultures/
│   ├── carte/
│   │   └── index.blade.php
│   ├── users/
│   └── parametres/
public/
└── js/
    └── carte.js
routes/
├── web.php
└── api.php
config/
└── services.php   # token Mapbox
```

---

## 4. Base de données — Migrations

### 4.1 Régions

```php
Schema::create('regions', function (Blueprint $table) {
    $table->id();
    $table->string('nom', 100)->unique();
    $table->timestamps();
});
```

### 4.2 Préfectures

```php
Schema::create('prefectures', function (Blueprint $table) {
    $table->id();
    $table->foreignId('region_id')->constrained()->cascadeOnDelete();
    $table->string('nom', 100);
    $table->string('code', 2); // auto-généré : 2 premières lettres du nom
    $table->timestamps();
    $table->unique(['region_id', 'code']);
});
```

### 4.3 Cantons

```php
Schema::create('cantons', function (Blueprint $table) {
    $table->id();
    $table->foreignId('region_id')->constrained()->cascadeOnDelete();
    $table->foreignId('prefecture_id')->constrained()->cascadeOnDelete();
    $table->string('nom', 100);
    $table->string('zone', 50)->nullable();
    $table->timestamps();
});
```

### 4.4 Villages

```php
Schema::create('villages', function (Blueprint $table) {
    $table->id();
    $table->foreignId('region_id')->constrained()->cascadeOnDelete();
    $table->foreignId('prefecture_id')->constrained()->cascadeOnDelete();
    $table->foreignId('canton_id')->constrained()->cascadeOnDelete();
    $table->foreignId('controleur_id')->nullable()->constrained('users')->nullOnDelete();
    $table->string('nom', 100);
    $table->string('zone', 50)->nullable();
    $table->timestamps();
});
```

### 4.5 Zones

```php
Schema::create('zones', function (Blueprint $table) {
    $table->id();
    $table->string('nom', 100)->unique();
    $table->string('mot_de_passe'); // bcrypt
    $table->timestamps();
});
```

### 4.6 Organisations Paysannes

```php
Schema::create('organisation_paysannes', function (Blueprint $table) {
    $table->id();
    $table->string('nom', 150);
    $table->foreignId('zone_id')->constrained()->cascadeOnDelete();
    $table->foreignId('village_id')->constrained()->cascadeOnDelete();
    $table->foreignId('controleur_id')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamps();
});
```

### 4.7 Producteurs

```php
Schema::create('producteurs', function (Blueprint $table) {
    $table->id();
    $table->string('code', 20)->unique();             // auto-généré
    $table->string('nom', 100);
    $table->string('prenom', 100);
    $table->foreignId('zone_id')->constrained()->cascadeOnDelete();
    $table->foreignId('village_id')->constrained()->cascadeOnDelete();
    $table->foreignId('organisation_paysanne_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('controleur_id')->nullable()->constrained('users')->nullOnDelete();
    $table->boolean('est_actif')->default(true);
    $table->timestamps();
});
```

### 4.8 Cultures

```php
Schema::create('cultures', function (Blueprint $table) {
    $table->id();
    $table->string('nom', 100)->unique();
    $table->timestamps();
});
```

### 4.9 Parcelles

```php
Schema::create('parcelles', function (Blueprint $table) {
    $table->id();
    $table->unsignedInteger('indice');                // auto-incrémenté par producteur
    $table->foreignId('producteur_id')->constrained()->cascadeOnDelete();
    $table->foreignId('village_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('culture_id')->nullable()->constrained()->nullOnDelete();

    // Superficie
    $table->decimal('superficie', 8, 4)->nullable();
    $table->decimal('superficie_bio', 8, 3)->nullable();
    $table->decimal('rendement_bio', 8, 3)->nullable();
    $table->unsignedBigInteger('volume_production')->nullable();

    // Caractéristiques
    $table->enum('niveau_pente', ['WITHOUT', 'SMALL', 'MEDIUM', 'HIGH'])->nullable();
    $table->enum('type_culture', ['SINGLE', 'ASSOCIATIVE', 'SPACER', 'PURE', 'STOLEN'])->nullable();
    $table->enum('type_employes', ['SEASONAL', 'PERMANENT'])->nullable();
    $table->enum('approbation_production', ['BIO', 'OK', 'DECLASSIFIED'])->nullable();

    // Booléens
    $table->boolean('bio')->default(false);
    $table->boolean('a_cours_eau')->default(false);
    $table->boolean('maisons_proximite')->default(false);
    $table->boolean('transformation_ferme')->default(false);

    // Géographie (Mapbox)
    $table->point('centre')->nullable();
    $table->polygon('contour')->nullable();

    $table->timestamps();
    $table->unique(['producteur_id', 'indice']);
    $table->spatialIndex('contour');
});
```

### 4.10 Arbres

```php
Schema::create('arbres', function (Blueprint $table) {
    $table->id();
    $table->foreignId('parcelle_id')->constrained()->cascadeOnDelete();
    $table->foreignId('culture_id')->constrained()->cascadeOnDelete();
    $table->unsignedInteger('nombre');
    $table->timestamps();
});
```

### 4.11 Identifications

```php
Schema::create('identifications', function (Blueprint $table) {
    $table->id();
    $table->string('numero', 50);
    $table->foreignId('producteur_id')->constrained()->cascadeOnDelete();
    $table->foreignId('controleur_id')->nullable()->constrained('users')->nullOnDelete();
    $table->decimal('superficie', 8, 4)->nullable();
    $table->enum('statut', ['EN_ATTENTE', 'APPROUVE', 'REJETE'])->default('EN_ATTENTE');
    $table->enum('approbation', ['BIO', 'OK', 'DECLASSIFIED'])->nullable();
    $table->string('campagne', 20);
    $table->timestamps();
    $table->unique(['numero', 'campagne']);
});
```

### 4.12 Contrôles

```php
Schema::create('controles', function (Blueprint $table) {
    $table->id();
    $table->string('numero', 50);
    $table->foreignId('parcelle_id')->constrained()->cascadeOnDelete();
    $table->foreignId('producteur_id')->constrained()->cascadeOnDelete();
    $table->foreignId('culture_id')->constrained()->cascadeOnDelete();
    $table->foreignId('controleur_id')->nullable()->constrained('users')->nullOnDelete();
    $table->decimal('superficie_parcelle', 8, 4)->nullable();
    $table->decimal('superficie_bio', 8, 3)->nullable();
    $table->string('campagne', 20);
    $table->timestamps();
});
```

### 4.13 Paramètres

```php
Schema::create('parametres', function (Blueprint $table) {
    $table->id();
    $table->string('nom', 100)->unique();
    $table->text('valeur');
    $table->timestamp('date_modification')->useCurrent()->useCurrentOnUpdate();
});
```

### 4.14 Extension Users (migration)

```php
Schema::table('users', function (Blueprint $table) {
    $table->string('prenom', 100)->nullable()->after('name');
    $table->enum('type', ['ADMIN', 'SUPERVISEUR', 'CONTROLEUR'])->default('CONTROLEUR')->after('prenom');
    $table->foreignId('zone_id')->nullable()->constrained()->nullOnDelete()->after('type');
    $table->boolean('est_actif')->default(true)->after('zone_id');
});
```

---

## 5. Models Eloquent & Relations

### Region.php

```php
class Region extends Model
{
    protected $fillable = ['nom'];

    public function prefectures(): HasMany { return $this->hasMany(Prefecture::class); }
    public function cantons(): HasMany     { return $this->hasMany(Canton::class); }
    public function villages(): HasMany    { return $this->hasMany(Village::class); }
}
```

### Prefecture.php

```php
class Prefecture extends Model
{
    protected $fillable = ['region_id', 'nom', 'code'];

    // Auto-génération du code (2 premières lettres)
    protected static function booted(): void
    {
        static::creating(function (Prefecture $p) {
            $p->code = strtoupper(substr($p->nom, 0, 2));
        });
    }

    public function region(): BelongsTo  { return $this->belongsTo(Region::class); }
    public function cantons(): HasMany   { return $this->hasMany(Canton::class); }
    public function villages(): HasMany  { return $this->hasMany(Village::class); }
}
```

### Canton.php

```php
class Canton extends Model
{
    protected $fillable = ['region_id', 'prefecture_id', 'nom', 'zone'];

    public function region(): BelongsTo     { return $this->belongsTo(Region::class); }
    public function prefecture(): BelongsTo { return $this->belongsTo(Prefecture::class); }
    public function villages(): HasMany     { return $this->hasMany(Village::class); }
}
```

### Village.php

```php
class Village extends Model
{
    protected $fillable = ['region_id', 'prefecture_id', 'canton_id', 'controleur_id', 'nom', 'zone'];

    public function region(): BelongsTo     { return $this->belongsTo(Region::class); }
    public function prefecture(): BelongsTo { return $this->belongsTo(Prefecture::class); }
    public function canton(): BelongsTo     { return $this->belongsTo(Canton::class); }
    public function controleur(): BelongsTo { return $this->belongsTo(User::class, 'controleur_id'); }
    public function producteurs(): HasMany  { return $this->hasMany(Producteur::class); }
}
```

### Zone.php

```php
class Zone extends Model
{
    protected $fillable = ['nom', 'mot_de_passe'];
    protected $hidden   = ['mot_de_passe'];

    protected static function booted(): void
    {
        static::creating(function (Zone $z) {
            $z->mot_de_passe = bcrypt($z->mot_de_passe);
        });
    }

    public function producteurs(): HasMany     { return $this->hasMany(Producteur::class); }
    public function organisations(): HasMany   { return $this->hasMany(OrganisationPaysanne::class); }
}
```

### OrganisationPaysanne.php

```php
class OrganisationPaysanne extends Model
{
    protected $fillable = ['nom', 'zone_id', 'village_id', 'controleur_id'];

    public function zone(): BelongsTo       { return $this->belongsTo(Zone::class); }
    public function village(): BelongsTo    { return $this->belongsTo(Village::class); }
    public function controleur(): BelongsTo { return $this->belongsTo(User::class, 'controleur_id'); }
    public function producteurs(): HasMany  { return $this->hasMany(Producteur::class); }
}
```

### Producteur.php

```php
class Producteur extends Model
{
    protected $fillable = [
        'code', 'nom', 'prenom', 'zone_id', 'village_id',
        'organisation_paysanne_id', 'controleur_id', 'est_actif'
    ];

    protected $casts = ['est_actif' => 'boolean'];

    protected static function booted(): void
    {
        static::addGlobalScope(new ZoneScope());

        static::creating(function (Producteur $p) {
            $last = static::withoutGlobalScopes()->max('id') ?? 0;
            $p->code = 'PROD-' . str_pad($last + 1, 5, '0', STR_PAD_LEFT);
        });
    }

    public function zone(): BelongsTo          { return $this->belongsTo(Zone::class); }
    public function village(): BelongsTo       { return $this->belongsTo(Village::class); }
    public function organisation(): BelongsTo  { return $this->belongsTo(OrganisationPaysanne::class, 'organisation_paysanne_id'); }
    public function controleur(): BelongsTo    { return $this->belongsTo(User::class, 'controleur_id'); }
    public function parcelles(): HasMany       { return $this->hasMany(Parcelle::class); }
    public function identifications(): HasMany { return $this->hasMany(Identification::class); }

    public function scopeActif(Builder $q): Builder   { return $q->where('est_actif', true); }
    public function scopeDeZone(Builder $q, ?int $id): Builder
    {
        return $id ? $q->where('zone_id', $id) : $q;
    }
}
```

### Parcelle.php

```php
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Grimzy\LaravelMysqlSpatial\Types\{Polygon, Point};

class Parcelle extends Model
{
    use SpatialTrait;

    protected $fillable = [
        'producteur_id', 'village_id', 'culture_id', 'indice',
        'superficie', 'superficie_bio', 'rendement_bio', 'volume_production',
        'niveau_pente', 'type_culture', 'type_employes', 'approbation_production',
        'bio', 'a_cours_eau', 'maisons_proximite', 'transformation_ferme',
        'centre', 'contour',
    ];

    protected $spatialFields = ['centre', 'contour'];

    protected $casts = [
        'bio'                  => 'boolean',
        'a_cours_eau'          => 'boolean',
        'maisons_proximite'    => 'boolean',
        'transformation_ferme' => 'boolean',
    ];

    protected static function booted(): void
    {
        // Auto-incrément indice par producteur
        static::creating(function (Parcelle $p) {
            $max = static::where('producteur_id', $p->producteur_id)->max('indice') ?? 0;
            $p->indice = $max + 1;
        });
    }

    // Couleur selon approbation (utilisée par la carte Mapbox)
    public function getCouleurCarteAttribute(): string
    {
        return match ($this->approbation_production) {
            'BIO'          => '#22c55e',
            'OK'           => '#eab308',
            'DECLASSIFIED' => '#ef4444',
            default        => '#94a3b8',
        };
    }

    public function producteur(): BelongsTo    { return $this->belongsTo(Producteur::class); }
    public function village(): BelongsTo       { return $this->belongsTo(Village::class); }
    public function culture(): BelongsTo       { return $this->belongsTo(Culture::class); }
    public function arbres(): HasMany          { return $this->hasMany(Arbre::class); }
    public function controles(): HasMany       { return $this->hasMany(Controle::class); }
    public function identifications(): HasMany { return $this->hasMany(Identification::class, 'producteur_id', 'producteur_id'); }
}
```

### Identification.php

```php
class Identification extends Model
{
    protected $fillable = [
        'numero', 'producteur_id', 'controleur_id',
        'superficie', 'statut', 'approbation', 'campagne'
    ];

    public function producteur(): BelongsTo { return $this->belongsTo(Producteur::class); }
    public function controleur(): BelongsTo { return $this->belongsTo(User::class, 'controleur_id'); }
}
```

### Controle.php

```php
class Controle extends Model
{
    protected $fillable = [
        'numero', 'parcelle_id', 'producteur_id', 'culture_id',
        'controleur_id', 'superficie_parcelle', 'superficie_bio', 'campagne'
    ];

    public function parcelle(): BelongsTo   { return $this->belongsTo(Parcelle::class); }
    public function producteur(): BelongsTo { return $this->belongsTo(Producteur::class); }
    public function culture(): BelongsTo    { return $this->belongsTo(Culture::class); }
    public function controleur(): BelongsTo { return $this->belongsTo(User::class, 'controleur_id'); }
}
```

### ZoneScope.php (Global Scope)

```php
// app/Models/Scopes/ZoneScope.php
class ZoneScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();
        // Admin voit tout — contrôleur voit seulement sa zone
        if ($user && !$user->hasRole('admin') && $user->zone_id) {
            $builder->where('zone_id', $user->zone_id);
        }
    }
}
```

> Appliquer ce scope sur : `Producteur`, `OrganisationPaysanne`, et `Village` via `static::addGlobalScope(new ZoneScope())` dans leur `booted()`.

---

## 6. Rôles & Permissions

### Installation Spatie

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

### Seeder des rôles

```php
// database/seeders/RolesAndPermissionsSeeder.php
public function run(): void
{
    app()[PermissionRegistrar::class]->forgetCachedPermissions();

    $permissions = [
        'regions.view', 'regions.create', 'regions.edit', 'regions.delete',
        'prefectures.view', 'prefectures.create', 'prefectures.edit', 'prefectures.delete',
        'cantons.view', 'cantons.create', 'cantons.edit', 'cantons.delete',
        'villages.view', 'villages.create', 'villages.edit', 'villages.delete',
        'zones.view', 'zones.create', 'zones.edit', 'zones.delete',
        'organisations.view', 'organisations.create', 'organisations.edit', 'organisations.delete',
        'producteurs.view', 'producteurs.create', 'producteurs.edit', 'producteurs.delete',
        'parcelles.view', 'parcelles.create', 'parcelles.edit', 'parcelles.delete',
        'parcelles.contour',       // Dessiner / modifier les contours sur la carte
        'identifications.view', 'identifications.create', 'identifications.approve',
        'controles.view', 'controles.create',
        'carte.view',
        'users.view', 'users.create', 'users.edit', 'users.delete',
        'parametres.view', 'parametres.edit',
    ];

    foreach ($permissions as $perm) {
        Permission::firstOrCreate(['name' => $perm]);
    }

    // ADMIN — tout
    Role::firstOrCreate(['name' => 'admin'])
        ->givePermissionTo(Permission::all());

    // SUPERVISEUR — lecture + validation identifications + carte
    Role::firstOrCreate(['name' => 'superviseur'])
        ->givePermissionTo([
            'regions.view', 'prefectures.view', 'cantons.view', 'villages.view', 'zones.view',
            'organisations.view', 'producteurs.view',
            'parcelles.view', 'identifications.view', 'identifications.approve',
            'controles.view', 'carte.view',
        ]);

    // CONTROLEUR — CRUD sur sa zone + carte + dessin contours
    Role::firstOrCreate(['name' => 'controleur'])
        ->givePermissionTo([
            'villages.view', 'organisations.view', 'organisations.create', 'organisations.edit',
            'producteurs.view', 'producteurs.create', 'producteurs.edit',
            'parcelles.view', 'parcelles.create', 'parcelles.edit', 'parcelles.contour',
            'identifications.view', 'identifications.create',
            'controles.view', 'controles.create',
            'carte.view',
        ]);
}
```

### Tableau récapitulatif

| Permission | Admin | Superviseur | Contrôleur |
|-----------|-------|-------------|------------|
| Gérer régions / préfectures / cantons | ✅ | 👁 | — |
| Gérer villages / zones | ✅ | 👁 | 👁 |
| Gérer organisations | ✅ | 👁 | ✅ |
| Gérer producteurs | ✅ | 👁 | ✅ (sa zone) |
| Gérer parcelles | ✅ | 👁 | ✅ (sa zone) |
| Dessiner contours carte | ✅ | — | ✅ |
| Valider identifications | ✅ | ✅ | — |
| Gérer utilisateurs | ✅ | — | — |
| Modifier paramètres | ✅ | — | — |

---

## 7. Controllers & Validation

### StoreParcelleRequest.php

```php
class StoreParcelleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'producteur_id'          => 'required|exists:producteurs,id',
            'village_id'             => 'nullable|exists:villages,id',
            'culture_id'             => 'nullable|exists:cultures,id',
            'superficie'             => 'nullable|numeric|min:0',
            'superficie_bio'         => 'nullable|numeric|min:0',
            'rendement_bio'          => 'nullable|numeric|min:0',
            'volume_production'      => 'nullable|integer|min:0',
            'niveau_pente'           => 'nullable|in:WITHOUT,SMALL,MEDIUM,HIGH',
            'type_culture'           => 'nullable|in:SINGLE,ASSOCIATIVE,SPACER,PURE,STOLEN',
            'type_employes'          => 'nullable|in:SEASONAL,PERMANENT',
            'approbation_production' => 'nullable|in:BIO,OK,DECLASSIFIED',
            'bio'                    => 'boolean',
            'a_cours_eau'            => 'boolean',
            'maisons_proximite'      => 'boolean',
            'transformation_ferme'   => 'boolean',
            'contour_geojson'        => 'nullable|json',
        ];
    }
}
```

### ParcelleController.php

```php
class ParcelleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:parcelles.view')->only(['index', 'show']);
        $this->middleware('permission:parcelles.create')->only(['create', 'store']);
        $this->middleware('permission:parcelles.edit')->only(['edit', 'update']);
        $this->middleware('permission:parcelles.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $parcelles = Parcelle::query()
            ->with(['producteur', 'village', 'culture'])
            ->when($request->producteur_id, fn($q, $v) => $q->where('producteur_id', $v))
            ->when($request->bio, fn($q) => $q->where('bio', true))
            ->when($request->campagne, fn($q, $v) => $q->whereHas('identifications', fn($q) => $q->where('campagne', $v)))
            ->latest()
            ->paginate(20);

        return view('parcelles.index', compact('parcelles'));
    }

    public function store(StoreParcelleRequest $request)
    {
        $data = $request->validated();

        // Traitement du contour GeoJSON si fourni
        if ($request->filled('contour_geojson')) {
            $geojson = json_decode($request->contour_geojson, true);
            if (isset($geojson['type']) && $geojson['type'] === 'Polygon') {
                $coords = $geojson['coordinates'][0];
                $data['contour'] = Polygon::fromJson($request->contour_geojson);
                $data['centre']  = new Point(
                    collect($coords)->avg(fn($c) => $c[1]),
                    collect($coords)->avg(fn($c) => $c[0])
                );
            }
        }

        // Les checkboxes HTML retournent "on" ou null
        foreach (['bio', 'a_cours_eau', 'maisons_proximite', 'transformation_ferme'] as $field) {
            $data[$field] = $request->boolean($field);
        }

        $parcelle = Parcelle::create($data);

        return redirect()->route('parcelles.show', $parcelle)
            ->with('success', 'Parcelle créée avec succès.');
    }
}
```

### ProducteurController — endpoint filter

```php
// Endpoint pour les <select> dynamiques
public function filter(Request $request)
{
    return Producteur::actif()
        ->when($request->zone_id, fn($q, $v) => $q->where('zone_id', $v))
        ->when($request->village_id, fn($q, $v) => $q->where('village_id', $v))
        ->get(['id', 'nom', 'prenom', 'code'])
        ->map(fn($p) => [
            'value' => $p->id,
            'label' => "{$p->code} — {$p->nom} {$p->prenom}"
        ]);
}
```

### CarteController.php

```php
class CarteController extends Controller
{
    public function index()
    {
        $this->authorize('carte.view');
        $zones       = Zone::orderBy('nom')->get(['id', 'nom']);
        $producteurs = Producteur::actif()->orderBy('nom')->get(['id', 'nom', 'prenom', 'code']);

        return view('carte.index', [
            'zones'       => $zones,
            'producteurs' => $producteurs,
            'mapboxToken' => config('services.mapbox.token'),
        ]);
    }

    // Retourne un GeoJSON FeatureCollection
    public function geojson(Request $request)
    {
        $parcelles = Parcelle::query()
            ->with(['producteur:id,nom,prenom,code', 'culture:id,nom', 'village:id,nom'])
            ->whereNotNull('contour')
            ->when($request->zone_id, fn($q, $v) => $q->whereHas('producteur', fn($q) => $q->where('zone_id', $v)))
            ->when($request->producteur_id, fn($q, $v) => $q->where('producteur_id', $v))
            ->when($request->bio, fn($q) => $q->where('bio', true))
            ->get();

        return response()->json([
            'type'     => 'FeatureCollection',
            'features' => $parcelles->map(fn(Parcelle $p) => [
                'type'       => 'Feature',
                'id'         => $p->id,
                'geometry'   => json_decode($p->contour->toJson()),
                'properties' => [
                    'id'             => $p->id,
                    'indice'         => $p->indice,
                    'producteur'     => "{$p->producteur->nom} {$p->producteur->prenom}",
                    'code_producteur'=> $p->producteur->code,
                    'village'        => $p->village?->nom,
                    'culture'        => $p->culture?->nom,
                    'superficie'     => $p->superficie,
                    'superficie_bio' => $p->superficie_bio,
                    'bio'            => $p->bio,
                    'approbation'    => $p->approbation_production,
                    'couleur'        => $p->couleur_carte,
                ],
            ])
        ]);
    }

    public function saveContour(Request $request, Parcelle $parcelle)
    {
        $this->middleware('permission:parcelles.contour');
        $request->validate(['geojson' => 'required|json']);

        $geojson = json_decode($request->geojson, true);
        if ($geojson['type'] !== 'Polygon') {
            return response()->json(['error' => 'Un Polygon est requis.'], 422);
        }

        $coords = $geojson['coordinates'][0];
        $parcelle->update([
            'contour' => Polygon::fromJson($request->geojson),
            'centre'  => new Point(
                collect($coords)->avg(fn($c) => $c[1]),
                collect($coords)->avg(fn($c) => $c[0])
            ),
        ]);

        return response()->json(['success' => true, 'message' => 'Contour sauvegardé.']);
    }

    public function deleteContour(Parcelle $parcelle)
    {
        $this->middleware('permission:parcelles.contour');
        $parcelle->update(['contour' => null, 'centre' => null]);
        return response()->json(['success' => true]);
    }
}
```

---

## 8. Routes

```php
// routes/web.php
Route::middleware(['auth'])->group(function () {

    Route::get('/', DashboardController::class)->name('dashboard');

    // ── Zones géographiques ──────────────────────────────────────
    Route::prefix('areas')->name('areas.')->group(function () {
        Route::resource('regions', RegionController::class);
        Route::resource('prefectures', PrefectureController::class);
        Route::resource('cantons', CantonController::class);
        Route::resource('villages', VillageController::class);
        Route::resource('zones', ZoneController::class);

        // Selects dynamiques
        Route::get('villages/filter', [VillageController::class, 'filter'])->name('villages.filter');
        Route::get('cantons/filter',  [CantonController::class, 'filter'])->name('cantons.filter');
    });

    // ── Organisations ────────────────────────────────────────────
    Route::resource('organisations', OrganisationPaysanneController::class);

    // ── Producteurs ──────────────────────────────────────────────
    Route::resource('producteurs', ProducteurController::class);
    Route::get('producteurs/filter', [ProducteurController::class, 'filter'])->name('producteurs.filter');

    // ── Cultures ─────────────────────────────────────────────────
    Route::resource('cultures', CultureController::class);

    // ── Parcelles ────────────────────────────────────────────────
    Route::resource('parcelles', ParcelleController::class);
    Route::prefix('parcelles/{parcelle}')->name('parcelles.')->group(function () {
        Route::resource('arbres', ArbreController::class)->shallow();
        Route::resource('identifications', IdentificationController::class)->shallow();
        Route::resource('controles', ControleController::class)->shallow();
    });

    // ── Carte Mapbox ─────────────────────────────────────────────
    Route::prefix('carte')->name('carte.')->group(function () {
        Route::get('/',                                  [CarteController::class, 'index'])->name('index');
        Route::get('/geojson',                           [CarteController::class, 'geojson'])->name('geojson');
        Route::post('/parcelles/{parcelle}/contour',     [CarteController::class, 'saveContour'])->name('save-contour');
        Route::delete('/parcelles/{parcelle}/contour',   [CarteController::class, 'deleteContour'])->name('delete-contour');
    });

    // ── Admin uniquement ─────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('parametres', ParametreController::class)->only(['index', 'edit', 'update']);
    });
});
```

---

## 9. Carte Interactive Mapbox

### Vue d'ensemble de la carte

```
┌─────────────────────────────────────────────────────────────┐
│  [Zone ▼]  [Producteur ▼]  [Bio ✓]  [Appliquer] [Reset]     │
│  Légende : 🟩 Bio  🟨 OK  🟥 Déclassifié  ⬜ Indéfini       │
├──────────────┬──────────────────────────────────────────────┤
│ PANNEAU INFO │            CARTE SATELLITE                   │
│              │                                              │
│ Producteur   │    🟩──────────────────┐                     │
│ Code         │    │   Parcelle A      │                     │
│ Village      │    │   2.5 ha          │                     │
│ Culture      │    └──────────────────┘                      │
│ Superficie   │         🟨──────┐                            │
│              │         │  B   │                             │
│ [Voir fiche] │         └──────┘                             │
│ [✏ Modifier] │                                              │
│ [🗑 Contour] │  [⬡ Dessiner] [✏ Modifier] [🗑 Suppr]       │
└──────────────┴──────────────────────────────────────────────┘
```

### Configuration

```php
// config/services.php
'mapbox' => [
    'token' => env('MAPBOX_TOKEN'),
],
```

```env
MAPBOX_TOKEN=pk.eyJ1IjoieW91cnVzZXJuYW1lIiwiYSI6ImNs...
```

### public/js/carte.js

```javascript
// ── Init Mapbox ──────────────────────────────────────────────
mapboxgl.accessToken = MAPBOX_TOKEN;

const map = new mapboxgl.Map({
    container: 'map',
    style: 'mapbox://styles/mapbox/satellite-streets-v12',
    center: [1.2314, 7.5460], // À ajuster selon le pays
    zoom: 8,
});

map.addControl(new mapboxgl.NavigationControl(), 'top-right');
map.addControl(new mapboxgl.ScaleControl(), 'bottom-left');
map.addControl(new mapboxgl.FullscreenControl(), 'top-right');

// ── Mapbox Draw ──────────────────────────────────────────────
const draw = new MapboxDraw({
    displayControlsDefault: false,
    controls: { polygon: true, trash: true },
    styles: [
        { id: 'gl-draw-polygon-fill',
          type: 'fill',
          filter: ['all', ['==', '$type', 'Polygon'], ['!=', 'mode', 'static']],
          paint: { 'fill-color': '#ff9f43', 'fill-opacity': 0.3 } },
        { id: 'gl-draw-polygon-stroke',
          type: 'line',
          filter: ['all', ['==', '$type', 'Polygon'], ['!=', 'mode', 'static']],
          paint: { 'line-color': '#ff9f43', 'line-width': 2 } },
    ]
});
map.addControl(draw, 'top-left');

// ── État ─────────────────────────────────────────────────────
let parcelleCourante = null;
let modeDessin = false;

// ── Chargement GeoJSON ───────────────────────────────────────
async function chargerParcelles(filtres = {}) {
    const params   = new URLSearchParams(filtres);
    const response = await fetch(`${GEOJSON_URL}?${params}`);
    const geojson  = await response.json();

    if (map.getSource('parcelles')) {
        map.getSource('parcelles').setData(geojson);
    } else {
        map.addSource('parcelles', { type: 'geojson', data: geojson });

        map.addLayer({
            id: 'parcelles-fill', type: 'fill', source: 'parcelles',
            paint: { 'fill-color': ['get', 'couleur'], 'fill-opacity': 0.45 }
        });
        map.addLayer({
            id: 'parcelles-stroke', type: 'line', source: 'parcelles',
            paint: { 'line-color': ['get', 'couleur'], 'line-width': 2 }
        });
        map.addLayer({
            id: 'parcelles-label', type: 'symbol', source: 'parcelles',
            layout: {
                'text-field': ['concat', ['to-string', ['get', 'superficie']], ' ha'],
                'text-size': 11,
                'text-font': ['Open Sans Bold', 'Arial Unicode MS Bold'],
            },
            paint: { 'text-color': '#fff', 'text-halo-color': '#000', 'text-halo-width': 1 }
        });
    }

    // Zoom automatique sur les résultats
    if (geojson.features.length > 0) {
        const bounds = new mapboxgl.LngLatBounds();
        geojson.features.forEach(f => {
            f.geometry.coordinates[0].forEach(coord => bounds.extend(coord));
        });
        map.fitBounds(bounds, { padding: 60 });
    }
}

// ── Clic sur parcelle → panneau info ─────────────────────────
map.on('click', 'parcelles-fill', (e) => {
    const props = e.features[0].properties;
    parcelleCourante = props.id;

    document.getElementById('panneau-content').innerHTML = `
        <span class="badge badge-light-${props.bio ? 'success' : 'secondary'}">${props.bio ? 'Bio' : 'Non bio'}</span>
        ${props.approbation ? `<span class="badge badge-light-info ms-1">${props.approbation}</span>` : ''}
        <table class="table table-sm table-borderless mt-2">
            <tr><td class="text-muted">Producteur</td><td><strong>${props.producteur}</strong></td></tr>
            <tr><td class="text-muted">Code</td><td>${props.code_producteur}</td></tr>
            <tr><td class="text-muted">Village</td><td>${props.village ?? '—'}</td></tr>
            <tr><td class="text-muted">Culture</td><td>${props.culture ?? '—'}</td></tr>
            <tr><td class="text-muted">Superficie</td><td>${props.superficie ? props.superficie + ' ha' : '—'}</td></tr>
            <tr><td class="text-muted">Superficie bio</td><td>${props.superficie_bio ? props.superficie_bio + ' ha' : '—'}</td></tr>
        </table>
    `;

    document.getElementById('btn-voir-parcelle').href = `/parcelles/${props.id}`;
    document.getElementById('panneau-info').classList.remove('d-none');
});

map.on('mouseenter', 'parcelles-fill', () => { map.getCanvas().style.cursor = 'pointer'; });
map.on('mouseleave', 'parcelles-fill', () => { map.getCanvas().style.cursor = ''; });

// ── Sauvegarde contour ───────────────────────────────────────
async function sauvegarderContour() {
    const data = draw.getAll();
    if (!data.features.length) return alert('Aucun polygone dessiné.');

    const response = await fetch(`/carte/parcelles/${parcelleCourante}/contour`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
        body: JSON.stringify({ geojson: JSON.stringify(data.features[0].geometry) }),
    });

    const result = await response.json();
    if (result.success) {
        draw.deleteAll();
        modeDessin = false;
        map.setLayoutProperty('parcelles-fill',   'visibility', 'visible');
        map.setLayoutProperty('parcelles-stroke', 'visibility', 'visible');
        document.getElementById('btn-nouvelle-parcelle').textContent = '+ Dessiner une parcelle';
        chargerParcelles(getFiltresActifs());
        toast('Contour sauvegardé !', 'success');
    }
}

// ── Filtres ──────────────────────────────────────────────────
function getFiltresActifs() {
    return {
        zone_id:       document.getElementById('filter-zone').value,
        producteur_id: document.getElementById('filter-producteur').value,
        bio:           document.getElementById('filter-bio').checked ? 1 : '',
    };
}

document.getElementById('btn-appliquer').addEventListener('click', () => chargerParcelles(getFiltresActifs()));
document.getElementById('btn-reset').addEventListener('click', () => {
    ['filter-zone', 'filter-producteur'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('filter-bio').checked = false;
    chargerParcelles();
});

// Filtre en cascade zone → producteurs
document.getElementById('filter-zone').addEventListener('change', async function () {
    const select = document.getElementById('filter-producteur');
    const res    = await fetch(`/producteurs/filter?zone_id=${this.value}`);
    const data   = await res.json();
    select.innerHTML = '<option value="">Tous les producteurs</option>';
    data.forEach(p => {
        const opt = new Option(p.label, p.value);
        select.add(opt);
    });
});

// ── Toast ────────────────────────────────────────────────────
function toast(message, type = 'success') {
    const colors = { success: '#22c55e', error: '#ef4444', warning: '#f59e0b' };
    const el = Object.assign(document.createElement('div'), {
        textContent: message,
        style: `position:fixed;bottom:80px;right:20px;z-index:9999;background:${colors[type]};
                color:#fff;padding:12px 20px;border-radius:8px;font-weight:500;
                box-shadow:0 4px 12px rgba(0,0,0,.2);`
    });
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 3000);
}

// ── Init ─────────────────────────────────────────────────────
map.on('load', () => chargerParcelles());
```

---

## 10. Interface utilisateur

### Pages de l'application

| URL | Page | Permission |
|-----|------|-----------|
| `/` | Dashboard — statistiques générales | Tous |
| `/areas/regions` | Gestion des régions | Admin |
| `/areas/prefectures` | Gestion des préfectures | Admin |
| `/areas/cantons` | Gestion des cantons | Admin |
| `/areas/villages` | Gestion des villages | Admin / Superviseur |
| `/areas/zones` | Gestion des zones | Admin |
| `/organisations` | Organisations paysannes | Admin / Contrôleur |
| `/producteurs` | Liste des producteurs | Tous |
| `/producteurs/{id}` | Fiche producteur + parcelles | Tous |
| `/cultures` | Gestion des cultures | Admin |
| `/parcelles` | Liste des parcelles | Tous |
| `/parcelles/create` | Formulaire création parcelle | Contrôleur / Admin |
| `/parcelles/{id}` | Fiche parcelle (onglets) | Tous |
| `/parcelles/{id}/arbres` | Arbres de la parcelle | Contrôleur / Admin |
| `/parcelles/{id}/identifications` | Identifications | Tous |
| `/parcelles/{id}/controles` | Contrôles | Tous |
| `/carte` | Carte interactive Mapbox | Tous |
| `/users` | Gestion des utilisateurs | Admin |
| `/parametres` | Paramètres système | Admin |

### Formulaire création parcelle — champs

| Champ | Type UI | Remarque |
|-------|---------|----------|
| Producteur | Select dynamique | `/producteurs/filter` |
| Village | Select dynamique | `/areas/villages/filter` |
| Contour (carte) | Mini-carte Mapbox Draw | GeoJSON → champ caché |
| Superficie | Input number (ha) | step=0.0001, auto-calculé depuis le polygone |
| Niveau de pente | Select | Sans / Faible / Moyen / Forte |
| Type de culture | Select | Seul / Associatif / Espacé / Pur / Dérobé |
| Bio ? | Toggle switch | Affiche le champ Superficie bio |
| Superficie bio | Input number (ha) | Visible si Bio = true |
| Rendement bio | Input number (Tonne) | |
| Type d'employés | Select | Saisonnier / Permanent |
| Cours d'eau à proximité ? | Toggle switch | |
| Maisons à proximité ? | Toggle switch | |
| Transformation à la ferme ? | Toggle switch | |
| Approbation de fabrication | Select | Bio / Ok / Déclassifié |
| Volume de production | Input number (Tonne) | |

> **Note checkboxes** : utiliser `$request->boolean('bio')` dans le controller car les toggles HTML retournent `"on"` ou `null`, pas `true/false`.

---

## 11. Déploiement

### Variables d'environnement (.env)

```env
APP_NAME="Certification Bio"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
APP_KEY=base64:...

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=certif_bio
DB_USERNAME=certif_user
DB_PASSWORD=secret

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

MAPBOX_TOKEN=pk.eyJ1IjoieW91cnVzZXJuYW1lIiwiYSI6ImNs...

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
```

### Docker Compose (Laravel Sail)

```yaml
services:
  laravel.app:
    build:
      context: ./vendor/laravel/sail/runtimes/8.3
    ports: ["${APP_PORT:-80}:80"]
    environment:
      DB_HOST: mysql
      REDIS_HOST: redis
    depends_on: [mysql, redis]
    volumes:
      - '.:/var/www/html'

  mysql:
    image: mysql/mysql-server:8.0
    environment:
      MYSQL_ROOT_PASSWORD: '${DB_PASSWORD}'
      MYSQL_DATABASE: '${DB_DATABASE}'
      MYSQL_USER: '${DB_USERNAME}'
      MYSQL_PASSWORD: '${DB_PASSWORD}'
    volumes:
      - sail-mysql:/var/lib/mysql
    ports: ["${FORWARD_DB_PORT:-3306}:3306"]

  redis:
    image: redis:alpine
    ports: ["${FORWARD_REDIS_PORT:-6379}:6379"]

volumes:
  sail-mysql:
```

### Commandes de mise en production

```bash
# Dépendances
composer install --no-dev --optimize-autoloader
npm install && npm run build

# Clé & migrations
php artisan key:generate
php artisan migrate --force
php artisan db:seed --class=RolesAndPermissionsSeeder

# Optimisation
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Storage
php artisan storage:link

# Créer le premier admin
php artisan tinker
>>> \App\Models\User::create([
...     'name' => 'Admin',
...     'email' => 'admin@example.com',
...     'password' => bcrypt('password'),
... ])->assignRole('admin');
```

---

## Récapitulatif des fichiers principaux

| Fichier | Rôle |
|---------|------|
| `app/Models/Scopes/ZoneScope.php` | Filtre automatique par zone utilisateur |
| `app/Models/Parcelle.php` | Model spatial + couleur carte |
| `app/Http/Controllers/CarteController.php` | API GeoJSON + gestion contours |
| `app/Http/Requests/StoreParcelleRequest.php` | Validation formulaire parcelle |
| `database/seeders/RolesAndPermissionsSeeder.php` | Rôles admin / superviseur / contrôleur |
| `resources/views/carte/index.blade.php` | Page carte Mapbox |
| `public/js/carte.js` | Logique carte (draw, filtres, panneau) |
| `routes/web.php` | Toutes les routes de l'application |
| `config/services.php` | Token Mapbox |
| `.env` | Variables d'environnement |