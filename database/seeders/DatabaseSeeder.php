<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Place;
use App\Models\Settings;
use App\Models\Reservation;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory(5)->create();

        Settings::create([
            'duree' => 1,
        ]);

        for($i=1;$i<=30;$i++)
        {
            if($i <= 9)
            {
                $libelle = 'A0';
                $libelle .= $i;
            }
            else
            {
                $libelle = 'A';
                $libelle .= $i;
            }
            Place::create([
                'libelle' => $libelle,
            ]);
        }

        User::create([
            'name'=>'admin',
            'prenom'=>'admin',
            'email'=>'admin@mail.fr',
            'admin'=>1,
            'active' =>1,
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'), // password
            'remember_token' => Str::random(10),
        ]);

        User::create([
            'name'=>'user',
            'prenom'=>'user',
            'email'=>'user@mail.fr',
            'admin'=>0,
            'active' =>1,
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'), // password
            'remember_token' => Str::random(10),
        ]);

        $newDateTime = Carbon::now()->addDay();
        for($i=1;$i<=5;$i++)
        {
            Reservation::create([
                'user_id' => $i,
                'place_id' => $i,
                'finished_at' => $newDateTime
            ]);
        }

    }
}
