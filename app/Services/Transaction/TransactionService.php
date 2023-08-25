<?php

namespace App\Services\Transaction;

use App\Jobs\SendSms;
use Illuminate\Support\Facades\DB;
use App\Models\Card;
use App\Models\Transaction;
use App\Models\Wage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class TransactionService
{
    public function transferMoney(string $sourceCardNumber, string $destinationCardNumber, $amount)
    {
        DB::beginTransaction();

        try {
            $sourceCard = Card::where('card_number', $sourceCardNumber)->lockForUpdate()->first();
            $destinationCard = Card::where('card_number', $destinationCardNumber)->lockForUpdate()->first();

            if ($sourceCard->balance < $amount + Wage::AMOUNT) {
                return ['message' => __('messages.transfer.insufficient-balance'), 'status' => JsonResponse::HTTP_BAD_REQUEST];
            }

            $sourceCard->decrement('balance', $amount + Wage::AMOUNT);
            $sourceCard->account->decrement('current_balance', $amount + Wage::AMOUNT);
            $destinationCard->increment('balance', $amount);
            $destinationCard->account->increment('current_balance', $amount);

            $transaction = new Transaction(['amount' => $amount]);
            $transaction->sourceCard()->associate($sourceCard);
            $transaction->destinationCard()->associate($destinationCard);
            $transaction->save();

            $wage = new Wage(['wage_amount' => Wage::AMOUNT]);
            $wage->transaction()->associate($transaction);
            $wage->save();

            dispatch(new SendSms($sourceCard->account->user->phone, __('messages.sms.source', ['amount' => $amount])))->afterCommit();
            dispatch(new SendSms($destinationCard->account->user->phone,  __('messages.sms.destination', ['amount' => $amount])))->afterCommit();

            DB::commit();

            return ['message' => __('messages.transfer.success'), 'status' => JsonResponse::HTTP_OK];
        } catch (\Exception $e) {
            Log::error('Transfer error: ' . $e->getMessage());
            DB::rollback();
            return ['message' => __('messages.transfer.fail'), 'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR];
        }
    }
}
