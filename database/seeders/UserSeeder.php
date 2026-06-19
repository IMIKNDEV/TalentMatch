<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Sophie Martin',
            'email' => 'sophie@example.com',
            'password' => bcrypt('password'),
        ]);

        User::create([
            'name' => 'Thomas Dubois',
            'email' => 'thomas@example.com',
            'password' => bcrypt('password'),
        ]);
    }
}
