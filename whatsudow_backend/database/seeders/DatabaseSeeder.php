<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\File;
use App\Models\User;
use App\Models\Image;
use App\Models\Phone;
use App\Models\Budget;
use App\Models\Company;
use App\Models\Message;
use App\Models\Service;
use App\Models\Category;
use App\Models\Document;
use App\Models\Favorite;
use App\Models\Location;
use App\Models\Description;
use App\Models\Availability;
use App\Models\Pdf;
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
        Company::factory()->create();
        Document::factory()->create();
        Description::factory()->create();
        Phone::factory()->create();
        Category::factory()->create();
        Service::factory()->create();
        Image::factory()->create();
        Location::factory()->create();
        Budget::factory()->create();
        Message::factory()->create();
        Favorite::factory()->create();
        Availability::factory()->create();
        Pdf::factory()->create();
        File::factory()->count(10)->create([
            'user_id' => $user->id,
        ]);

        // seeders
        $this->call(RoleSeeder::class);
    }
}
