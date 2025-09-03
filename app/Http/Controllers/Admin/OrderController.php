<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class OrderController extends Controller
{
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
        $order->save();

        return response()->json(['success' => true]);
    }
}
