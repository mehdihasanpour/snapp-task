<?php

namespace App\Interfaces;

use Illuminate\Http\JsonResponse;

interface TransactionRepositoryInterface
{
    public function topTransactions(): JsonResponse;
}