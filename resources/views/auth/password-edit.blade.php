@extends('layouts.app')

@section('title', 'Ubah Kata Sandi')

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
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h4 class="card-title fw-bold text-primary mb-4 text-center">Ubah Kata Sandi</h4>

                <form action="{{ route('password.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="current_password" class="form-label fw-medium">Kata Sandi Saat Ini</label>
                        <div class="password-wrapper">
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('current_password', this)" tabindex="-1">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-medium">Kata Sandi Baru</label>
                        <div class="password-wrapper">
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('password', this)" tabindex="-1">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-medium">Konfirmasi Kata Sandi Baru</label>
                        <div class="password-wrapper">
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('password_confirmation', this)" tabindex="-1">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-medium">Simpan Perubahan Kata Sandi</button>
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
