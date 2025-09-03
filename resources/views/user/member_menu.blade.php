@extends('user.template._layout')
@section('title', 'Menu Member')

@section('content')
<div class="header">
    <div class="banner">
        <div class="banner-text">
            <h2>Menu Member</h2>
            <p>Tukar poinmu sekarang!</p>
        </div>
    </div>
</div>

<div class="product-list" id="productList"></div>
@endsection

@section('script')
<script>
    const productList = $('#productList');

    function showSnackbar(message) {
        const snackbar = $('#snackbar');
        snackbar.text(message);
        snackbar.addClass('show');

        setTimeout(() => {
            snackbar.removeClass('show');
        }, 3000);
    }

    function getMemberMenu(searchQuery = '') {
        $.ajax({
            url: `/member/api/get-member-menu?search=${encodeURIComponent(searchQuery)}`,
            method: 'GET',
            success: function(data) {
                renderMenus(data.items);
            },
            error: function(err) {
                alert('Gagal memuat menu member!');
            }
        });
    }

    function renderMenus(items) {
        productList.empty();
        $.each(items, function(index, item) {
            const productCard = $(`
                <div class="product-card">
                    <div class="parent-img">
                        <img src="{{ asset('storage') }}/${item.menu.image}" alt="${item.menu.name}" />
                    </div>
                    <div class="product-info">
                        <div class="product-name">${item.menu.name}</div>
                        <div class="product-price">${item.point} Poin</div>
                    </div>
                    <button class="add-to-cart" onclick="redeem(${item.menu_id})">Tukar</button>
                </div>
            `);
            productList.append(productCard);
        });
    }

    function redeem(menuId) {
        $.ajax({
            url: '/member/redeem',
            method: 'POST',
            data: {
                menu_id: menuId,
                _token: '{{ csrf_token() }}'
            },
            success: function(result) {
                 if (result.success) {
                    window.location.href = '/order/' + result.uuid;
                } else {
                    showSnackbar(result.message);
                }
            },
            error: function(err) {
                showSnackbar('Gagal menukar poin. Silakan coba lagi.');
            }
        });
    }

    $(document).ready(function() {
        getMemberMenu();

        $('#searchInput').on('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                getMemberMenu($(this).val());
            }
        });
    });
</script>
@endsection
