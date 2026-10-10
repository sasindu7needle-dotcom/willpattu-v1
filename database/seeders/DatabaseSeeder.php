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
        User::query()->updateOrCreate([
            'username' => 'admin',
        ], [
            'name' => 'Wilpattu Administrator',
            'email' => 'admin@wilpattu.test',
            'password' => '7#iK0x31bo',
            'is_admin' => true,
        ]);

        $this->call([
            PageContentSeeder::class,
            PageContentItemSeeder::class,
        ]);
    }
}
