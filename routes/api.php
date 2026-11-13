<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CurrencyCategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('currencies', [CurrencyCategoryController::class, 'currency']);
    Route::get('categories', [CurrencyCategoryController::class, 'category']);

    // wallet
    Route::get('wallets', [WalletController::class, 'index']);
    Route::post('wallets', [WalletController::class, 'store']);
    Route::get('wallets/{wallet}', [WalletController::class, 'show']);
    Route::put('wallets/{wallet}', [WalletController::class, 'update']);
    Route::delete('wallets/{wallet}', [WalletController::class, 'destroy']);

    // transaction
    Route::get('transactions', [TransactionController::class, 'index']);
    Route::post('transactions', [TransactionController::class, 'store']);
    Route::delete('transactions/{transaction}', [TransactionController::class, 'delete']);
});
