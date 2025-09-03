@extends('user.template._layout')
@section('title', 'Pesanan Saya')
@section('content')
<div class="cart-page pb-4">
    <h2 class="cart-title pt-0 px-0">Pesanan Saya</h2>

    {{-- Informasi Pesanan --}}
    <div class="py-3 bg-light rounded mb-3">
        <p><b>Kode Pesanan:</b> {{ $checkTransaction->uuid }}</p>
        <p><b>Nama Pemesan:</b> {{ $checkTransaction->name }}</p>
        <p><b>No. WhatsApp:</b> +62{{ $checkTransaction->phone }}</p>
        <p><b>No. Meja:</b> {{ $checkTransaction->table->number }}</p>
        <p><b>Status:</b>
            @if($checkTransaction->status == 0)
            <span class="badge bg-warning">Pending</span>
            @elseif($checkTransaction->status == 1)
            <span class="badge bg-primary">Sedang Dibuat</span>
            @elseif($checkTransaction->status == 2)
            <span class="badge bg-success">Sudah Disajikan</span>
            @elseif($checkTransaction->status == 3)
            <span class="badge bg-danger">Selesai</span>
            @endif
        </p>
    </div>

    {{-- Daftar Item Pesanan --}}
    @php $totalHarga = 0; @endphp
    @foreach ($checkTransaction->items as $item)
    @php
    $isMember = $item->type === 'member';
    $price = $isMember ? $item->menu->memberItem->point ?? 0 : ($item->menu->price ?? 0);
    $subtotal = $isMember ? 0 : $price * $item->qty; // member item tidak masuk total uang
    $totalHarga += $subtotal;
    @endphp

    <div class="cart-item px-0">
        <img src="{{ asset('storage/' . ($item->menu->image ?? '')) }}" alt="{{ $item->menu->name ?? '' }}"
            class="cart-img">
        <div class="cart-info">
            <div class="cart-name">
                {{ $item->menu->name ?? '' }}
                @if($isMember)
                <span class="badge bg-info ms-2">Tukar Poin</span>
                @endif
            </div>

            <div class="cart-bottom">
                @if($isMember)
                <div class="cart-price">{{ $price }} Poin</div>
                @else
                <div class="cart-price">Rp {{ number_format($price, 0, ',', '.') }}</div>
                @endif

                <div class="qty-control">
                    <span class="qty">Qty: {{ $item->qty }}</span>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    {{-- Total --}}
    <div class="row px-3 mt-3">
        <hr>
        <div class="cart-total text-end">
            Total: <strong id="totalHarga">Rp {{ number_format($totalHarga, 0, ',', '.') }}</strong>
        </div>
    </div>


    <div class="row px-3 mt-4">
        <form action="{{ route('user.order.endOfOrder', $checkTransaction->uuid) }}" method="POST" class="p-0">
            @csrf
            <button type="submit" class="col-12 btn btn-coffee flex-fill" @if($checkTransaction->status != 2) disabled
                @endif>
                Pesanan Selesai
            </button>
        </form>
    </div>
</div>
@endsection
