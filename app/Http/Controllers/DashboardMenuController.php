<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardMenuController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::check()) {
            if(Auth::user()->role == 'admin') {
                return redirect()->route('admin.dashboard');
            }

            $user = Auth::user();

            if (is_null($user->verified_at)) {
                return redirect()->route('verify')
                    ->with('message', 'Silakan verifikasi akun terlebih dahulu.');
            }
        }
        $cart = session()->get('cart', []);
        $cartCount = count($cart);

        if ($request->ref) {
            $table_code = Table::where('code', $request->ref)->where('status', 0)->first();

            if (!$table_code) {
                return redirect()->route('user.menu')
                    ->with('cartCount', $cartCount)
                    ->with('error', 'Invalid or already used table code.');
            }

            $table_code->status = 1;
            $table_code->save();
            session()->put('table_code', $table_code->code);

            $checkTransaction = Order::where('table_code', $table_code->code)->where('status', '!=', 3)->first();

            return redirect()->route('user.menu')
                ->with('cartCount', $cartCount)
                ->with('checkTransaction', $checkTransaction);
        }

        $tableCode = session()->get('table_code', null);
        $checkTransaction = Order::where('table_code', $tableCode)->where('status', '!=', 3)->first();

        return view('user.menu')
            ->with('cartCount', $cartCount)
            ->with('checkTransaction', $checkTransaction);
    }

    // Method baru untuk mengembalikan data menu dalam format JSON
    public function getMenus(Request $request)
    {
        $searchQuery = $request->input('search');
        $cart = session()->get('cart', []);

        if ($searchQuery) {
            $categories = Category::with(['menus' => function ($query) use ($searchQuery) {
                $query->where('name', 'like', "%{$searchQuery}%")
                      ->orWhere('description', 'like', "%{$searchQuery}%");
            }])->get();
        } else {
            $categories = Category::with('menus')->get();
        }

        $categories = $categories->filter(function ($category) {
            return $category->menus->isNotEmpty();
        });

        return response()->json([
            'categories' => $categories,
            'cart' => $cart,
            'cartCount' =>  count($cart)
        ]);
    }
}
