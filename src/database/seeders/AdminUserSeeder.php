<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::unguarded(function (): void {
            User::updateOrCreate(
                ['email' => 'admin@vitaflow.local'],
                [
                    'name' => 'Administrador',
                    'password' => Hash::make('Admin@123'),
                    'tipo' => User::TIPO_ADMIN,
                ],
            );
        });
    }
}
