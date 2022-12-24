<?php

namespace Database\Seeders;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Sentinel::getRoleRepository()
            ->createModel()
            ->create([
                'name'       => 'Administrator',
                'slug'       => 'admin',
            ]);

        Sentinel::getRoleRepository()
            ->createModel()
            ->create([
                'name'       => 'User',
                'slug'       => 'user',
            ]);

        Sentinel::getRoleRepository()
            ->createModel()
            ->create([
                'name'       => 'Expert',
                'slug'       => 'expert',
            ]);
    }
}
