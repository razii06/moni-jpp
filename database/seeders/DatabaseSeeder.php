<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin JPP',
            'username' => 'admin',
            'email' => 'admin@pim.co.id',
            'password' => Hash::make('adminjpp321#'), 
            'role' => 'admin',
        ]);
    }
}