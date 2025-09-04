<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Table;
use App\Models\User;
use App\Services\WablasService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class OrderController extends Controller
{
    protected $wablas;

    public function __construct(WablasService $wablas)
    {
        $this->wablas = $wablas;
    }

    public function index()
    {
        return view('admin.orders.index');
    }

    public function getOrders(Request $request)
    {
        $query = Order::with(['items.menu', 'table'])
        ->whereDate('created_at', Carbon::now()->toDateString());

        // Filter berdasarkan nomor meja
        if ($request->has('table_number_filter') && !empty($request->table_number_filter)) {
            $tableNumber = (int) $request->table_number_filter;
            $query->whereHas('tableQr.table', function ($q) use ($tableNumber) {
                $q->where('number', $tableNumber);
            });
        }

        // Filter nama pemesan
        if ($request->has('orderer_name_filter') && !empty($request->orderer_name_filter)) {
            $query->where('name', 'like', '%' . $request->orderer_name_filter . '%');
        }

        // Filter berdasarkan status
        if (empty($request->status_filter) && $request->status_filter !== '0') {
            $query->where('status', '!=', 3);
        } elseif ($request->has('status_filter') && $request->status_filter !== '') {
            $query->where('status', (int) $request->status_filter);
        }

        $orders = $query->latest()->get();

        return response()->json($orders);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:0,1,2,3'
        ]);

        $order->status = $request->status;

        if ($request->status == 3) {
            $transaction = Order::where('uuid', $order->uuid)->with(['items.menu.memberItem'])->firstOrFail();
            $table = Table::where('code', $transaction->table_code)->first();
            $table->status = 0;
            $table->save();

            if($transaction->user_id) {
                $user = User::where('id', $transaction->user_id)->first();

                $totalHarga = $transaction->items->where('type', '!=', 'member')
                    ->sum(fn($item) => $item->menu->price * $item->qty);

                $poinBaru = floor($totalHarga * 0.01);

                $totalPoinDigunakan = $transaction->items->where('type', 'member')
                    ->sum(fn($item) => $item->menu->memberItem->point * $item->qty);

                $user->member_point = $user->member_point + $poinBaru - $totalPoinDigunakan;
                $user->save();
            }

            if($order->phone) {
                $this->sendReceipt($transaction);
            }
        }

        $order->save();

        return response()->json(['success' => true]);
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
