<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LaundryOrderController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('orders.create');
});

Route::resource('customers', CustomerController::class)->only(['index', 'store']);

Route::get('orders/create', [LaundryOrderController::class, 'create'])->name('orders.create');
Route::post('orders', [LaundryOrderController::class, 'store'])->name('orders.store');
Route::get('orders/{order}', [LaundryOrderController::class, 'show'])->name('orders.show');
Route::post('orders/{order}/status', [LaundryOrderController::class, 'updateStatus'])->name('orders.status.update');

Route::post('orders/{order}/mpesa', [PaymentController::class, 'initiateMpesa'])->name('orders.mpesa');
Route::post('mpesa/callback', [PaymentController::class, 'mpesaCallback'])->name('mpesa.callback');
