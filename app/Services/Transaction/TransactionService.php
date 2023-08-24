<?php

namespace App\Services\Transaction;

use Illuminate\Support\Facades\DB;
use App\Models\Card;
use App\Models\Transaction;
use App\Models\Wage;
use App\Jobs\SendSmsNotification;

class TransactionService
{
    public function transferMoney(Card $sourceCard, Card $destinationCard, $amount)
    {
        try {
            $sourceCard->decrement('balance', $amount + Wage::AMOUNT);
            $destinationCard->increment('balance', $amount);

            $transaction = new Transaction(['amount' => $amount]);
            $transaction->sourceCard()->associate($sourceCard);
            $transaction->destinationCard()->associate($destinationCard);
            $transaction->save();

            $wage = new Wage(['wage_amount' => Wage::AMOUNT]);
            $wage->transaction()->associate($transaction);
            $wage->save();

            dispatch(new SendSmsNotification($sourceCard->account->user->phone, 'Your transaction has been processed.'));
            dispatch(new SendSmsNotification($destinationCard->account->user->phone, 'You have received a transaction.'));

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }
}
