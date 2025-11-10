<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserDashboardController;

/*
|--------------------------------------------------------------------------
| User-Specific Routes (Sales Staff)
|--------------------------------------------------------------------------
|
| Routes đặc thù chỉ dành cho nhân viên bán hàng (role: user)
| Các route chung đã được định nghĩa trong web.php
|
*/

Route::middleware(['auth', 'role:user'])->group(function () {
    // User Dashboard - Trang chủ cho nhân viên bán hàng
    Route::get('/', [UserDashboardController::class, 'index'])->name('user.dashboard');
    
    // Các route đặc thù khác của user (nếu có) có thể thêm vào đây
});
