<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            if(Auth::user()->role == 'admin') {
                return redirect()->route('admin.dashboard');
            }
        }
        // Ambil data keranjang dari session
        $cart = session()->get('cart', []);
        $cartCount = count($cart);

        $tableCode = session()->get('table_code', null);

        $checkTransaction = Order::where('table_code', $tableCode)->where('status', '!=', 3)->first();

        if ($tableCode) {
            $table = Table::where('code', $tableCode)->first();
        }

        // Kirim data keranjang ke view
        return view('user.keranjang')
            ->with('cart', $cart)
            ->with('cartCount', $cartCount)
            ->with('tableCode', $tableCode)
            ->with('table', $table)
            ->with('checkTransaction', $checkTransaction);
    }

    public function addToCart(Request $request)
    {
        $menuId = $request->input('menu_id');
        $qty = $request->input('qty');

        $menu = Menu::find($menuId);

        if (!$menu) {
            return response()->json(['success' => false, 'message' => 'Menu tidak ditemukan!']);
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$menuId])) {
            $cart[$menuId]['qty'] += $qty;
        } else {
            $cart[$menuId] = [
                "id" => $menu->id,
                "name" => $menu->name,
                "qty" => $qty,
                "price" => $menu->price,
                "image" => $menu->image,
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil ditambahkan ke keranjang!',
        ]);
    }

    // Tambahan: method untuk update kuantitas dan hapus item
    public function updateCart(Request $request)
    {
        $menuId = $request->input('menu_id');
        $qty = $request->input('qty');

        $cart = session()->get('cart');

        if (isset($cart[$menuId])) {
            $cart[$menuId]['qty'] = $qty;
            session()->put('cart', $cart);

            return response()->json([
                'success' => true,
                'total_qty' => count($cart),
            ]);
        }

        return response()->json([
            'success' => false,
        ]);
    }

    public function removeCart(Request $request)
    {
        $menuId = $request->input('menu_id');
        $cart = session()->get('cart');

        if (isset($cart[$menuId])) {
            unset($cart[$menuId]);
            session()->put('cart', $cart);
            return response()->json([
                'success' => true,
                'total_qty' => count($cart),
            ]);
        }

        return response()->json([
            'success' => false,
        ]);
    }

    // ... di dalam class CartController
    public function getTotalItems()
    {
        $cart = session()->get('cart', []);
        $total_qty = count($cart);

        return response()->json([
            'success' => true,
            'total_qty' => $total_qty,
        ]);
    }

    // Tambahkan method baru ini di dalam class CartController
    public function getCartItem(Request $request)
    {
        $menuId = $request->input('menu_id');
        $cart = session()->get('cart', []);

        if (isset($cart[$menuId])) {
            return response()->json([
                'success' => true,
                'item' => $cart[$menuId],
            ]);
        }

        return response()->json(['success' => false]);
    }

    public function updateDirectly(Request $request)
    {
        $menuId = $request->input('menu_id');
        $delta = $request->input('delta');

        $cart = session()->get('cart');

        if (isset($cart[$menuId])) {
            $newQty = $cart[$menuId]['qty'] + $delta;

            if ($newQty > 0) {
                $cart[$menuId]['qty'] = $newQty;
            } else {
                // Hapus item jika kuantitasnya menjadi 0 atau kurang
                unset($cart[$menuId]);
            }

            session()->put('cart', $cart);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }

    public function checkKode(Request $request)
    {
        $kodeMeja = $request->input('kode_meja');

        $table = Table::where('code', $kodeMeja)->where('status', 0)->first();

        if ($table) {
            $table->status = 1;
            $table->save();

            session()->put('table_code', $table->code);

            return response()->json([
                'success' => true,
                'message' => 'Kode meja valid dan berhasil digunakan.',
                'table_number' => $table->number,
                'table_code' => $table->code,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Kode meja tidak valid atau sudah digunakan.'
        ]);
    }

    public function resetKode(Request $request)
    {
        $tableCode = session()->get('table_code');

        if ($tableCode) {
            $table = Table::where('code', $tableCode)->first();
            if ($table) {
                $table->status = 0;
                $table->save();
            }

            session()->forget('table_code');
        }

        return response()->json([
            'success' => true,
            'message' => 'Kode meja berhasil direset.'
        ]);
    }

}
