@extends('user.template._layout')

@section('title', 'Register')

@section('content')
<div class="login-page d-flex flex-column align-items-center justify-content-center">

    <!-- Logo & Judul -->
    <div class="text-center mb-4">
        <img src="{{ asset('img/logo.png') }}" alt="Logo Warkop" class="login-logo">
        <h2 class="fw-bold text-primary-coffee mt-3">Disini Warkop</h2>
        <p class="text-muted">Buat akun baru untuk bergabung</p>
    </div>

    <!-- Form Register -->
    <div class="login-form w-100" style="max-width: 400px;">
        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-bold text-tertiary">Nama Lengkap</label>
                <input type="text" name="name" class="form-control form-control-lg @error('name') is-invalid @enderror"
                    placeholder="Masukkan nama" value="{{ old('name') }}" required>
                @error('name')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold text-tertiary">Username</label>
                <input type="text" name="username"
                    class="form-control form-control-lg @error('username') is-invalid @enderror"
                    placeholder="Masukkan username" value="{{ old('username') }}" required>
                @error('username')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold text-tertiary">Email</label>
                <input type="email" name="email"
                    class="form-control form-control-lg @error('email') is-invalid @enderror"
                    placeholder="Masukkan email" value="{{ old('email') }}" required>
                @error('email')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold text-tertiary">Nomor HP</label>
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-light border-0 fw-bold text-tertiary">+62</span>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                        placeholder="8123456789" value="{{ old('phone') }}" required>
                </div>
                @error('phone')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold text-tertiary">Password</label>
                <input type="password" name="password"
                    class="form-control form-control-lg @error('password') is-invalid @enderror"
                    placeholder="Masukkan password" required>
                @error('password')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold text-tertiary">Konfirmasi Password</label>
                <input type="password" name="password_confirmation"
                    class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror"
                    placeholder="Ulangi password" required>
                @error('password_confirmation')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="btn btn-coffee w-100 py-2">Daftar</button>
        </form>

        <div class="text-center mt-3">
            <small>
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-primary-coffee fw-bold">Login</a>
            </small>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $("#app-bar-navigation").addClass('d-none');
</script>
@endsection
