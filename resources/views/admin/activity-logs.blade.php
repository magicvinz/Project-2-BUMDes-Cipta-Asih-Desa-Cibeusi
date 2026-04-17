@extends('layouts.app')

@section('title', 'Log Aktivitas Sistem')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fs-4 fw-semibold mb-0">Log Aktivitas Sistem</h4>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-uppercase text-secondary small px-4 py-3">Waktu</th>
                        <th class="text-uppercase text-secondary small px-4 py-3">Pengguna / Peran</th>
                        <th class="text-uppercase text-secondary small px-4 py-3">Aksi</th>
                        <th class="text-uppercase text-secondary small px-4 py-3">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($logs as $log)
                    <tr>
                        <td class="px-4 py-3 ws-nowrap text-muted small">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-4 py-3">
                            @if($log->user)
                                <div class="fw-medium">{{ $log->user->name }}</div>
                                <div class="small text-muted">{{ ucfirst(str_replace('_', ' ', $log->user->role)) }}</div>
                            @else
                                <span class="text-muted fst-italic">Sistem / Guest</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $badgeColor = 'secondary';
                                if($log->action === 'login') $badgeColor = 'info';
                                if($log->action === 'register') $badgeColor = 'primary';
                                if($log->action === 'create_tiket') $badgeColor = 'success';
                                if($log->action === 'validasi_tiket') $badgeColor = 'warning text-dark';
                                if($log->action === 'update_profil') $badgeColor = 'dark';
                            @endphp
                            <span class="badge bg-{{ $badgeColor }}">{{ strtoupper(str_replace('_', ' ', $log->action)) }}</span>
                        </td>
                        <td class="px-4 py-3 text-muted small">
                            {{ $log->description ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">Belum ada log aktivitas yang terekam.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $logs->links('pagination::bootstrap-5') }}
</div>
@endsection
