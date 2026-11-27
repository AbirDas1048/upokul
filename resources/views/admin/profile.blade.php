@extends('layouts.admin')
@section('title', 'My profile')
@section('inline-errors', '1')

@section('content')
<div class="ad-page-head">
    <div><h1>My profile</h1><p>Your own sign-in details. Nobody else can change these but you.</p></div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="ad-card text-center">
            <div class="ad-card-body py-4">
                <span class="ad-avatar mx-auto mb-3" style="width:5rem;height:5rem;font-size:1.6rem">{{ $user->initials() }}</span>
                <h2 class="h5 mb-1">{{ $user->name }}</h2>
                <div class="text-secondary small mb-3">{{ $user->email }}</div>
                @if ($user->isMaster()) <span class="ad-badge warn"><i class="fa-solid fa-crown"></i> Master administrator</span>
                @else <span class="ad-badge">{{ $user->role?->name ?? 'No role' }}</span> @endif
                <div class="small text-secondary mt-3">Last sign-in: {{ $user->last_login_at?->diffForHumans() ?? '—' }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.profile.update') }}" data-once>
            @csrf @method('PUT')
            <div class="ad-card">
                <div class="ad-card-body">
                    <div class="row g-3">
                        @include('admin.partials.field', ['field' => ['name' => 'name', 'label' => 'Full name', 'type' => 'text', 'rules' => 'required', 'col' => 6], 'value' => $user->name])
                        @include('admin.partials.field', ['field' => ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'rules' => 'required', 'col' => 6], 'value' => $user->email])
                        <div class="col-12"><hr class="my-1"><div class="fw-semibold mb-1">Change password <span class="text-secondary fw-normal small">(optional)</span></div></div>
                        <div class="col-md-4">
                            <label class="form-label" for="current_password">Current password</label>
                            <input class="form-control @error('current_password') is-invalid @enderror" type="password" id="current_password" name="current_password" autocomplete="current-password">
                            @error('current_password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="password">New password</label>
                            <input class="form-control @error('password') is-invalid @enderror" type="password" id="password" name="password" autocomplete="new-password">
                            @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="password_confirmation">Confirm</label>
                            <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
                        </div>
                    </div>
                </div>
                <div class="ad-card-foot"><button class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Save profile</button></div>
            </div>
        </form>
    </div>
</div>
@endsection
