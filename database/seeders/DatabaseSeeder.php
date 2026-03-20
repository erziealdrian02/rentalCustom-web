<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
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
