<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use App\Services\WablasService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected $wablas;

    public function __construct(WablasService $wablas)
    {
        $this->wablas = $wablas;
    }

    public function index()
    {
        if (Auth::check()) {
            if(Auth::user()->role == 'admin') {
                return redirect()->route('admin.dashboard');
            }
        }

        $auth = Auth::check();
        $cart = session()->get('cart', []);
        $cartCount = count($cart);

        $tableCode = session()->get('table_code', null);

        $checkTransaction = Order::where('table_code', $tableCode)->where('status', '!=', 3)->first();

        if ($auth) {
            $orders = Order::where('user_id', auth()->user()->id)->with('items')->latest()->get();
        }

        return view('user.orders.index')->with([
            'orders' => $orders ?? [],
            'cartCount' => $cartCount,
            'checkTransaction' => $checkTransaction,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_meja' => 'required',
            'name'      => 'required',
            'phone'     => Auth::check() ? 'required' : 'nullable',
            'cart'      => 'required|array',
        ]);

        $tableCode = session()->get('table_code', null);

        $checkTransaction = Order::where('table_code', $tableCode)->where('status', '!=', 3)->with('items')->first();

        if (!$checkTransaction) {
            $table = Table::where('code', $tableCode)->first();
            $table->status = 1;
            $table->save();
            $order = Order::create([
                'user_id'    => Auth::id() ?? null,
                'name'       => $request->name,
                'phone'      => $request->phone ?? null,
                'table_code' => $request->kode_meja,
                'status'     => 0
            ]);
        } else {
            $checkTransaction->status = 0;
            $checkTransaction->save();
            $order = $checkTransaction;
        }

        foreach ($request->cart as $menuId => $item) {
            $menuExist = $order->items->where('menu_id', $menuId)->where('type', '!=', 'member')->first();
            if ($menuExist) {
                $menuExist->qty = $menuExist->qty + $item['qty'];
                $menuExist->save();
            } else {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id'  => $menuId,
                    'qty'      => $item['qty']
                ]);
            }
        }

        session()->forget('cart');

        return response()->json([
            'success' => true,
            'uuid'    => $order->uuid
        ]);
    }

    public function show($uuid)
    {
        $cart = session()->get('cart', []);
        $cartCount = count($cart);
        $checkTransaction = Order::where('uuid', $uuid)->with('items')->firstOrFail();

        return view('user.orders.detail', compact('checkTransaction', 'cartCount'));
    }

    public function endOfOrder($uuid)
    {
        $transaction = Order::where('uuid', $uuid)->with(['items.menu.memberItem'])->firstOrFail();
        $transaction->status = 3;
        $transaction->save();

        $table = Table::where('code', $transaction->table_code)->first();
        $table->status = 0;
        $table->save();

        if (Auth::check()) {
            $user = Auth::user();

            $totalHarga = $transaction->items->where('type', '!=', 'member')
                ->sum(fn($item) => $item->menu->price * $item->qty);

            $poinBaru = floor($totalHarga * 0.01);

            $totalPoinDigunakan = $transaction->items->where('type', 'member')
                ->sum(fn($item) => $item->menu->memberItem->point * $item->qty);

            $user->member_point = $user->member_point + $poinBaru - $totalPoinDigunakan;
            $user->save();
        }

        $this->sendReceipt($transaction);

        session()->forget('cart');
        session()->forget('table_code');

        return redirect()->route('user.menu');
    }

    public function sendReceipt($transaction)
    {
        $totalHarga = $transaction->items->where('type', '!=', 'member')
            ->sum(fn($item) => $item->menu->price * $item->qty);

        $totalPoin = $transaction->items->where('type', 'member')
            ->sum(fn($item) => $item->menu->memberItem->point * $item->qty);

        $message  = "☕ *Disini Warkop*\n";
        $message .= "DIGITAL RECEIPT\n";
        $message .= "Kode Transaksi : {$transaction->uuid}\n";
        $message .= "------------------------------------------------\n";
        $message .= "Nama Pelanggan : {$transaction->name}\n";
        $message .= "No Handphone   : 62{$transaction->phone}\n";
        $message .= "Tanggal        : " . $transaction->created_at->locale('id')->translatedFormat('d F Y H:i') . "\n\n";
        $message .= "Detail\n";
        $message .= "------------------------------------------------\n";

        foreach ($transaction->items as $item) {
            if ($item->type === 'member') {
                $point = $item->menu->memberItem->point * $item->qty;
                $message .= "x{$item->qty} {$item->menu->name} ({$point} Poin)\n";
            } else {
                $subtotal = $item->menu->price * $item->qty;
                $message .= "x{$item->qty} {$item->menu->name} Rp " . number_format($subtotal, 0, ',', '.') . "\n";
            }
        }

        $message .= "------------------------------------------------\n";
        $message .= "Total: Rp " . number_format($totalHarga, 0, ',', '.') . "\n";
        if ($totalPoin > 0) {
            $message .= "Total Poin Digunakan: {$totalPoin} Poin\n";
        }

        $response = $this->wablas->sendMessage('62' . $transaction->phone, $message);

        return response()->json($response);
    }
}
