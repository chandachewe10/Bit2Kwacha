<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Lenco\PaymentController;
use App\Http\Controllers\Lenco\LencoMobileMoneyService;
use App\Http\Controllers\SellBitcoinController;
use App\Http\Controllers\AirtimeController;
use App\Http\Controllers\ExchangeRateController;
use App\Services\LencoMobileMoneyService as MobileMoneyLookup;
use App\Services\ZambianMobileNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/privacy-terms', function () {
    return view('terms');
})->name('terms');

Route::get('/subscription/payment', function (Request $request, MobileMoneyLookup $lenco) {
    if ($request->filled('amount_kwacha') || $request->filled('amount_sats')) {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
            'amount_kwacha' => ['required', 'numeric', 'min:20'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'amount_sats' => ['required', 'numeric', 'min:1'],
            'amount_btc' => ['required', 'numeric', 'min:0'],
            'conversion_fee' => ['required', 'numeric', 'min:0'],
            'network_fee' => ['nullable', 'numeric', 'min:0'],
            'type' => ['nullable', 'in:buy'],
        ]);

        try {
            $resolved = $lenco->resolve($data['phone']);
        } catch (InvalidArgumentException $exception) {
            return response($exception->getMessage(), 422);
        } catch (\Throwable $exception) {
            Log::warning('Could not resolve mobile-money recipient before checkout', ['message' => $exception->getMessage()]);
            return response('We could not verify this mobile-money number. Please check it and try again.', 422);
        }

        $data['phone'] = $resolved['phone'];
        $data['recipient_name'] = $resolved['account_name'];
        $data['operator'] = $resolved['operator'];
        $data['name'] = $request->input('name', 'Customer');
        $data['email'] = $request->input('email', 'customer@bitkwik.com');
        $data['network_fee'] = $data['network_fee'] ?? config('services.bitcoin.buy_network_fee');
        session()->put('pending_transaction_data', $data);
    } else {
        $data = session()->get('pending_transaction_data');
    }

    if (!$data) {
        return redirect('/')->with('error', 'Please start a Bitcoin purchase first.');
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

Route::post('/generate-airtime-invoice', [AirtimeController::class, 'generateInvoice'])
    ->name('generate.airtime.invoice');

Route::get('/api/exchange-rates', [ExchangeRateController::class, 'getRates'])
    ->name('exchange.rates');

Route::post('/api/check-balance', [ExchangeRateController::class, 'checkBalance'])
    ->name('check.balance');

Route::get('/api/get-balance', [ExchangeRateController::class, 'getBalance'])
    ->name('get.balance');

Route::post('/api/check-lenco-balance', [SellBitcoinController::class, 'checkFloat'])
    ->name('check.lenco.balance');
