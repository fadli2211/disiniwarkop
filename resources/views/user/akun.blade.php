@extends('user.template._layout')
@section('title', 'Akun Saya')
@section('content')
<div class="header">
    <div class="banner-akun">
        <div class="profile-card">
            <div class="icon-user">
                <i class="fas fa-user-circle fa-3x"></i>
            </div>
            <div>
                <h2 class="profile-name">{{ Auth::user()->name }}</h2>
                <p class="profile-email">+62 {{ Auth::user()->phone }}</p>
            </div>
        </div>
        <div class="icon-user d-none"
            onclick="openSheet('Kopi Latte', 15000, 'https://cdn.pixabay.com/photo/2015/09/09/18/09/coffee-932259_1280.jpg')">
            <i class="fa-solid fa-pen"></i>
        </div>
    </div>
</div>
<div class="profile-container">
    <h2 class="akun-title">Akun</h2>
    <div class="profile-menu">
        {{-- Riwayat Pembelian --}}
        <div class="profile-menu-item" id="order-history">
            <div class="profile-menu-item-right"><i class="fas fa-receipt"></i><span>Riwayat Pembelian</span></div>
            <span class="profile-menu-item-left">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
        </div>

        {{-- Poin Member --}}
        <div class="profile-menu-item" id="member-menu">
            <div class="profile-menu-item-right"><i class="fas fa-coins"></i><span>Poin Member</span></div>
            <span class="profile-menu-item-left">{{ Auth::user()->member_point }}</span>
        </div>

        {{-- Logout --}}
        <div class="profile-menu-item" onclick="confirmLogout()">
            <div class="profile-menu-item-right"><i class="fas fa-sign-out-alt"></i><span>Logout</span></div>
            <span class="profile-menu-item-left"><i class="fa-solid fa-chevron-right"></i></span>
        </div>
    </div>
</div>
@endsection

@section('overlay')
<div class="overlay" id="overlay">
    <div class="bottom-sheet" id="bottomSheet" role="dialog" aria-modal="true" aria-hidden="true">
        <div class="sheet-top-bar" aria-hidden="true" onclick="closeSheet()"></div>
        <div class="sheet-body">
            <div class="form-input-group">
                <div id="sheetTitle" class="sheet-title-2">Nama</div>
                <input class="input-form" placeholder="Masukkan nama" />
            </div>
            <div class="form-input-group">
                <div id="sheetTitle" class="sheet-title-2">Email</div>
                <input class="input-form" placeholder="Masukkan email" />
            </div>
            <div class="form-input-group">
                <div id="sheetTitle" class="sheet-title-2">Nomor HP</div>
                <input class="input-form" placeholder="Masukkan nomor HP" />
            </div>
            <div class="form-input-group">
                <div id="sheetTitle" class="sheet-title-2">Username</div>
                <input class="input-form" placeholder="Masukkan username" />
            </div>
            <div class="form-input-group">
                <div id="sheetTitle" class="sheet-title-2">Password</div>
                <input class="input-form" type="password" placeholder="Masukkan password" />
            </div>
            <div style="text-align:center;">
                <button class="add-to-cart">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    const overlay = document.getElementById('overlay');
    overlay.style.display = "none";
    const sheet = document.getElementById('bottomSheet');

    function openSheet() {
        overlay.style.display = "flex";
        setTimeout(() => {
            overlay.classList.add('active');
        }, 100);
        setTimeout(() => sheet.focus?.(), 300);
    }

    function closeSheet() {
        overlay.classList.remove('active');
        sheet.setAttribute('aria-hidden', 'true');
        setTimeout(() => {
            overlay.style.display = "none";
        }, 100);
    }

    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) closeSheet();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && overlay.classList.contains('active')) closeSheet();
    });

    $('#order-history').on('click', function () {
        window.location.href = '/order';
    });

    $('#member-menu').on('click', function () {
        window.location.href = '/member';
    });

    function confirmLogout() {
        if (confirm("Yakin ingin keluar dari akun?")) {
            window.location.href = "{{ url('/logout') }}";
        }
    }
</script>
@endsection
