<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Lenco\PaymentController;
use App\Http\Controllers\SellBitcoinController;
use App\Http\Controllers\ExchangeRateController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/subscription/payment', function () {
    // Check if new transaction data is provided in the request
    // This ensures that subsequent buy requests update the payment details
    if (request()->filled('amount_kwacha') || request()->filled('amount_sats')) {
        $data = [
            'name' => request()->input('name', 'Customer'),
            'email' => request()->input('email', 'customer@bitkwik.com'),
            'phone' => request()->input('phone', ''),
            'amount_kwacha' => request()->input('amount_kwacha', 0),
            'total_amount' => request()->input('total_amount', 0),
            'amount_sats' => request()->input('amount_sats', 0),
            'amount_btc' => request()->input('amount_btc', 0),
            'conversion_fee' => request()->input('conversion_fee', 0),
            'network_fee' => request()->input('network_fee', 5),
            'type' => request()->input('type', 'buy'),
        ];
        session()->put('pending_transaction_data', $data);
    } else {
        // Use existing session data if no new data in request
        $data = session()->get('pending_transaction_data');
    }

    return view('gateways.Lenco.LencoPayments', ['data' => $data]);
})->name('subscription.lenco');

Route::post('/subscription/payment', function () {
    $data = request()->all();
    session()->put('pending_transaction_data', $data);
    return redirect()->route('subscription.lenco');
});

Route::post('/complete-subscription', [PaymentController::class, 'completeSubscription'])
    ->name('complete.subscription');

Route::post('/generate-invoice', [SellBitcoinController::class, 'generateInvoice'])
    ->name('generate.invoice');

Route::get('/api/exchange-rates', [ExchangeRateController::class, 'getRates'])
    ->name('exchange.rates');

Route::post('/api/check-balance', [ExchangeRateController::class, 'checkBalance'])
    ->name('check.balance');

Route::get('/api/get-balance', [ExchangeRateController::class, 'getBalance'])
    ->name('get.balance');