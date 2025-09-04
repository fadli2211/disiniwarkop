<?php

namespace App\Http\Controllers;

use App\Models\MemberItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuMemberController extends Controller
{
    public function index()
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

        $tableCode = session()->get('table_code', null);
        $checkTransaction = Order::where('table_code', $tableCode)->where('status', '!=', 3)->first();

        return view('user.member_menu')
            ->with('cartCount', $cartCount)
            ->with('checkTransaction', $checkTransaction);
    }

    public function getMemberMenu(Request $request)
    {
        $search = $request->get('search');

        $query = MemberItem::with('menu');

        if ($search) {
            $query->whereHas('menu', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            });
        }

        $items = $query->get();

        return response()->json([
            'items' => $items
        ]);
    }

    public function redeem(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
        ]);

        $user = Auth::user();
        $memberItem = MemberItem::where('menu_id', $request->menu_id)->with('menu')->firstOrFail();

        // cek poin cukup
        if ($user->member_point < $memberItem->point) {
            return response()->json([
                'success' => false,
                'message' => 'Poin kamu tidak cukup!'
            ]);
        }

        // cari order aktif
        $order = Order::where('user_id', $user->id)->where('status', '!=', 3)->first();

        if (!$order) {
            $order = Order::create([
                'user_id'    => $user->id,
                'name'       => $user->name,
                'phone'      => $user->phone ?? null,
                'table_code' => session('table_code') ?? null,
                'status'     => 0,
            ]);
        }

        // buat order item dengan type member
        OrderItem::create([
            'order_id' => $order->id,
            'menu_id'  => $memberItem->menu_id,
            'qty'      => 1,
            'type'     => 'member',
        ]);

        return response()->json([
            'success' => true,
            'message' => "Berhasil menukar {$memberItem->menu->name} dengan {$memberItem->point} poin!",
            'uuid'    => $order->uuid,
        ]);
    }
}
