@extends('user.template._layout')

@section('title', 'Login')

@section('content')
<div class="login-wrapper">
    <div class="login-page">
        <!-- Konten utama (tengah layar) -->
        <div class="login-content">
            <div class="text-center mb-4">
                <img src="{{ asset('img/logo.png') }}" alt="Logo Warkop" class="login-logo">
                <h2 class="fw-bold text-primary-coffee mt-3">Disini Warkop</h2>
                <p class="text-muted">Silakan masuk untuk melanjutkan</p>
            </div>

            <div class="login-form">
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold text-tertiary">Username</label>
                        <input type="text" name="username" class="form-control form-control-lg" placeholder="Masukkan username" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-tertiary">Password</label>
                        <input type="password" name="password" class="form-control form-control-lg" placeholder="Masukkan password" required>
                    </div>

                    <button type="submit" class="btn btn-coffee w-100 py-2">Login</button>
                </form>

                <div class="text-center mt-3">
                    <small>
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="text-primary-coffee fw-bold">Daftar</a>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer-global">
        <div class="d-flex justify-content-center gap-4 mb-2">
            <a href="https://www.instagram.com/disini_warkop" target="_blank" class="text-dark fs-4">
                <i class="fab fa-instagram"></i>
            </a>
            <a href="https://www.tiktok.com/@disiniwarkop" target="_blank" class="text-dark fs-4">
                <i class="fab fa-tiktok"></i>
            </a>
            <a href="https://www.google.com/maps/place/Disini+Warkop" target="_blank" class="text-dark fs-4">
                <i class="fas fa-map-marker-alt"></i>
            </a>
        </div>
        <small class="text-muted">&copy; {{ date('Y') }} Disini Warkop</small>
    </footer>
</div>
@endsection

@section('script')
<script>
    $("#app-bar-navigation").addClass('d-none');
</script>
@endsection
