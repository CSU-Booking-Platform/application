<?php

namespace Database\Factories;

use App\Models\BookingRequest;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BookingRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        return [
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'status' => $this->faker->randomElement(["review", "approved", "refused"]),
            //'reference' => [],
            'onsite_contact' => [
                'name' => $this->faker->name,
                'phone' => $this->faker->phoneNumber,
                'email' => $this->faker->email,
            ],
            'event' => [
                'name' => $this->faker->name,
                'phone' => $this->faker->phoneNumber,
                'email' => $this->faker->email,
            ],
            'notes' => $this->faker->paragraphs,
        ];
    }
}
