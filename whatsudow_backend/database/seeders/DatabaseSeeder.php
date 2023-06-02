<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // factories
        \App\Models\User::factory()->create();
        \App\Models\Company::factory()->create();
        \App\Models\Document::factory()->create();
        \App\Models\Description::factory()->create();
        \App\Models\Phone::factory()->create();
        \App\Models\Category::factory()->create();
        \App\Models\Service::factory()->create();

        // seeders
        $this->call(RoleSeeder::class);
    }
}
