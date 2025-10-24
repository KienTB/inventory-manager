<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function index()
    {
        // Chỉ dành cho user role
        if (!auth()->user()->isUser()) {
            abort(403, 'Unauthorized access');
        }

        return view('user-dashboard');
    }
}
