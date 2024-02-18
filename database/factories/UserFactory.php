<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name,
            'username' => fake()->userName,
            'email' => fake()->unique()->safeEmail,
            'phone_number' => fake()->unique()->phoneNumber,
            'address' => fake()->address,
            'subdistrict' => fake()->city,
            'district' => fake()->city,
            'city' => fake()->city,
            'province' => fake()->state,
            'postal_code' => fake()->postcode,
            'profile_picture' => fake()->imageUrl(640, 480, 'people', true),
            'date_of_birth' => fake()->date,
            'place_of_birth' => fake()->city,
            'gender' => fake()->randomElement(['male','female']),
            'position' => fake()->randomElement(['superadmin', 'admin', 'user']),
            'status' => fake()->randomElement(['active', 'inactive', 'banned']),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
