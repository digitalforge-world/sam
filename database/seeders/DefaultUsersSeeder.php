<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Seeder;

class DefaultUsersSeeder extends Seeder
{
    public function run(): void
    {
        // ── Créer des zones par défaut si aucune n'existe ──
        $zones = [];
        $zoneNames = ['Zone Maritime', 'Zone des Plateaux', 'Zone Centrale', 'Zone de la Kara', 'Zone des Savanes'];

        foreach ($zoneNames as $nom) {
            $zones[] = Zone::firstOrCreate(['nom' => $nom]);
        }

        // ══════════════════════════════════════════════
        //  1. ADMINISTRATEUR
        // ══════════════════════════════════════════════
        $admin = User::firstOrCreate(
            ['email' => 'admin@sam-bio.tg'],
            [
                'name'              => 'ADMIN',
                'prenom'            => 'Système',
                'password'          => 'admin2024',   // hashé automatiquement via cast
                'type'              => 'ADMIN',
                'zone_id'           => null,
                'est_actif'         => true,
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles(['admin']);

        // ══════════════════════════════════════════════
        //  2. SUPERVISEUR
        // ══════════════════════════════════════════════
        $superviseur = User::firstOrCreate(
            ['email' => 'superviseur@sam-bio.tg'],
            [
                'name'              => 'KOFFI',
                'prenom'            => 'Mensah',
                'password'          => 'super2024',
                'type'              => 'SUPERVISEUR',
                'zone_id'           => $zones[0]->id,  // Zone Maritime
                'est_actif'         => true,
                'email_verified_at' => now(),
            ]
        );
        $superviseur->syncRoles(['superviseur']);

        // ══════════════════════════════════════════════
        //  3. CONTRÔLEURS (3 par défaut)
        // ══════════════════════════════════════════════
        $controleurs = [
            [
                'email'   => 'controleur1@sam-bio.tg',
                'name'    => 'AGBEKO',
                'prenom'  => 'Kossi',
                'zone_id' => $zones[0]->id,  // Zone Maritime
            ],
            [
                'email'   => 'controleur2@sam-bio.tg',
                'name'    => 'AMOUZOU',
                'prenom'  => 'Afi',
                'zone_id' => $zones[1]->id,  // Zone des Plateaux
            ],
            [
                'email'   => 'controleur3@sam-bio.tg',
                'name'    => 'TCHALA',
                'prenom'  => 'Essowe',
                'zone_id' => $zones[2]->id,  // Zone Centrale
            ],
        ];

        foreach ($controleurs as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'prenom'            => $data['prenom'],
                    'password'          => 'control2024',
                    'type'              => 'CONTROLEUR',
                    'zone_id'           => $data['zone_id'],
                    'est_actif'         => true,
                    'email_verified_at' => now(),
                ]
            );
            $user->syncRoles(['controleur']);
        }

        // ── Résumé dans la console ──
        $this->command->newLine();
        $this->command->info('╔══════════════════════════════════════════════════╗');
        $this->command->info('║     ✅ Utilisateurs par défaut créés             ║');
        $this->command->info('╠══════════════════════════════════════════════════╣');
        $this->command->info('║  ADMIN        admin@sam-bio.tg      / admin2024 ║');
        $this->command->info('║  SUPERVISEUR  superviseur@sam-bio.tg / super2024║');
        $this->command->info('║  CONTROLEUR1  controleur1@sam-bio.tg / control2024║');
        $this->command->info('║  CONTROLEUR2  controleur2@sam-bio.tg / control2024║');
        $this->command->info('║  CONTROLEUR3  controleur3@sam-bio.tg / control2024║');
        $this->command->info('╚══════════════════════════════════════════════════╝');
        $this->command->newLine();
    }
}
