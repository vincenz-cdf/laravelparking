<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PlaceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $libelle = $this->faker->randomElement($array = array ('a','b','c'));
        $libelle .= $this->faker->numberBetween($min = 1, $max = 9);
        return [
            'libelle' => $libelle,
        ];
    }
}
