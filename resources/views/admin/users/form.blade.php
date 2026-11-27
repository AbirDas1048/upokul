@extends('layouts.admin')
@section('title', $user->exists ? 'Edit admin user' : 'Add admin user')
@section('inline-errors', '1')

@section('content')
<div class="ad-page-head">
    <div>
        <div class="ad-crumbs"><a href="{{ route('admin.users.index') }}">Admin users</a> / {{ $user->exists ? 'Edit' : 'Add' }}</div>
        <h1>{{ $user->exists ? 'Edit '.$user->name : 'Add admin user' }}</h1>
    </div>
</div>

<form method="POST" data-once action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}" autocomplete="off">
    @csrf
    @if ($user->exists) @method('PUT') @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="ad-card">
                <div class="ad-card-head"><h2>Account</h2></div>
                <div class="ad-card-body">
                    <div class="row g-3">
                        @include('admin.partials.field', ['field' => ['name' => 'name', 'label' => 'Full name', 'type' => 'text', 'rules' => 'required', 'col' => 6], 'value' => $user->name])
                        @include('admin.partials.field', ['field' => ['name' => 'email', 'label' => 'Email (login)', 'type' => 'email', 'rules' => 'required', 'col' => 6], 'value' => $user->email])
                        <div class="col-md-6">
                            <label class="form-label" for="password">Password @unless ($user->exists) <span class="text-danger">*</span> @endunless</label>
                            <input class="form-control @error('password') is-invalid @enderror" type="password" id="password" name="password" autocomplete="new-password" @required(! $user->exists)>
                            @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            <div class="form-text">{{ $user->exists ? 'Leave blank to keep the current password. ' : '' }}Min. 10 characters, upper &amp; lower case, a number.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="password_confirmation">Confirm password</label>
                            <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="ad-card">
                <div class="ad-card-head"><h2>Access</h2></div>
                <div class="ad-card-body">
                    <label class="form-label" for="role_id">Role <span class="text-danger">*</span></label>
                    <select class="form-select @error('role_id') is-invalid @enderror" id="role_id" name="role_id" required>
                        <option value="">Choose a role…</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" @selected((string) old('role_id', $user->role_id) === (string) $role->id)>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('role_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    <div class="form-text">You can only pick roles that don't exceed your own permissions.
                        @can('roles.create') <a href="{{ route('admin.roles.create') }}">Create a role</a>. @endcan</div>

                    <div class="form-check form-switch mt-4">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" @checked(old('is_active', $user->is_active))>
                        <label class="form-check-label fw-semibold" for="is_active">Account is active</label>
                        <div class="form-text">Disabled accounts cannot sign in.</div>
                    </div>
                </div>
                <div class="ad-card-foot">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> {{ $user->exists ? 'Save changes' : 'Create user' }}</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
