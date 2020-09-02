<?php

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
        factory('App\User')->create(['email' => 'admin@admin.com']);
        $this->call([
            StatusProjectSeeder::class,
            ProjectSeeder::class
        ]);
    }
}
