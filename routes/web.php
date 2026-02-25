<?php

use App\Http\Controllers\{
    DashboardController,
    CarteController,
    OrganisationPaysanneController,
    ProducteurController,
    CultureController,
    ParcelleController,
    ArbreController,
    IdentificationController,
    ControleController,
    UserController,
    ParametreController,
    ProfileController,
};
use App\Http\Controllers\Areas\{
    RegionController,
    PrefectureController,
    CantonController,
    VillageController,
    ZoneController,
};
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    Route::get('/', DashboardController::class)->name('dashboard');

    // ── Zones géographiques ──────────────────────────────────────
    Route::prefix('areas')->name('areas.')->group(function () {
        Route::resource('regions', RegionController::class);
        Route::resource('prefectures', PrefectureController::class);
        Route::resource('cantons', CantonController::class);
        Route::resource('villages', VillageController::class);
        Route::resource('zones', ZoneController::class);

        Route::get('villages/filter', [VillageController::class, 'filter'])->name('villages.filter');
        Route::get('cantons/filter',  [CantonController::class, 'filter'])->name('cantons.filter');
    });

    // ── Organisations ────────────────────────────────────────────
    Route::resource('organisations', OrganisationPaysanneController::class);

    // ── Producteurs ──────────────────────────────────────────────
    Route::resource('producteurs', ProducteurController::class);
    Route::get('producteurs-filter', [ProducteurController::class, 'filter'])->name('producteurs.filter');

    // ── Cultures ─────────────────────────────────────────────────
    Route::resource('cultures', CultureController::class);

    // ── Parcelles ────────────────────────────────────────────────
    Route::resource('parcelles', ParcelleController::class);
    Route::post('parcelles/{parcelle}/arbres', [ArbreController::class, 'store'])->name('parcelles.arbres.store');
    Route::delete('arbres/{arbre}', [ArbreController::class, 'destroy'])->name('arbres.destroy');

    // ── Identifications ──────────────────────────────────────────
    Route::resource('identifications', IdentificationController::class)->only(['index', 'create', 'store']);
    Route::patch('identifications/{identification}/approve', [IdentificationController::class, 'approve'])->name('identifications.approve');

    // ── Contrôles ────────────────────────────────────────────────
    Route::resource('controles', ControleController::class)->only(['index', 'create', 'store']);

    // ── Carte (Leaflet / OSM) ──────────────────────────────────────
    Route::prefix('carte')->name('carte.')->group(function () {
        Route::get('/',              [CarteController::class, 'index'])->name('index');
        Route::get('/geojson',       [CarteController::class, 'geojson'])->name('geojson');
        Route::post('/save-contour', [CarteController::class, 'saveContour'])->name('save-contour');
        Route::delete('/parcelles/{parcelle}/contour', [CarteController::class, 'deleteContour'])->name('delete-contour');
    });

    // ── Admin uniquement ─────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::get('parametres',  [ParametreController::class, 'index'])->name('parametres.index');
        Route::put('parametres',  [ParametreController::class, 'update'])->name('parametres.update');
    });

    // ── Profil ───────────────────────────────────────────────────
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
