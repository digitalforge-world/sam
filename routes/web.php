<?php

use App\Http\Controllers\Web\{
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
    ExportController,
};
use App\Http\Controllers\Web\Areas\{
    RegionController,
    PrefectureController,
    CommuneController,
    CantonController,
    VillageController,
    ZoneController,
};
use Illuminate\Support\Facades\Route;

Route::get('/scan', function() {
    $path = public_path('logo.jpg');
    $fav = public_path('favicon.ico');
    
    return [
        'logo' => [
            'exists' => file_exists($path),
            'mtime' => file_exists($path) ? date('Y-m-d H:i:s', filemtime($path)) : 'N/A',
            'size' => file_exists($path) ? filesize($path) : 0,
        ],
        'favicon' => [
            'exists' => file_exists($fav),
            'mtime' => file_exists($fav) ? date('Y-m-d H:i:s', filemtime($fav)) : 'N/A',
        ],
        'public_path' => public_path(),
    ];
});

Route::middleware(['auth'])->group(function () {

    // ── Admin uniquement ─────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::resource('users', UserController::class);
        Route::post('users/{user}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');
        Route::get('parametres',  [ParametreController::class, 'index'])->name('parametres.index');
        Route::put('parametres',  [ParametreController::class, 'update'])->name('parametres.update');
        
        // Toutes les autres routes dashboard passent ici
        Route::prefix('areas')->name('areas.')->group(function () {
            Route::resource('regions', RegionController::class);
            Route::resource('prefectures', PrefectureController::class);
            Route::resource('communes', CommuneController::class);
            Route::resource('cantons', CantonController::class);
            Route::resource('villages', VillageController::class);
            Route::resource('zones', ZoneController::class);
            Route::get('villages/filter', [VillageController::class, 'filter'])->name('villages.filter');
            Route::get('cantons/filter',  [CantonController::class, 'filter'])->name('cantons.filter');
        });
        Route::resource('organisations', OrganisationPaysanneController::class);
        Route::resource('producteurs', ProducteurController::class);
        Route::get('producteurs-filter', [ProducteurController::class, 'filter'])->name('producteurs.filter');
        Route::resource('cultures', CultureController::class);
        Route::resource('parcelles', ParcelleController::class);
        Route::post('parcelles/{parcelle}/arbres', [ArbreController::class, 'store'])->name('parcelles.arbres.store');
        Route::delete('arbres/{arbre}', [ArbreController::class, 'destroy'])->name('arbres.destroy');
        Route::resource('identifications', IdentificationController::class);
        Route::patch('identifications/{identification}/approve', [IdentificationController::class, 'approve'])->name('identifications.approve');
        Route::resource('controles', ControleController::class);
        Route::prefix('carte')->name('carte.')->group(function () {
            Route::get('/',              [CarteController::class, 'index'])->name('index');
            Route::get('/geojson',       [CarteController::class, 'geojson'])->name('geojson');
            Route::post('/save-contour', [CarteController::class, 'saveContour'])->name('save-contour');
            Route::delete('/parcelles/{parcelle}/contour', [CarteController::class, 'deleteContour'])->name('delete-contour');
        });

        // ── Exports PDF / Excel ──────────────────────────────────────
        Route::prefix('export')->name('export.')->group(function () {
            Route::get('organisations/pdf',   [ExportController::class, 'organisationsPdf'])->name('organisations.pdf');
            Route::get('organisations/excel', [ExportController::class, 'organisationsExcel'])->name('organisations.excel');
            Route::get('producteurs/pdf',     [ExportController::class, 'producteursPdf'])->name('producteurs.pdf');
            Route::get('producteurs/excel',   [ExportController::class, 'producteursExcel'])->name('producteurs.excel');
            Route::get('cultures/pdf',        [ExportController::class, 'culturesPdf'])->name('cultures.pdf');
            Route::get('cultures/excel',      [ExportController::class, 'culturesExcel'])->name('cultures.excel');
            Route::get('parcelles/pdf',       [ExportController::class, 'parcellesPdf'])->name('parcelles.pdf');
            Route::get('parcelles/excel',     [ExportController::class, 'parcellesExcel'])->name('parcelles.excel');
        });
    });

    // ── Profil ───────────────────────────────────────────────────
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
