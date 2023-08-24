<?php

use App\Http\Controllers\V1\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/transfer', [TransactionController::class, 'transferMoney'])->name('v1.transfer');