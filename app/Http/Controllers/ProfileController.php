<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $cartCount = count($cart);

        $tableCode = session()->get('table_code', null);

        $checkTransaction = Order::where('table_code', $tableCode)->where('status', '!=', 3)->first();

        return view('user.akun')->with([
            'checkTransaction' => $checkTransaction,
            'cartCount' => $cartCount,
        ]);
    }
}
