<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Place;
use App\Models\Reservation;
use Illuminate\Support\Str;
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
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'), // password
            'remember_token' => Str::random(10),
        ]);

        User::create([
            'name'=>'user',
            'prenom'=>'user',
            'email'=>'user@mail.fr',
            'admin'=>0,
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'), // password
            'remember_token' => Str::random(10),
        ]);

        for($i=1;$i<=5;$i++)
        {
            Reservation::create([
                'duree' => 3600,
                'user_id' => $i,
                'place_id' => $i
            ]);
        }

    }
}
