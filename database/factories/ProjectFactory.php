<?php

// database/factories/ProjectFactory.php
namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition()
    {
        return [
            'customer_id' => Customer::factory(),
            'name' => $this->faker->sentence(3),
        ];
    }
}