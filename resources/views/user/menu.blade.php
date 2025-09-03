@extends('user.template._layout')
@section('title', 'Menu')
@section('content')
<div class="header">
    <div class="banner">
        <div class="banner-text">
            <h2>Disini Warkop</h2>
            <p style="font-size: 12px;">Banyak yang lebih berarti, Hanya Disini Warkop yang punya Arti</p>
        </div>
        <div class="banner-img">
            <img src="{{ asset('img/coffee.png') }}" alt="Coffee Shop">
        </div>
    </div>
    <div class="search-bar">
        <form action="{{ url('/dashboard-menu') }}" method="GET" id="searchForm">
            <input type="search" id="searchInput" name="search" placeholder="Cari menu..." value="{{ request('search') }}" />
            <button type="submit" style="display: none;"></button>
        </form>
    </div>
</div>

<div class="product-list" id="productList"></div>
@endsection
@section('overlay')
<div class="overlay" id="overlay">
    <div class="bottom-sheet" id="bottomSheet" role="dialog" aria-modal="true" aria-hidden="true">
        <div class="sheet-top-bar" aria-hidden="true" onclick="closeSheet()"></div>
        <div class="sheet-body">
            <img id="sheetImg" class="sheet-img" src="" alt="" />
            <div id="sheetTitle" class="sheet-title"></div>
            <div id="sheetPrice" class="sheet-price"></div>
            <p id="sheetDescription" class="sheet-description"></p>

            <div style="text-align:center;">
                <div class="quantity">
                    <button onclick="changeQty(-1)">−</button>
                    <div id="qty" style="min-width:28px; text-align:center; font-weight:700;">1</div>
                    <button onclick="changeQty(1)">+</button>
                </div>
                <button class="add-to-cart" onclick="addToCart()">Tambahkan ke Keranjang</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    let qty = 1;
        let selectedMenuId;
        let initialQty = 1;
        const productList = $('#productList');
        const overlay = $('#overlay');
        const sheet = $('#bottomSheet');
        const qtyEl = $('#qty');
        const sheetTitle = $('#sheetTitle');
        const sheetPrice = $('#sheetPrice');
        const sheetImg = $('#sheetImg');
        const sheetDescription = $('#sheetDescription');
        const cartBadge = $('.cart-badge');
        let error = "{{ session('error') ?? '' }}";

        // Fungsi untuk menampilkan snackbar
        function showSnackbar(message) {
            const snackbar = $('#snackbar');
            snackbar.text(message);
            snackbar.addClass('show');

            setTimeout(() => {
                snackbar.removeClass('show');
            }, 3000);
        }

        if (error) {
            showSnackbar(error);
        }

        // Fungsi untuk mengambil data dari server
        function getDataMenu(searchQuery = '') {
            $.ajax({
                url: `/api/get-menu?search=${encodeURIComponent(searchQuery)}`,
                method: 'GET',
                success: function(data) {
                    renderMenus(data);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching menus:', error);
                    showSnackbar('Gagal memuat menu. Coba refresh halaman!');
                }
            });
        }

        // Fungsi untuk merender data ke HTML
        function renderMenus(data) {
            productList.empty();

            // Loop untuk setiap kategori
            $.each(data.categories, function(index, category) {
                if (category.menus.length > 0) {
                    const categoryTitle = $('<div class="category-title"></div>').text(category.name);
                    productList.append(categoryTitle);

                    // Loop untuk setiap menu dalam kategori
                    $.each(category.menus, function(menuIndex, menu) {
                        const qtyInCart = data.cart[menu.id] ? data.cart[menu.id].qty : 0;
                        const productCard = $('<div class="product-card"></div>');
                        const isAvailable = menu.is_available == 1;

                        // Tambahkan kelas 'disabled' jika menu tidak tersedia
                        if (!isAvailable) {
                            productCard.addClass('disabled');
                        }

                        // Buat konten utama dari kartu produk
                        const clickableContent = $(`
                            <div class="parent-img">
                                <img src="{{ asset('storage') }}/${menu.image}" alt="${menu.name}" />
                            </div>
                            <div class="product-info">
                                <div class="product-name">${menu.name}</div>
                                <div class="product-price">Rp ${Number(menu.price).toLocaleString().replace(/,/g, '.')}</div>
                            </div>
                        `);

                        // Tambahkan event handler klik hanya jika menu tersedia
                        if (isAvailable) {
                            clickableContent.on('click', function() {
                                openSheet(menu.id, menu.name, menu.price, `{{ asset('storage') }}/${menu.image}`, menu.description);
                            });
                        }

                        // Tambahkan konten ke dalam kartu produk
                        productCard.append(clickableContent);

                        // Tambahkan overlay 'Tidak Tersedia' hanya jika menu tidak tersedia
                        if (!isAvailable) {
                            productCard.append('<div class="overlay-unavailable">Tidak Tersedia</div>');
                        } else {
                            // Tampilkan kontrol kuantitas jika produk tersedia dan sudah ada di keranjang
                            if (qtyInCart > 0) {
                                productCard.append(`
                                    <div class="quantity">
                                        <button class="qty-btn" onclick="updateQtyDirectly(${menu.id}, -1)">−</button>
                                        <span class="qty-display">${qtyInCart}</span>
                                        <button class="qty-btn" onclick="updateQtyDirectly(${menu.id}, 1)">+</button>
                                    </div>
                                `);
                            }
                        }

                        // Tambahkan kartu produk yang sudah lengkap ke dalam daftar produk
                        productList.append(productCard);
                    });
                }
            });

            cartBadge.text(data.cartCount);
        }

        // Panggil fungsi pengambilan data saat halaman dimuat
        $(document).ready(function() {
            const searchQuery = $('#searchInput').val();
            getDataMenu(searchQuery);
        });

        // Event listener untuk search bar
        $('#searchInput').on('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                getDataMenu($(this).val());
            }
        });

        $('#searchInput').on('search', function () {
            if ($(this).val().length === 0) {
                getDataMenu('');
            }
        });

        // Fungsi-fungsi untuk bottom sheet
        function openSheet(id, name, price, img, description) {
            if ($('.product-card[data-id="'+id+'"]').hasClass('disabled')) {
                showSnackbar('Produk ini tidak tersedia.');
                return;
            }
            selectedMenuId = id;
            overlay.css('display', 'flex');
            sheetTitle.text(name);
            sheetPrice.text('Rp ' + Number(price).toLocaleString().replace(/,/g, '.'));
            sheetImg.attr('src', img).attr('alt', name);
            sheetDescription.text(description);

            $.ajax({
                url: `/keranjang/item`,
                method: 'GET',
                data: { menu_id: id },
                success: function(result) {
                    if (result.success) {
                        qty = result.item.qty;
                        initialQty = result.item.qty;
                    } else {
                        qty = 1;
                        initialQty = 0;
                    }
                    qtyEl.text(qty);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching cart item:', error);
                    qty = 1;
                    initialQty = 0;
                    qtyEl.text(qty);
                }
            });

            sheet.attr('aria-hidden', 'false');
            setTimeout(() => {
                overlay.addClass('active');
            }, 100);
            setTimeout(() => sheet.focus?.(), 300);
        }

        function changeQty(delta) {
            qty = Math.max(1, qty + delta);
            qtyEl.text(qty);
        }

        function addToCart() {
            const addedQty = qty - initialQty;

            $.ajax({
                url: '/keranjang/tambah',
                method: 'POST',
                data: {
                    menu_id: selectedMenuId,
                    qty: addedQty,
                    _token: '{{ csrf_token() }}'
                },
                success: function(result) {
                    if (result.success) {
                        showSnackbar(result.message);
                        getDataMenu($('#searchInput').val());
                        closeSheet();
                    } else {
                        showSnackbar('Gagal menambahkan menu ke keranjang!');
                    }
                },
                error: function(xhr, status, error) {
                    showSnackbar('Gagal menambahkan menu ke keranjang!');
                }
            });
        }

        function closeSheet() {
            overlay.removeClass('active');
            sheet.attr('aria-hidden', 'true');
            setTimeout(() => {
                overlay.css('display', 'none');
            }, 100);
            getDataMenu($('#searchInput').val());
        }

        function updateQtyDirectly(menuId, delta) {
            $.ajax({
                url: '/keranjang/update-direct',
                method: 'POST',
                data: {
                    menu_id: menuId,
                    delta: delta,
                    _token: '{{ csrf_token() }}'
                },
                success: function(result) {
                    if (result.success) {
                        showSnackbar('Kuantitas berhasil diupdate!');
                        getDataMenu($('#searchInput').val());
                    } else {
                        showSnackbar('Gagal mengubah kuantitas!');
                    }
                },
                error: function(xhr, status, error) {
                    showSnackbar('Gagal mengubah kuantitas!');
                }
            });
        }

        function addToCartDirectly(menuId) {
            $.ajax({
                url: '/keranjang/tambah',
                method: 'POST',
                data: {
                    menu_id: menuId,
                    qty: 1,
                    _token: '{{ csrf_token() }}'
                },
                success: function(result) {
                    if (result.success) {
                        showSnackbar(result.message);
                        getDataMenu($('#searchInput').val());
                    } else {
                        showSnackbar('Gagal menambahkan menu!');
                    }
                },
                error: function(xhr, status, error) {
                    showSnackbar('Gagal menambahkan menu!');
                }
            });
        }

        // Event listener lainnya
        overlay.on('click', function(e) {
            if ($(e.target).is(overlay)) closeSheet();
        });

        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && overlay.hasClass('active')) closeSheet();
        });
</script>
@endsection
