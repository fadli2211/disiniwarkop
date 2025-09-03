<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // hanya return view, tanpa chart
        $year = request()->get('year', Carbon::now()->year);
        return view('dashboard', compact('year'));
    }

    public function chart(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);

        $orders = Order::with(['items.menu'])
            ->where('status', 3)
            ->whereYear('created_at', $year)
            ->get()
            ->groupBy(fn($order) => \Carbon\Carbon::parse($order->created_at)->format('n'))
            ->map(
                fn($orders) =>
                $orders->sum(
                    fn($order) =>
                    $order->items->sum(
                        fn($item) => ($item->type === 'member')
                            ? 0
                            : $item->qty * ($item->menu?->price ?? 0)
                    )
                )
            );


        $labels = [];
        $data   = [];
        $colors = [];
        $prev   = null;

        for ($month = 1; $month <= 12; $month++) {
            $labels[] = \Carbon\Carbon::create()->month($month)->format('F');
            $value = $orders->get($month, 0);
            $data[] = $value;

            if ($prev === null) {
                $colors[] = 'rgba(128, 128, 128, 0.6)';
            } else {
                if ($value > $prev) {
                    $colors[] = 'rgba(54, 162, 235, 0.6)';
                } elseif ($value < $prev) {
                    $colors[] = 'rgba(255, 99, 132, 0.6)';
                } else {
                    $colors[] = 'rgba(255, 206, 86, 0.6)';
                }
            }
            $prev = $value;
        }

        return response()->json([
            'year'   => $year,
            'labels' => $labels,
            'data'   => $data,
            'colors' => $colors,
        ]);
    }

    public function dailyStats()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // Total Pendapatan Hari Ini
        $incomeToday = Order::with('items.menu')
            ->where('status', 3)
            ->whereDate('created_at', $today)
            ->get()
            ->sum(
                fn($order) =>
                $order->items->sum(
                    fn($item) => ($item->type === 'member')
                        ? 0
                        : $item->qty * ($item->menu?->price ?? 0)
                )
            );

        // Total Pendapatan Kemarin
        $incomeYesterday = Order::with('items.menu')
            ->where('status', 3)
            ->whereDate('created_at', $yesterday)
            ->get()
            ->sum(
                fn($order) =>
                $order->items->sum(
                    fn($item) => ($item->type === 'member')
                        ? 0
                        : $item->qty * ($item->menu?->price ?? 0)
                )
            );

        $ordersToday = Order::whereDate('created_at', $today)->count();
        $ordersYesterday = Order::whereDate('created_at', $yesterday)->count();

        $ongoingToday = Order::where('status', 2)
            ->whereDate('created_at', $today)
            ->count();

        $completedToday = Order::where('status', 3)
            ->whereDate('created_at', $today)
            ->count();

        return response()->json([
            'cards' => [
                [
                    'id' => 'totalIncome',
                    'label' => 'Total Pendapatan Hari Ini',
                    'value' => 'Rp. ' . number_format($incomeToday, 0, ',', '.'),
                    'trend' => $incomeToday > $incomeYesterday ? 'up' : ($incomeToday < $incomeYesterday ? 'down' : 'neutral'),
                    'diff' => 'Rp. ' . number_format(abs($incomeToday - $incomeYesterday), 0, ',', '.')
                ],
                [
                    'id' => 'totalOrders',
                    'label' => 'Total Pesanan Hari Ini',
                    'value' => $ordersToday,
                    'trend' => $ordersToday > $ordersYesterday ? 'up' : ($ordersToday < $ordersYesterday ? 'down' : 'neutral'),
                    'diff' => abs($ordersToday - $ordersYesterday)
                ],
                [
                    'id' => 'ongoingOrders',
                    'label' => 'Pesanan Yang Sedang Berjalan Hari Ini',
                    'value' => $ongoingToday
                ],
                [
                    'id' => 'completedOrders',
                    'label' => 'Pesanan Yang Selesai Hari Ini',
                    'value' => $completedToday
                ],
            ]
        ]);
    }
}
