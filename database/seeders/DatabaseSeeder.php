<?php

namespace Database\Seeders;

use App\Models\Place;
use App\Models\Reservation;
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
        \App\Models\User::factory(10)->create();
        \App\Models\Reservation::factory(10)->create();
        \App\Models\Place::factory(10)->create();
    }
}
