<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

            $role = Role::create([
                'name' => 'admin',
                'guard_name' => 'api'
            ]);

            $user = User::where('email', 'admin@admin.com')->first();
            $user->assignRole($role);


            Permission::create(['name' => 'web login', 'guard_name' => 'api']);
            $role->givePermissionTo('web login');

    }
}
