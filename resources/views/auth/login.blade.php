@extends('layouts.app')

@section('title', 'Login')

@push('styles')
<style>
    .password-wrapper { position: relative; }
    .password-wrapper .form-control { padding-right: 2.8rem; }
    .toggle-password {
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        padding: 0;
        color: #adb5bd;
        cursor: pointer;
        font-size: 1rem;
        line-height: 1;
        transition: color 0.2s;
    }
    .toggle-password:hover { color: #6c757d; }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-5">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-4 p-md-5">
                <h4 class="card-title fs-4 fw-semibold mb-4 text-center">Login SI-ASIH</h4>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="password-wrapper">
                            <input type="password" name="password" id="login_password" class="form-control" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('login_password', this)" tabindex="-1">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                        <div class="text-end mt-1">
                            <a href="{{ route('password.request') }}" class="small text-muted text-decoration-none">Lupa kata sandi?</a>
                        </div>
                    </div>
                    <div class="mb-4 form-check">
                        <input type="checkbox" name="remember" id="remember" class="form-check-input">
                        <label for="remember" class="form-check-label">Ingat saya</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-medium">Login</button>

                    <div class="position-relative my-4">
                        <hr class="text-muted">
                        <span class="position-absolute top-50 start-50 translate-middle bg-white px-2 small text-muted">atau</span>
                    </div>

                    <a href="{{ route('login.google') }}" class="btn btn-outline-danger w-100 py-2 d-flex align-items-center justify-content-center gap-2 fw-medium">
                        <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.16 7.09-10.27 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg>
                        Login dengan Google
                    </a>
                </form>
                <p class="mt-4 mb-0 text-center small text-muted">Belum punya akun? <a href="{{ route('register') }}" class="text-primary text-decoration-none">Daftar</a></p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    }
}
</script>
@endpush
