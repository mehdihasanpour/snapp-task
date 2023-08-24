<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Card;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create()->each(function ($user) {
            $user->accounts()->saveMany(Account::factory()->count(2)->make());
        });
        
        Account::all()->each(function ($account) {
            $account->cards()->saveMany(Card::factory()->count(2)->make());
        });
    }
}
