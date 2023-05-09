<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'phone' => $this->faker->e164PhoneNumber,
            'department_id' => $this->faker->numberBetween(1,6)

        ];
    }
}
