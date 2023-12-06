<?php

namespace Database\Factories;

use App\Models\Medicine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExpirationMedicine>
 */
class ExpirationMedicineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "medicine_id" => Medicine::factory()->create(''),
            "amount" => $this->faker->numberBetween(1 , 100),
            "expiration_date" => $this->faker->date()
        ];
    }
}
