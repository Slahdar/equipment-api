<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    public function up()
    {
        app()['cache']->forget('spatie.permission.cache');
        
        // Create roles
        $admin = Role::create(['name' => 'admin']);
        $manager = Role::create(['name' => 'manager']);
        $user = Role::create(['name' => 'user']);
        
        // Create permissions
        $permissions = [
            // Domains
            'view domains',
            'create domains',
            'edit domains',
            'delete domains',
            
            // Families
            'view families',
            'create families',
            'edit families',
            'delete families',
            
            // Equipment types
            'view equipment_types',
            'create equipment_types',
            'edit equipment_types',
            'delete equipment_types',
            
            // Brands
            'view brands',
            'create brands',
            'edit brands',
            'delete brands',
            
            // Document types
            'view document_types',
            'create document_types',
            'edit document_types',
            'delete document_types',
            
            // Documents
            'view documents',
            'create documents',
            'edit documents',
            'delete documents',
            'archive documents',
            
            // Products
            'view products',
            'create products',
            'edit products',
            'delete products',
            'associate products',
            
            // Inventories
            'view inventories',
            'create inventories',
            'edit inventories',
            'delete inventories',
            
            // Users
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage permissions',
        ];
        
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
        
        // Assign permissions to roles
        $admin->givePermissionTo(Permission::all());
        
        $manager->givePermissionTo([
            'view domains', 'create domains', 'edit domains',
            'view families', 'create families', 'edit families',
            'view equipment_types', 'create equipment_types', 'edit equipment_types',
            'view brands', 'create brands', 'edit brands',
            'view document_types', 'create document_types', 'edit document_types',
            'view documents', 'create documents', 'edit documents', 'archive documents',
            'view products', 'create products', 'edit products', 'associate products',
            'view inventories', 'create inventories', 'edit inventories',
            'view users',
        ]);
        
        $user->givePermissionTo([
            'view domains', 
            'view families',
            'view equipment_types',
            'view brands',
            'view document_types',
            'view documents',
            'view products',
            'view inventories',
        ]);
    }

    public function down()
    {
        app()['cache']->forget('spatie.permission.cache');
        
        // Delete roles and permissions
        $roles = Role::all();
        foreach ($roles as $role) {
            $role->delete();
        }
        
        $permissions = Permission::all();
        foreach ($permissions as $permission) {
            $permission->delete();
        }
    }
};