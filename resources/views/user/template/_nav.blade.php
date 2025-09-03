<div id="app-bar-navigation" class="parent-bottom-nav-container">
    <nav class="bottom-nav-container">
        <a href="/" class="nav-item {{ request()->is('/') ? 'active' : '' }}">
            <i class="fas fa-coffee"></i>
            <span>Menu</span>
        </a>

        <a href="/keranjang" class="nav-item {{ request()->is('keranjang') ? 'active' : '' }}">
            <i class="fas fa-shopping-cart"></i>
            <span>Keranjang</span>
            <span class="cart-badge">{{ $cartCount ?? 0 }}</span>
        </a>

        @if (Auth::check() || !$checkTransaction)
            <a href="/order" class="nav-item {{ request()->is('order*') ? 'active' : '' }}">
                <i class="fa-solid fa-receipt"></i>
                <span>Pesanan</span>
            </a>
        @else
            <a href="/order/{{$checkTransaction->uuid}}" class="nav-item {{ request()->is('order*') ? 'active' : '' }}">
                <i class="fa-solid fa-receipt"></i>
                <span>Pesanan</span>
            </a>
        @endif


        @if (Auth::check())
            <a href="/profile" class="nav-item {{ request()->is('profile') ? 'active' : '' }}">
                <i class="fas fa-user"></i>
                <span>Akun</span>
            </a>
        @else
            <a href="{{ route('login') }}" class="nav-item {{ request()->routeIs('login') ? 'active' : '' }}">
                <i class="fas fa-user"></i>
                <span>Login</span>
            </a>
        @endif
    </nav>
</div>
