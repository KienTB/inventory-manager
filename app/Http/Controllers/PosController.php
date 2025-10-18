<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Gloudemans\Shoppingcart\Facades\Cart;

class PosController extends Controller
{
    public function index()
    {
        return view('pos.index', [
            'customers' => Customer::all(['id','name']),
        ]);
    }
}
