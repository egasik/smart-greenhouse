<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@greenhouse.local'],
            [
                'name' => 'Администратор',
                'password' => Hash::make('admin12345'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@greenhouse.local'],
            [
                'name' => 'Пользователь',
                'password' => Hash::make('user12345'),
                'role' => 'user',
            ]
        );
    }
}

