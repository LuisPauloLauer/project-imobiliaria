<?php

use App\Laravue\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Laravue\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $master = User::create([
            'role_id'   => 1,
            'name'      => 'Master',
            'slug'      => 'master',
            'cpf'       =>  99999999991,
            'birth'     => '200210101',
            'sex'       => 'M',
            'fone'      => 999999999,
            'email'     => 'master@laravue.dev',
            'password'  => Hash::make('laravue'),
        ]);
        $admin = User::create([
            'role_id'   => 2,
            'name'      => 'Admin',
            'slug'      => 'admin',
            'cpf'       =>  99999999992,
            'birth'     => '200210101',
            'sex'       => 'M',
            'fone'      => 999999999,
            'email'     => 'admin@laravue.dev',
            'password'  => Hash::make('laravue'),
        ]);
        $manager = User::create([
            'role_id'   => 3,
            'name'      => 'Manager',
            'slug'      => 'manager',
            'cpf'       =>  99999999993,
            'birth'     => '200210101',
            'sex'       => 'M',
            'fone'      => 999999999,
            'email'     => 'manager@laravue.dev',
            'password'  => Hash::make('laravue'),
        ]);
        $editor = User::create([
            'role_id'   => 4,
            'name'      => 'Editor',
            'slug'      => 'editor',
            'cpf'       =>  99999999994,
            'birth'     => '200210101',
            'sex'       => 'M',
            'fone'      => 999999999,
            'email'     => 'editor@laravue.dev',
            'password'  => Hash::make('laravue'),
        ]);
        $user = User::create([
            'role_id'   => 5,
            'name'      => 'User',
            'slug'      => 'user',
            'cpf'       =>  99999999995,
            'birth'     => '200210101',
            'sex'       => 'M',
            'fone'      => 999999999,
            'email'     => 'user@laravue.dev',
            'password'  => Hash::make('laravue'),
        ]);
        $visitor = User::create([
            'role_id'   => 6,
            'name'      => 'Visitor',
            'slug'      => 'visitor',
            'cpf'       =>  99999999996,
            'birth'     => '200210101',
            'sex'       => 'M',
            'fone'      => 999999999,
            'email'     => 'visitor@laravue.dev',
            'password'  => Hash::make('laravue'),
        ]);

        $masterRole = Role::findByName(\App\Laravue\Acl::ROLE_MASTER);
        $adminRole = Role::findByName(\App\Laravue\Acl::ROLE_ADMIN);
        $managerRole = Role::findByName(\App\Laravue\Acl::ROLE_MANAGER);
        $editorRole = Role::findByName(\App\Laravue\Acl::ROLE_EDITOR);
        $userRole = Role::findByName(\App\Laravue\Acl::ROLE_USER);
        $visitorRole = Role::findByName(\App\Laravue\Acl::ROLE_VISITOR);
        $master->syncRoles($masterRole);
        $admin->syncRoles($adminRole);
        $manager->syncRoles($managerRole);
        $editor->syncRoles($editorRole);
        $user->syncRoles($userRole);
        $visitor->syncRoles($visitorRole);
        //$this->call(UsersTableSeeder::class);
    }
}
