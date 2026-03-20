<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'id' => '4ec528e8-6f91-4d4b-9d81-a97ef1acf4ca',
            'name' => 'Administrator',
            'email' => 'admin@toolrental.com',
            'email_verified_at' => now(),
            'role' => 'admin',
            'status' => 'active',
            'password' => bcrypt('12345678'),
        ]);
    }
}
