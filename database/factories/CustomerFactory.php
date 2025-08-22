<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Customer;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'business_number' => $this->faker->numerify('##########'),
            'industry' => $this->faker->randomElement(['IT', 'Manufacturing', 'Retail', 'Finance']),
        ];
    }
}
