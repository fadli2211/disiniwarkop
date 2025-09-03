@extends('user.template._layout')

@section('title', 'Verifikasi Akun')

@section('content')
<div class="login-page d-flex flex-column align-items-center justify-content-center">

    <!-- Logo & Judul -->
    <div class="text-center mb-4">
        <img src="{{ asset('img/logo.png') }}" alt="Logo Warkop" class="login-logo">
        <h2 class="fw-bold text-primary-coffee mt-3">Verifikasi Akun</h2>
        <p class="text-muted">Masukkan kode verifikasi 4 digit yang dikirim ke nomor HP Anda</p>
    </div>

    <!-- Form OTP -->
    <div class="login-form w-100" style="max-width: 400px;">
        <form action="{{ route('verify.otp') }}" method="POST">
            @csrf
            <div class="d-flex justify-content-between mb-4 otp-inputs">
                <input type="text" name="otp[]" maxlength="1" class="form-control otp-box" required>
                <input type="text" name="otp[]" maxlength="1" class="form-control otp-box" required>
                <input type="text" name="otp[]" maxlength="1" class="form-control otp-box" required>
                <input type="text" name="otp[]" maxlength="1" class="form-control otp-box" required>
            </div>

            <button type="submit" class="btn btn-coffee w-100 py-2">Verifikasi</button>
        </form>

        <div class="text-center mt-3">
            <small>
                Tidak menerima kode?
                <form action="{{ route('resend.verification') }}" method="post" class="d-inline">
                    @csrf
                    <button id="resend-btn" type="submit" class="btn text-primary-coffee p-0 m-0">
                        Kirim Ulang
                    </button>
                </form>
                <div id="countdown" class="text-muted mt-2"></div>
            </small>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $("#app-bar-navigation").addClass('d-none');
    let message = "{{ session('message') ?? '' }}";
    function showSnackbar(message) {
        const snackbar = $('#snackbar');
        snackbar.text(message);
        snackbar.addClass('show');

        setTimeout(() => {
            snackbar.removeClass('show');
        }, 3000);
    }

    if (message) {
        showSnackbar(message);
    }

    const inputs = document.querySelectorAll('.otp-box');
    inputs.forEach((input, index) => {
        input.addEventListener('input', () => {
            if (input.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !input.value && index > 0) {
                inputs[index - 1].focus();
            }
        });
    });

    let expiresAt = "{{ $expiresAt ?? '' }}";
    if (expiresAt) {
        let endTime = new Date(expiresAt).getTime();
        let resendBtn = document.getElementById("resend-btn");
        let countdownEl = document.getElementById("countdown");

        function updateCountdown() {
            let now = new Date().getTime();
            let distance = endTime - now;

            if (distance > 0) {
                resendBtn.disabled = true;
                let seconds = Math.floor((distance % (1000 * 60)) / 1000);
                let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                countdownEl.innerText = `Coba lagi dalam ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
            } else {
                resendBtn.disabled = false;
                countdownEl.innerText = "";
                clearInterval(timer);
            }
        }

        updateCountdown();
        let timer = setInterval(updateCountdown, 1000);
    }
</script>
@endsection
