<?php

use App\Http\Controllers\LocaleController;
use App\Http\Controllers\PaymentCallbackController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

Route::middleware(['web', 'auth'])->prefix('payments')->name('payments.')->group(function () {
    Route::get('/stripe/success/{checkout}', [PaymentCallbackController::class, 'stripeSuccess'])
        ->name('stripe.success');
    Route::get('/paypal/success/{checkout}', [PaymentCallbackController::class, 'paypalSuccess'])
        ->name('paypal.success');
    Route::get('/cancel/{checkout}', [PaymentCallbackController::class, 'cancel'])
        ->name('cancel');
});
