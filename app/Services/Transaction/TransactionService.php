<?php

namespace App\Services\Transaction;

use Illuminate\Support\Facades\DB;
use App\Models\Card;
use App\Models\Transaction;
use App\Models\Wage;
use App\Jobs\SendSmsNotification;
use Exception;
use Illuminate\Support\Facades\Log;

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

            dispatch(new SendSmsNotification($sourceCard->account->user->phone, __('messages.sms.source',['amount'=>$amount])))->afterCommit();
            dispatch(new SendSmsNotification($destinationCard->account->user->phone,  __('messages.sms.destination',['amount'=>$amount])))->afterCommit();

            DB::commit();

            return true;
        } catch (\Exception $e) {
            Log::error('Transfer error: ' . $e->getMessage());
            DB::rollback();
            return false;
        }
    }
}
