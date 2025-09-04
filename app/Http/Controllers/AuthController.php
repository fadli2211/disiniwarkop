<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\WablasService;

class AuthController extends Controller
{
    protected $wablas;

    public function __construct(WablasService $wablas)
    {
        $this->wablas = $wablas;
    }

    public function login()
    {
        $cart = session()->get('cart', []);
        $cartCount = count($cart);

        $tableCode = session()->get('table_code', null);

        $checkTransaction = Order::where('table_code', $tableCode)->where('status', '!=', 3)->first();

        return view('auth.login')
            ->with('checkTransaction', $checkTransaction)
            ->with('cartCount', $cartCount);
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } else {
                if (is_null($user->verified_at)) {
                    Verification::where('user_id', $user->id)->delete();

                    $otp = $this->generateUniqueOtp();

                    Verification::create([
                        'user_id'    => $user->id,
                        'otp'        => $otp,
                        'expires_at' => now()->addMinutes(5),
                    ]);

                    $message = "Hai {$user->name} 👋\n\n"
                        . "Selamat datang di *Disini Warkop*!\n\n"
                        . "Kode verifikasi kamu adalah: *{$otp}* 🍀\n\n"
                        . "Kode ini berlaku 5 menit ya.\n"
                        . "Jangan dibagikan ke siapa pun biar akunmu tetap aman.\n\n"
                        . "Sampai ketemu di Disini Warkop ☕✨";

                    $this->wablas->sendMessage('62' . $user->phone, $message);

                    return redirect()->route('verify')
                        ->with('message', 'Kode verifikasi sudah dikirim ke WhatsApp kamu.');
                }

                return redirect()->route('user.menu');
            }
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->withInput();
    }

    public function register()
    {
        $cart = session()->get('cart', []);
        $cartCount = count($cart);

        $tableCode = session()->get('table_code', null);

        $checkTransaction = Order::where('table_code', $tableCode)->where('status', '!=', 3)->first();
        return view('auth.register')->with('checkTransaction', $checkTransaction)->with('cartCount', $cartCount);
    }

    public function createAccount(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|max:12|unique:users',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'phone' => $request->phone,
        ]);

        Auth::login($user);

        $otp = $this->generateUniqueOtp();

        Verification::create([
            'user_id' => $user->id,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(5),
        ]);

        $message = "Hai $user->name 👋\n\nSelamat datang di *Disini Warkop*!\n\nKode verifikasi kamu adalah: *$otp* 🍀\n\nKode ini berlaku 5 menit ya.\nJangan dibagikan ke siapa pun biar akunmu tetap aman.\n\nSampai ketemu di Disini Warkop ☕✨";
        $this->wablas->sendMessage('62' . $user->phone, $message);

        return redirect()->route('verify');
    }

    public function verification()
    {
        $cart = session()->get('cart', []);
        $cartCount = count($cart);
        $tableCode = session()->get('table_code', null);
        $checkTransaction = Order::where('table_code', $tableCode)
            ->where('status', '!=', 3)
            ->first();

        $user = Auth::user();

        $verification = \App\Models\Verification::where('user_id', $user->id)
            ->latest()
            ->first();

        $expiresAt = $verification?->expires_at;

        return view('auth.verification', compact('user', 'checkTransaction', 'cartCount', 'expiresAt'));
    }


    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required',
        ]);

        $otp = implode('', $request->otp);

        $user = Auth::user();
        $verification = Verification::where('user_id', $user->id)
            ->where('otp', $otp)
            ->where('expires_at', '>', now())
            ->first();

        if ($verification) {
            $user->verified_at = now();
            $user->save();

            $verification->delete();

            return redirect()->route('user.menu');
        } else {
            return back()->with(['message' => 'Kode verifikasi salah atau sudah kadaluarsa.']);
        }
    }

    public function sendVerification()
    {
        $user = Auth::user();

        $otp = $this->generateUniqueOtp();

        $lastVerification = Verification::where('user_id', $user->id)->latest()->first();
        if ($lastVerification) {
            $lastVerification->delete();
        }

        Verification::create([
            'user_id' => $user->id,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(5),
        ]);

        $message = "Hai $user->name 👋\n\nSelamat datang di *Disini Warkop*!\n\nKode verifikasi kamu adalah: *$otp* 🍀\n\nKode ini berlaku 5 menit ya.\nJangan dibagikan ke siapa pun biar akunmu tetap aman.\n\nSampai ketemu di Disini Warkop ☕✨";
        $this->wablas->sendMessage('62' . $user->phone, $message);

        return back()->with(['message' => 'Kode verifikasi baru telah dikirim ke WhatsApp kamu.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }

    private function generateUniqueOtp()
    {
        do {
            $otp = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (Verification::where('otp', $otp)->exists());

        return $otp;
    }
}
