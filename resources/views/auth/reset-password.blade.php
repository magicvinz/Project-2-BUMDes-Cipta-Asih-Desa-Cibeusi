@extends('layouts.app')

@section('title', 'Reset Kata Sandi')

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
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-circle mb-3" style="width:56px;height:56px">
                        <i class="bi bi-shield-lock fs-4 text-success"></i>
                    </div>
                    <h4 class="fw-semibold mb-1">Buat Kata Sandi Baru</h4>
                    <p class="text-muted small mb-0">Pastikan kata sandi baru Anda kuat dan mudah diingat.</p>
                </div>

                <form method="POST" action="{{ route('password.update.reset') }}">
                    @csrf

                    {{--
                        Token dikirim sebagai hidden field.
                        Token ini dibuat oleh Laravel saat user klik link di email,
                        dan akan divalidasi di controller untuk memastikan request sah.
                    --}}
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="mb-3">
                        <label class="form-label fw-medium">Email</label>
                        <input type="email" name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $email) }}"
                            placeholder="nama@email.com"
                            required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Kata Sandi Baru</label>
                        <div class="password-wrapper">
                            <input type="password" name="password" id="new_password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Minimal 8 karakter" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('new_password', this)" tabindex="-1">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium">Konfirmasi Kata Sandi Baru</label>
                        <div class="password-wrapper">
                            <input type="password" name="password_confirmation" id="new_password_conf"
                                class="form-control"
                                placeholder="Ulangi kata sandi baru" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('new_password_conf', this)" tabindex="-1">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100 py-2 fw-medium">
                        <i class="bi bi-check-lg me-1"></i> Simpan Kata Sandi Baru
                    </button>
                </form>
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
