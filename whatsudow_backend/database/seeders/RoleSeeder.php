<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $permissions = [
            'crear galería',
            'actualizar galería',
            'eliminar galería',
            'ver galería',
        ];

        foreach ($permissions as $permissionName) {
            Permission::create(['name' => $permissionName]);
        }

        $role1 = Role::create(['name' => 'proveedor']);
        $role1->givePermissionTo(['crear galería', 'actualizar galería', 'eliminar galería', 'ver galería']);

        $role2 = Role::create(['name' => 'organizador']);
        $role2->givePermissionTo(['ver galería']);

        
    }
}
