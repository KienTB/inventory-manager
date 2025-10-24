<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Order\OrderCompleteController;
use App\Http\Controllers\Order\OrderPendingController;
use App\Http\Controllers\UserDashboardController;

/*
|--------------------------------------------------------------------------
| User Routes (Sales Staff)
|--------------------------------------------------------------------------
|
| Routes dành cho nhân viên bán hàng (role: user)
| Chỉ bao gồm POS và xem đơn hàng
|
*/

Route::middleware(['auth', 'role:user'])->group(function () {

    // User Dashboard - Trang chủ cho nhân viên bán hàng
    Route::get('/', [UserDashboardController::class, 'index'])->name('user.dashboard');

    // POS - Point of Sale (Giao diện bán hàng chính)
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/checkout', [OrderController::class, 'store'])->name('pos.checkout');

    // Orders - Xem đơn hàng (chỉ đọc)
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/pending', OrderPendingController::class)->name('orders.pending');
    Route::get('/orders/complete', OrderCompleteController::class)->name('orders.complete');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    // TODO: Remove from OrderController - Download invoice
    Route::get('/orders/details/{order_id}/download', [OrderController::class, 'downloadInvoice'])->name('order.downloadInvoice');

    // Profile - Chỉnh sửa thông tin cá nhân
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});
