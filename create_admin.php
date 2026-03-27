<?php

// ============================================================
//  SCRIPT DE CRÉATION D'UN ADMINISTRATEUR
//  Usage : php create_admin.php
//  À placer à la racine du projet Laravel, puis supprimer après.
// ============================================================

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

// ── Paramètres du nouvel admin ───────────────────────────────
$email    = 'admin2@sam-bio.tg';
$name     = 'ADMIN2';
$prenom   = 'Dev';
$password = 'admin2025';
// ────────────────────────────────────────────────────────────

$user = User::firstOrCreate(
    ['email' => $email],
    [
        'name'              => $name,
        'prenom'            => $prenom,
        'password'          => $password,   // hashé automatiquement via le cast du modèle
        'type'              => 'ADMIN',
        'zone_id'           => null,
        'est_actif'         => true,
        'email_verified_at' => now(),
    ]
);

$user->syncRoles(['admin']);

echo "\n";
echo "✅  Administrateur créé avec succès !\n";
echo "──────────────────────────────────────\n";
echo "  Email    : " . $user->email . "\n";
echo "  Password : " . $password . "\n";
echo "  Rôle     : admin\n";
echo "──────────────────────────────────────\n";
echo "⚠️  Supprimez ce fichier du serveur après utilisation !\n";
echo "\n";
