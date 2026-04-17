<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class ForgotPasswordController extends Controller
{
    // ─────────────────────────────────────────────
    // BAGIAN 1: Kirim Link Reset ke Email
    // ─────────────────────────────────────────────

    /**
     * Tampilkan form input email (GET /lupa-password).
     * Pengguna akan mengisi email di sini.
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Proses pengiriman link reset (POST /lupa-password).
     *
     * Password::sendResetLink() akan:
     * 1. Cek apakah email ada di tabel users
     * 2. Buat token unik, simpan ke tabel password_reset_tokens
     * 3. Kirim email berisi link dengan token tersebut
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        // $status berisi konstanta string dari Laravel:
        // Password::RESET_LINK_SENT → berhasil
        // Password::INVALID_USER   → email tidak ditemukan
        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Link reset kata sandi telah dikirim ke email Anda.')
            : back()->withErrors(['email' => 'Email tidak ditemukan dalam sistem kami.']);
    }

    // ─────────────────────────────────────────────
    // BAGIAN 2: Reset Password dengan Token
    // ─────────────────────────────────────────────

    /**
     * Tampilkan form password baru (GET /reset-password/{token}).
     * URL ini didapat dari link di email — token ada di URL-nya.
     */
    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email, // email dikirim sebagai query string di link
        ]);
    }

    /**
     * Proses penyimpanan password baru (POST /reset-password).
     *
     * Password::reset() akan:
     * 1. Validasi token (cocok & belum expired)
     * 2. Update password user di tabel users
     * 3. Hapus token dari tabel password_reset_tokens
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // Simpan password baru yang sudah di-hash
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // $status berisi konstanta:
        // Password::PASSWORD_RESET → berhasil
        // Password::INVALID_TOKEN  → token tidak valid atau sudah expired (60 menit)
        // Password::INVALID_USER   → email tidak ditemukan
        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Kata sandi berhasil diubah! Silakan login.')
            : back()->withErrors(['email' => __($status)]);
    }
}
