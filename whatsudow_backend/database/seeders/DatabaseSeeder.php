<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Pdf;
use App\Models\File;
use App\Models\User;
use App\Models\Phone;
use App\Models\Avatar;
use App\Models\Budget;
use App\Models\Company;
use App\Models\Gallery;
use App\Models\Message;
use App\Models\Service;
use App\Models\Category;
use App\Models\Document;
use App\Models\Favorite;
use App\Models\Location;
use App\Models\Description;
use App\Models\Availability;
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
        $user = User::factory()->create();
        Company::factory()->count(3)->create();
        Document::factory()->count(10)->create();
        Description::factory()->count(10)->create();
        Phone::factory()->count(5)->create();
        Category::factory()->count(5)->create();
        Service::factory()->count(20)->create();
        Location::factory()->create();
        Budget::factory()->create();
        Message::factory()->create();
        Favorite::factory()->create();
        Avatar::factory()->create();
        Availability::factory()->create();
        Pdf::factory()->count(10)->create();
        File::factory()->count(10)->create([
            'user_id' => $user->id,
        ]);
        Gallery::factory()->count(10)->create();

        

        // seeders
        $this->call(RoleSeeder::class);
    }
}
