@extends('layouts.admin')
@section('title', 'Admin users')

@section('content')
<div class="ad-page-head">
    <div>
        <h1>Admin users</h1>
        <p>People who can sign in to this panel. What they can do is decided by their role.</p>
    </div>
    @can('users.create')
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="fa-solid fa-user-plus me-1"></i> Add admin user</a>
    @endcan
</div>

<div class="ad-card">
    <div class="ad-card-head">
        <span class="small text-secondary">{{ $users->total() }} user(s)</span>
        <form method="GET" class="d-flex gap-2" role="search">
            <input type="search" name="q" value="{{ $search }}" class="form-control form-control-sm" placeholder="Search name or e-mail…" style="min-width:16rem">
            <button class="btn btn-soft btn-sm" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table ad-table">
            <thead><tr><th>User</th><th>Role</th><th>Status</th><th class="d-none d-md-table-cell">Last sign-in</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @forelse ($users as $u)
                @php $manageable = \App\Support\AdminGuard::canManageUser(auth()->user(), $u); @endphp
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <span class="ad-avatar">{{ $u->initials() }}</span>
                            <div><div class="fw-semibold">{{ $u->name }} @if ($u->is(auth()->user())) <span class="ad-badge mute ms-1">You</span> @endif</div><div class="small text-secondary">{{ $u->email }}</div></div>
                        </div>
                    </td>
                    <td>
                        @if ($u->isMaster()) <span class="ad-badge warn"><i class="fa-solid fa-crown"></i> Master admin</span>
                        @elseif ($u->role) <span class="ad-badge">{{ $u->role->name }}</span>
                        @else <span class="ad-badge mute">No role</span> @endif
                    </td>
                    <td><span class="ad-badge {{ $u->is_active ? 'ok' : 'bad' }}">{{ $u->is_active ? 'Active' : 'Disabled' }}</span></td>
                    <td class="d-none d-md-table-cell text-secondary small">{{ $u->last_login_at?->diffForHumans() ?? 'Never' }}</td>
                    <td class="text-end text-nowrap">
                        @if ($manageable)
                            @can('users.update')
                                <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-soft btn-sm" title="Edit"><i class="fa-solid fa-pen"></i></a>
                            @endcan
                            @can('users.delete')
                                <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="d-inline" data-confirm="Delete {{ $u->name }}? They will no longer be able to sign in." data-confirm-button="Delete">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            @endcan
                        @else
                            <span class="text-secondary small" title="{{ $u->isMaster() ? 'The master admin cannot be changed here' : ($u->is(auth()->user()) ? 'Use My profile for your own account' : 'This account has more access than you') }}" data-bs-toggle="tooltip"><i class="fa-solid fa-lock"></i></span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="ad-empty"><i class="fa-solid fa-users"></i> No users found.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($users->hasPages())
        <div class="ad-card-foot justify-content-between align-items-center"><span class="small text-secondary">Page {{ $users->currentPage() }} of {{ $users->lastPage() }}</span>{{ $users->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection
