@extends('user.template._layout')
@section('title', 'Keranjang Saya')
@section('content')
<div class="cart-page">
    <h2 class="cart-title pt-0 px-0">Keranjang Saya</h2>

    @php
    $totalHarga = 0;
    $cartCount = count(session()->get('cart', []));
    @endphp

    @if (count($cart) > 0)
    @foreach ($cart as $id => $item)
    @php
    $qty = (int) $item['qty'];
    $price = (int) $item['price'];
    $subtotal = $price * $qty;
    $totalHarga += $subtotal;
    @endphp
    <div class="cart-item pt-2 px-0" data-id="{{ $id }}">
        <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" class="cart-img">
        <div class="cart-info">
            <div class="cart-name">{{ $item['name'] }}</div>
            <div class="cart-bottom">
                <div class="cart-price">Rp {{ number_format($item['price'], 0, ',', '.') }}</div>
                <div class="quantity">
                    <button class="qty-btn" onclick="updateQty({{ $id }}, -1)">-</button>
                    <span class="qty">{{ $item['qty'] }}</span>
                    <button class="qty-btn" onclick="updateQty({{ $id }}, 1)">+</button>
                    <button class="remove-btn" onclick="removeItem({{ $id }})"><i class="fas fa-trash"></i></button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    @else
    <div style="text-align: center; margin-top: 4rem; color: #6c757d;">
        <i class="fas fa-shopping-cart" style="font-size: 4rem; opacity: 0.7;"></i>
        <h4 style="margin-top: 1rem;">Keranjang Kamu kosong</h4>
        <p>Yuk, tambahkan menu favorit Kamu!</p>
    </div>
    @endif

    <div class="mt-4 border-top">
        <h5 class="fw-bold mb-3 mt-4">Detail Pemesan</h5>
        <div class="row">
            {{-- Nama Pemesan --}}
            <div class="col-12 mb-3">
                <label for="name" class="form-label">Nama Pemesan</label>
                <input type="text" id="name" name="name"
                    class="form-control form-control-lg"
                    placeholder="Nama Pemesan"
                    value="{{ Auth::check() ? Auth::user()->name : ($checkTransaction ? $checkTransaction->name : '') }}"
                    @if(Auth::check() || $checkTransaction) readonly @endif>
            </div>

            {{-- Nomor WhatsApp --}}
            <div class="col-12 mb-3">
                <label for="phone" class="form-label">No Whatsapp</label>
                <div class="input-group input-group-lg">
                    <span class="input-group-text">+62</span>
                    <input type="text" id="phone" name="phone"
                        class="form-control form-control-lg"
                        placeholder="8123456789"
                        value="{{ Auth::check() ? Auth::user()->phone : ($checkTransaction ? $checkTransaction->phone : '') }}"
                        @if(Auth::check() || $checkTransaction) readonly @endif>
                </div>
            </div>

            {{-- Kode Meja --}}
            <div class="col-12 mb-3">
                <label for="kode_meja" class="form-label">Kode Meja</label>
                <input type="text" id="kode_meja" name="kode_meja"
                    class="form-control form-control-lg"
                    placeholder="Contoh: M01"
                    value="{{ $tableCode ?? '' }}" {{ $tableCode ? 'readonly' : '' }}>
                <small class="text-danger d-none" id="code_error">*Kode tidak ditemukan</small>
                <small class="text-success d-none" id="code_success">*Kode berhasil digunakan</small>
            </div>

            @if (!$checkTransaction)
            {{-- Tombol cek/reset kode --}}
            <div class="col-12 d-flex gap-2">
                <button class="btn btn-coffee flex-fill" id="check-btn"
                    @if($tableCode) style="display:none" @endif>Cek Kode</button>
                <button class="btn btn-secondary flex-fill" id="reset-btn"
                    @if(!$tableCode) style="display:none" @endif>Reset</button>
            </div>
            @endif

        </div>
    </div>


</div>
<!-- Modal Konfirmasi -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-header bg-danger text-white">
            <h5 class="modal-title">Konfirmasi Hapus</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            Apakah Anda yakin ingin menghapus item ini dari keranjang?
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="button" id="btnConfirmDelete" class="btn btn-danger">Ya, Hapus</button>
        </div>
        </div>
    </div>
</div>
  
<div class="cart-footer">
    <div class="footer-left">
        {{-- Checkbox "Semua" dihilangkan karena belum ada logika yang sesuai--}}
    </div>
    <div class="footer-right">
        <div class="cart-total">Total: <strong id="totalHarga">Rp {{ number_format($totalHarga, 0, ',', '.')
                }}</strong></div>
        @if ($tableCode && count($cart) > 0)
        <button class="btn btn-coffee flex-fill" id="checkout-btn">Pesan</button>
        @else
        <button class="btn btn-coffee flex-fill" id="checkout-btn" disabled>Pesan</button> @endif
    </div>
</div>
@endsection

@section('script')
<script>
    const cartBadge = $('.cart-badge');
    let cart = @json($cart) ?? null;

    function showSnackbar(message) {
        const snackbar = $('#snackbar');
        snackbar.text(message);
        snackbar.addClass('show');

        setTimeout(() => {
            snackbar.removeClass('show');
        }, 3000);
    }

    // Update qty item di cart
    function updateQty(menuId, delta) {
        const qtyElement = $(`.cart-item[data-id="${menuId}"] .qty`);
        let currentQty = parseInt(qtyElement.text());

        if (isNaN(currentQty)) currentQty = 1; // fallback aman

        let newQty = Math.max(1, currentQty + delta);

        $.ajax({
            url: '/keranjang/update',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                menu_id: menuId,
                qty: newQty
            },
            success: function(result) {
                if (result.success) {
                    qtyElement.text(newQty);
                    cartBadge.text(result.total_qty);
                    location.reload();
                } else {
                    alert('Gagal mengupdate kuantitas.');
                }
            }
        });
    }

    // Hapus item dari cart
    let menuIdToDelete = null; // simpan menu_id sementara

    function removeItem(menuId) {
        menuIdToDelete = menuId;
        $('#confirmDeleteModal').modal('show');
    }

    $('#btnConfirmDelete').on('click', function() {
        if (!menuIdToDelete) return;

        $.ajax({
            url: '/keranjang/hapus',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                menu_id: menuIdToDelete
            },
            success: function(result) {
                if (result.success) {
                    showSnackbar('Item berhasil dihapus.');
                    cartBadge.text(result.total_qty);
                    location.reload();
                } else {
                    showSnackbar('Item gagal dihapus.');
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat hapus item.');
            },
            complete: function() {
                $('#confirmDeleteModal').modal('hide');
                menuIdToDelete = null;
            }
        });
    });


    // Check kode meja
    $('#check-btn').on('click', function() {
        const kodeMeja = $('#kode_meja').val();
        const codeError = $('#code_error');
        const codeSuccess = $('#code_success');

        if ($.trim(kodeMeja) === '') {
            codeError.removeClass('d-none');
            codeSuccess.addClass('d-none');
            return;
        }

        $.ajax({
            url: '/keranjang/check-kode',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                kode_meja: kodeMeja
            },
            success: function(result) {
                if (result.success) {
                    codeError.addClass('d-none');
                    codeSuccess.removeClass('d-none');

                    if (Object.keys(cart).length > 0) {
                        $('#checkout-btn').prop('disabled', false);
                    }
                    $('#kode_meja').val(result.table_code).prop('readonly', true);
                    $('#reset-btn').show();
                    $('#check-btn').hide();
                } else {
                    codeError.removeClass('d-none');
                    codeSuccess.addClass('d-none');
                }
            },
            error: function() {
                codeError.removeClass('d-none');
                codeSuccess.addClass('d-none');
            }
        });
    });

    // Reset kode meja
    $('#reset-btn').on('click', function() {
        $.ajax({
            url: '/keranjang/reset-kode',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(result) {
                if (result.success) {
                    location.reload();
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat reset kode meja.');
            }
        });
    });

    $('#checkout-btn').on('click', function() {
        const kodeMeja = $('#kode_meja').val();
        const name = $('input[name="name"]').val();
        const phone = $('input[name="phone"]').val();

        if ($.trim(kodeMeja) === '' || $.trim(name) === '' || $.trim(phone) === '') {
            showSnackbar('Mohon lengkapi semua detail pemesan.');
            return;
        }

        $.ajax({
            url: '/keranjang/checkout',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                kode_meja: kodeMeja,
                name: name,
                phone: phone,
                cart: cart
            },
            success: function(result) {
                if (result.success) {
                    window.location.href = '/order/' + result.uuid;
                } else {
                    showSnackbar('Gagal membuat pesanan. Silakan coba lagi.');
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat memproses pesanan.');
            }
        });
    });

</script>
@endsection
