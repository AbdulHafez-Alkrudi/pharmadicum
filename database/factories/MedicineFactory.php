<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Medicine>
 */
class MedicineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'company_id' => Company::factory(),
            'scientific_name_EN'=> $this->faker->name,
            'scientific_name_AR'=> $this->faker->name,
            'economic_name_EN'=> $this->faker->name,
            'economic_name_AR'=> $this->faker->name,
           /* 'quantity_in_stock'=>$this->faker->randomNumber(),
            'expiration_date'=> $this->faker->date(),
            */'unit_price'=>$this->faker->numberBetween(1000 , 1000000)
        ];
    }
}
