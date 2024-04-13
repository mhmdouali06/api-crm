<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::create(['name' => 'SuperAdmin']);
        
        $permissions = [
            'create_user' => 'Créer un utilisateur',
            'update_user' => 'Mettre à jour l\'utilisateur',
            'delete_user' => 'Supprimer l\'utilisateur',
            'view_user' => 'Voir l\'utilisateur',
            'create_roles' => 'Créer des rôles',
            'update_roles' => 'Mettre à jour les rôles',
            'delete_roles' => 'Supprimer les rôles',
            'view_roles' => 'Voir les rôles',
            'view_supplier' => 'Voir les fournisseurs',
            'create_supplier' => 'Créer un fournisseur',
            'update_supplier' => 'Mettre à jour un fournisseur',
            'delete_supplier' => 'Supprimer un fournisseur',
            'view_category' => 'Voir les categories',
            'create_category' => 'Créer une categorie',
            'update_category' => 'Mettre à jour une categorie',
            'delete_category' => 'Supprimer une categorie',

        ];
        $permissionList=[
            'create_user',
            'update_user' ,
            'delete_user' ,
            'view_user',
            'create_roles' ,
            'update_roles',
            'delete_roles' ,
            'view_roles',
            'view_supplier',
            'create_supplier',
            'update_supplier',
            'delete_supplier',
            'view_category',
            'create_category',
            'update_category',
            'delete_category',
        ];
        
        foreach ($permissions as $name => $label) {
           $permission=new Permission();
           $permission->name=$name;
           $permission->label=$label;
           $permission->save();
        }
        
        $role->syncPermissions($permissionList);
    }
}
