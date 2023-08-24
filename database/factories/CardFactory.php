<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Card;
use Illuminate\Database\Eloquent\Factories\Factory;

class CardFactory extends Factory
{
    protected $model = Card::class;

    public function definition()
    {
        return [
            'account_id' => Account::factory(),
            'card_number' => $this->faker->regexify('/^(603799)\d{10}$/'),
            'expiration_date' => $this->faker->creditCardExpirationDate,
            'cvv' => $this->faker->numerify('###'),
            'balance' => $this->faker->numberBetween(0, 10000000),
        ];
    }

    protected function generateCardNumber()
    {
        $prefix = '603799';
        $suffix = $this->faker->numberBetween(10000000, 99999999); // Generate an 8-digit suffix

        return $prefix . $suffix;
    }
}
