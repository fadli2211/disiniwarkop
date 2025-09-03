<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="https://www.instagram.com/disini_warkop?igsh=MWwyeGFib2o2NDVmeg==" class="b-brand d-flex align-items-center text-decoration-none" target="_blank" >
                <img src="{{ asset('img/logo.svg') }}" alt="Logo Warkop" class="img-fluid" style="height: 45px; width: auto;">
                <span class="mt-1 ms-2 fw-bold text-dark fs-5">
                    Disini Warkop
                </span>
            </a>
        </div>
        <div class="navbar-content">
            <ul class="pc-navbar">
                {{-- Untuk Admin --}}
                @if(auth()->check() && auth()->user()->role === 'admin')
                    <li class="pc-item">
                        <a href="{{ route('admin.dashboard') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                            <span class="pc-mtext">Dashboard</span>
                        </a>
                    </li>

                    <li class="pc-item">
                        <a href="{{ route('admin.user') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-user"></i></span>
                            <span class="pc-mtext">User</span>
                        </a>
                    </li>

                    <li class="pc-item">
                        <a href="{{ route('admin.category.index') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-layout-grid"></i></span>
                            <span class="pc-mtext">Kategori</span>
                        </a>
                    </li>

                    <li class="pc-item">
                        <a href="{{ route('admin.menu.index') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-file-text"></i></span>
                            <span class="pc-mtext">Menu</span>
                        </a>
                    </li>

                    <li class="pc-item">
                        <a href="{{ route('admin.menu-member.index') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-certificate"></i></span>
                            <span class="pc-mtext">Menu Member</span>
                        </a>
                    </li>

                    <li class="pc-item">
                        <a href="{{ route('admin.tables.index') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-qrcode"></i></span>
                            <span class="pc-mtext">Meja</span>
                        </a>
                    </li>

                    <li class="pc-item">
                        <a href="{{ route('admin.orders.index') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-shopping-cart"></i></span>
                            <span class="pc-mtext">Pesanan</span>
                        </a>
                    </li>
                @endif

                {{-- Untuk User --}}
                @if(auth()->check() && auth()->user()->role === 'user')
                    <li class="pc-item">
                        <a href="{{ route('user.dashboard') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                            <span class="pc-mtext">Dashboard</span>
                        </a>
                    </li>

                    <li class="pc-item">
                        <a href="{{ route('user.laporan') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-file-plus"></i></span>
                            <span class="pc-mtext">Buat Laporan</span>
                        </a>
                    </li>
                    <li class="pc-item">
                        <a href="{{ route('user.laporan.saya') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-file-text"></i></span>
                            <span class="pc-mtext">Laporan Saya</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>
