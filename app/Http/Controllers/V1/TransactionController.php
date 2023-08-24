<?php

namespace App\Http\Controllers\V1;

use App\Models\Card;
use App\Models\Transaction;
use App\Models\Wage;
use App\Http\Controllers\Controller;
use App\Http\Requests\MoneyTransferRequest;
use App\Jobs\SendSmsNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function __invoke(MoneyTransferRequest $request)
    {
        $sourceCard = Card::where('card_number', $request->input('source_card_number'))->first();
        $destinationCard = Card::where('card_number', $request->input('destination_card_number'))->first();

        $amount = $request->input('amount');

        if ($sourceCard->balance < $amount + Wage::AMOUNT) {
            return response()->json(['message' => __('messages.transfer.insufficient-balance')], JsonResponse::HTTP_BAD_REQUEST);
        }

        DB::beginTransaction();

        try {
            $sourceCard->decrement('balance', $amount + Wage::AMOUNT);
            $destinationCard->increment('balance', $amount);

            $transaction = new Transaction([
                'amount' => $amount,
                'transaction_date' => now(),
            ]);
            $transaction->sourceCard()->associate($sourceCard);
            $transaction->destinationCard()->associate($destinationCard);
            $transaction->save();

            $wage = new Wage([
                'wage_amount' => Wage::AMOUNT,
                'wage_date' => now(),
            ]);
            $wage->transaction()->associate($transaction);
            $wage->save();

            dispatch(new SendSmsNotification($sourceCard->account->user->phone, 'Your transaction has been processed.'));
            dispatch(new SendSmsNotification($destinationCard->account->user->phone, 'You have received a transaction.'));

            DB::commit();

            return response()->json(['message' => __('messages.transfer.success')], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => __('messages.transfer.fail')], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
}
