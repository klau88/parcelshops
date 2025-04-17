<?php

namespace Database\Factories;

use App\Enums\Carrier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PostNL>
 */
class ParcelshopFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'external_id' => fake()->numberBetween(1, 999999),
            'name' => fake()->sentence(),
            'slug' => fake()->slug(),
            'carrier' => fake()->randomElement(Carrier::cases())->value,
            'type' => fake()->randomElement(Carrier::cases())->value,
            'street' => fake()->streetName(),
            'number' => fake()->buildingNumber(),
            'postal_code' => fake()->postcode(),
            'city' => fake()->city(),
            'country' => fake()->country(),
            'telephone' => fake()->phoneNumber(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'monday' => fake()->time('H:i') . '-' . fake()->time('H:i'),
            'tuesday' => fake()->time('H:i') . '-' . fake()->time('H:i'),
            'wednesday' => fake()->time('H:i') . '-' . fake()->time('H:i'),
            'thursday' => fake()->time('H:i') . '-' . fake()->time('H:i'),
            'friday' => fake()->time('H:i') . '-' . fake()->time('H:i'),
            'saturday' => fake()->time('H:i') . '-' . fake()->time('H:i'),
            'sunday' => fake()->time('H:i') . '-' . fake()->time('H:i'),
        ];
    }
}
