@extends('layouts.admin')
@section('title', 'Roles & permissions')

@section('content')
<div class="ad-page-head">
    <div>
        <h1>Roles &amp; permissions</h1>
        <p>A role is a named set of permissions. Give each admin user a role so they can only view, add, edit or delete what you allow.</p>
    </div>
    @can('roles.create')
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i> New role</a>
    @endcan
</div>

<div class="ad-card mb-4" style="border-color:var(--ad-gold)">
    <div class="ad-card-body d-flex align-items-center gap-3">
        <span class="ad-ico-tile" style="background:rgba(201,162,78,.18);color:var(--ad-gold)"><i class="fa-solid fa-crown"></i></span>
        <div><div class="fw-semibold">Master administrator</div><div class="small text-secondary">Created during installation. Always has every permission, needs no role, and cannot be edited, limited or removed by other admins.</div></div>
    </div>
</div>

<div class="row g-3">
    @forelse ($roles as $role)
        @php $count = (int) ($permissionCounts[$role->id] ?? 0); $manageable = \App\Support\AdminGuard::canManageRole($actor, $role); @endphp
        <div class="col-md-6 col-xl-4">
            <div class="ad-card h-100">
                <div class="ad-card-body d-flex flex-column h-100">
                    <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                        <h2 class="h5 mb-0">{{ $role->name }}</h2>
                        <span class="ad-badge">{{ $role->users_count }} user{{ $role->users_count === 1 ? '' : 's' }}</span>
                    </div>
                    <p class="text-secondary small flex-grow-1">{{ $role->description ?: 'No description.' }}</p>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1"><span class="text-secondary">Permissions</span><b>{{ $count }} / {{ $totalPermissions }}</b></div>
                        <div class="progress" style="height:6px"><div class="progress-bar" style="width: {{ round($count / max(1, $totalPermissions) * 100) }}%; background: linear-gradient(90deg,#12868a,#23cdd2)"></div></div>
                    </div>
                    <div class="d-flex gap-2">
                        @if ($manageable)
                            @can('roles.update') <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-soft btn-sm"><i class="fa-solid fa-pen me-1"></i> Edit</a> @endcan
                            @can('roles.delete')
                                <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" data-confirm="Delete the role &quot;{{ $role->name }}&quot;?" data-confirm-button="Delete">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            @endcan
                        @else
                            <span class="small text-secondary"><i class="fa-solid fa-lock me-1"></i> Has permissions you don't have</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12"><div class="ad-card"><div class="ad-empty"><i class="fa-solid fa-user-shield"></i> No roles yet.</div></div></div>
    @endforelse
</div>
@endsection
