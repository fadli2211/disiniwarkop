@extends('user.template._layout')
@section('title', 'Pesanan Saya')
@section('content')
    <div class="cart-page pb-4">
        <h2 class="cart-title pt-0 px-0">Pesanan Saya</h2>

        @forelse ($orders as $order)
            @php
                $totalHarga = $order->items->where('type', '!=', 'member')->sum(function ($item) {
                    return ($item->menu->price ?? 0) * $item->qty;
                });

                $totalPoin = $order->items->where('type', 'member')->sum(function ($item) {
                    return $item->menu->memberItem->point ?? 0;
                });
            @endphp

            <a href="{{ url('/order/' . $order->uuid) }}" class="text-decoration-none text-dark">
                <div class="card mb-3 p-3 shadow-sm hover-shadow-sm" style="cursor:pointer;">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="mb-1">
                                {{ \Carbon\Carbon::parse($order->created_at)->locale('id')->translatedFormat('d F Y H:i') }}
                            </p>
                            <p class="mb-1"><b>Status:</b>
                                @if($order->status == 0)
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($order->status == 1)
                                    <span class="badge bg-primary">Sedang Dibuat</span>
                                @elseif($order->status == 2)
                                    <span class="badge bg-success">Sudah Disajikan</span>
                                @elseif($order->status == 3)
                                    <span class="badge bg-danger">Selesai</span>
                                @endif
                            </p>
                        </div>
                        <div class="text-end">
                            <p class="mb-0">Total</p>
                            <h5 class="mb-0">
                                Rp {{ number_format($totalHarga, 0, ',', '.') }}
                            </h5>
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="text-center my-5 text-muted">
                <i class="fas fa-clipboard-list" style="font-size: 4rem; opacity: 0.7;"></i>
                <h5 class="mt-3">Belum ada pesanan</h5>
                <p>Yuk, pesan menu favoritmu sekarang!</p>
            </div>
        @endforelse
    </div>
@endsection
