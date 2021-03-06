<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create(['email' => 'admin@admin.com']);
        User::factory()->create(['email' => 'pierre@admin.com']);
        $this->call([
            StatusProjectSeeder::class,
            ProjectSeeder::class,
            TaskSeeder::class
        ]);
    }
}
