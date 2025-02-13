<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'role' => 'admin',
            'name' => 'Petar',
            'surname' => 'Petrović',
            'phone_number' => '123456789',
            'email' => 'petar@petrovic.com',
            'password' =>  bcrypt('password123'),
            'position' => 'Manager',
        ]);

        User::create([
            'role' => 'employee',
            'name' => 'Milan',
            'surname' => 'Milanović',
            'phone_number' => '123456789',
            'email' => 'milan@milanovic.com',
            'password' =>  bcrypt('password123'),
            'position' => 'Software Developer',
        ]);

    
    }
}
