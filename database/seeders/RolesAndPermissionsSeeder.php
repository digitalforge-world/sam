<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
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
            'parcelles.contour',
            'identifications.view', 'identifications.create', 'identifications.approve',
            'controles.view', 'controles.create',
            'carte.view',
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'parametres.view', 'parametres.edit',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        Role::firstOrCreate(['name' => 'admin'])
            ->givePermissionTo(Permission::all());

        Role::firstOrCreate(['name' => 'superviseur'])
            ->givePermissionTo([
                'regions.view', 'prefectures.view', 'cantons.view', 'villages.view', 'zones.view',
                'organisations.view', 'producteurs.view',
                'parcelles.view', 'identifications.view', 'identifications.approve',
                'controles.view', 'carte.view',
            ]);

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
}
