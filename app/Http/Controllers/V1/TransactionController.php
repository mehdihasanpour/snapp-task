<?php

namespace App\Http\Controllers\V1;

use App\Models\Card;
use App\Models\Transaction;
use App\Models\Wage;
use App\Http\Controllers\Controller;
use App\Http\Requests\MoneyTransferRequest;
use App\Jobs\SendSmsNotification;
use App\Services\Transaction\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function __invoke(MoneyTransferRequest $request, TransactionService $transactionService)
    {
        DB::beginTransaction();

        $sourceCard = Card::where('card_number', $request->input('source_card_number'))->lockForUpdate()->first();
        $destinationCard = Card::where('card_number', $request->input('destination_card_number'))->lockForUpdate()->first();

        $amount = $request->input('amount');

        if ($sourceCard->balance < $amount + Wage::AMOUNT) {
            return response()->json(['message' => __('messages.transfer.insufficient-balance')], JsonResponse::HTTP_BAD_REQUEST);
        }

        if ($transactionService->transferMoney($sourceCard, $destinationCard, $amount)) {
            return response()->json(['message' => __('messages.transfer.success')], JsonResponse::HTTP_OK);
        }

        return response()->json(['message' => __('messages.transfer.fail')], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}
