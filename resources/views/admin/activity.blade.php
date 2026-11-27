@extends('layouts.admin')
@section('title', 'Activity log')

@section('content')
<div class="ad-page-head">
    <div>
        <h1>Activity log</h1>
        <p>Who changed what, and when.</p>
    </div>
    @can('activity.delete')
        <form method="POST" action="{{ route('admin.activity.clear') }}" data-confirm="Permanently clear the whole activity log?" data-confirm-button="Clear log">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger"><i class="fa-solid fa-broom me-1"></i> Clear log</button>
        </form>
    @endcan
</div>

<div class="ad-card">
    <div class="ad-card-head">
        <span class="small text-secondary">{{ $logs->total() }} entr{{ $logs->total() === 1 ? 'y' : 'ies' }}</span>
        <form method="GET" class="d-flex gap-2" role="search">
            <input type="search" name="q" value="{{ $search }}" class="form-control form-control-sm" placeholder="Search…" style="min-width:16rem">
            <button class="btn btn-soft btn-sm" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table ad-table">
            <thead><tr><th>When</th><th>Who</th><th>Action</th><th>Details</th><th class="d-none d-lg-table-cell">IP</th></tr></thead>
            <tbody>
            @forelse ($logs as $log)
                <tr>
                    <td class="text-nowrap small text-secondary" title="{{ $log->created_at }}">{{ $log->created_at?->diffForHumans() }}</td>
                    <td>{{ $log->user_name ?? 'System' }}</td>
                    <td><span class="ad-badge">{{ $log->action }}</span></td>
                    <td>{{ $log->description }}</td>
                    <td class="d-none d-lg-table-cell small text-secondary">{{ $log->ip_address }}</td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="ad-empty"><i class="fa-solid fa-clock-rotate-left"></i> Nothing logged yet.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($logs->hasPages())
        <div class="ad-card-foot justify-content-between align-items-center"><span class="small text-secondary">Page {{ $logs->currentPage() }} of {{ $logs->lastPage() }}</span>{{ $logs->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection
