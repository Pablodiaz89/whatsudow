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
        /*
        $roles = [
            'proveedor',
            'organizador',
        ];

        $permissions = [
            'crear galería',
            'actualizar galería',
            'eliminar galería',
            'ver galería',
        ];

        foreach ($roles as $roleName) {
            // verificación si el rol ya existe
            if (!Role::where('name', $roleName)->exists()) {
                Role::create(['name' => $roleName]);
            }
        }

        foreach ($permissions as $permissionName) {
            // verificación si el permiso ya existe
            if (!Permission::where('name', $permissionName)->exists()) {
                Permission::create(['name' => $permissionName]);
            }
        }

        $role1 = Role::findByName('proveedor');
        $role1->givePermissionTo(Permission::whereIn('name', ['crear galería', 'actualizar galería', 'eliminar galería', 'ver galería'])->get());

        $role2 = Role::findByName('organizador');
        $role2->givePermissionTo('ver galería');
        */


        // creación de roles
        $role1 = Role::create(['name' => 'proveedor']);
        $role2 = Role::create(['name' => 'organizador']);

        // creación de permisos
        Permission::create(['name' => 'crear galería'])->assignRole($role1);
        Permission::create(['name' => 'actualizar galería'])->assignRole($role1);
        Permission::create(['name' => 'eliminar galería'])->assignRole($role1);
        Permission::create(['name' => 'ver galería'])->syncRoles([$role1, $role2]);

        
        


    }
}
