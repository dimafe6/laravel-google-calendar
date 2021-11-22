<?php

namespace Dimafe6\Database\Factories;

use Dimafe6\GoogleCalendar\Models\GoogleAccount;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class GoogleAccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GoogleAccount::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'google_id'           => Str::random(32),
            'user_name'           => $this->faker->name,
            'nickname'            => $this->faker->name,
            'avatar'              => $this->faker->imageUrl,
            'email'               => $this->faker->email,
            'access_token'        => Str::random(32),
            'access_token_expire' => now()->addDay(),
            'refresh_token'       => Str::random(32),
        ];
    }
}