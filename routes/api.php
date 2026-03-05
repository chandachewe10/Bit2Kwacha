<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\OPENNODE\ConfirmBitCoinToBankController;
use App\Http\Controllers\API\OPENNODE\ConfirmBitCoinToMobileController;
use App\Http\Controllers\Lenco\ConfirmMobileToBitcoinController;
use App\Http\Controllers\ExchangeRateController;
use App\Http\Controllers\SellBitcoinController;
use App\Http\Controllers\Lenco\PaymentController;

// Webhook routes (OpenNode callbacks)
Route::post('/confirm-bitcoin-to-bank', [ConfirmBitCoinToBankController::class, 'confirmBitCoinToBankPayments']);
Route::post('/confirm-bitcoin-to-mobile', [ConfirmBitCoinToMobileController::class, 'confirmBitCoinToMobileMoneyPayments']);
Route::post('/confirm-mobile-to-bitcoin', [ConfirmMobileToBitcoinController::class, 'confirmMobileToBitcoinPayments']);

// Mobile App API Routes
Route::get('/exchange-rates', [ExchangeRateController::class, 'getRates'])->name('api.exchange.rates');
Route::post('/check-balance', [ExchangeRateController::class, 'checkBalance'])->name('api.check.balance');
Route::get('/get-balance', [ExchangeRateController::class, 'getBalance'])->name('api.get.balance');
Route::post('/check-lenco-balance', [SellBitcoinController::class, 'checkFloat'])->name('api.check.lenco.balance');
Route::post('/generate-invoice', [SellBitcoinController::class, 'generateInvoice'])->name('api.generate.invoice');
Route::post('/complete-subscription', [PaymentController::class, 'completeSubscription'])->name('api.complete.subscription');
