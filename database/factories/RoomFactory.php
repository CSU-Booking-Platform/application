<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Room::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'number' => $this->faker->word,
            'floor' => $this->faker->RandomDigit,
            'building' => $this->faker->word,
            'status' => $this->faker->randomElement(["available", "unavailable"]),
            'min_days_advance' => $this->faker->numberBetween(0, 4),
            'max_days_advance' => $this->faker->numberBetween(5, 14),
        ];
    }
}
