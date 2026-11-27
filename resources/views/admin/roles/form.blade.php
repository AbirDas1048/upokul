@extends('layouts.admin')
@section('title', $role->exists ? 'Edit role' : 'New role')
@section('inline-errors', '1')

@section('content')
@php
    $modules = config('permissions.modules');
    $groups = config('permissions.groups');
    $granted = array_flip(old('permissions', $granted));
    $held = array_flip($actorKeys);
    $columns = ['view' => 'View', 'create' => 'Add', 'update' => 'Edit', 'delete' => 'Delete'];
@endphp
<div class="ad-page-head">
    <div>
        <div class="ad-crumbs"><a href="{{ route('admin.roles.index') }}">Roles</a> / {{ $role->exists ? 'Edit' : 'New' }}</div>
        <h1>{{ $role->exists ? 'Edit role: '.$role->name : 'New role' }}</h1>
        <p>Tick what people with this role are allowed to do. Add, Edit and Delete always include View.</p>
    </div>
</div>

<form method="POST" data-once action="{{ $role->exists ? route('admin.roles.update', $role) : route('admin.roles.store') }}">
    @csrf
    @if ($role->exists) @method('PUT') @endif

    <div class="ad-card mb-4">
        <div class="ad-card-body">
            <div class="row g-3">
                @include('admin.partials.field', ['field' => ['name' => 'name', 'label' => 'Role name', 'type' => 'text', 'rules' => 'required', 'col' => 5, 'placeholder' => 'e.g. Content Editor'], 'value' => $role->name])
                @include('admin.partials.field', ['field' => ['name' => 'description', 'label' => 'Description', 'type' => 'text', 'rules' => 'nullable', 'col' => 7, 'placeholder' => 'What is this role for?'], 'value' => $role->description])
            </div>
        </div>
    </div>

    <div class="ad-card" data-matrix>
        <div class="ad-card-head">
            <h2>Permissions <span class="ad-badge ms-2"><span data-perm-count>0</span> selected</span></h2>
            <div class="form-check form-switch m-0">
                <input class="form-check-input" type="checkbox" role="switch" id="all" data-all>
                <label class="form-check-label small fw-semibold" for="all">Select everything I can grant</label>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table ad-table ad-matrix align-middle">
                <thead>
                <tr>
                    <th>Module</th>
                    @foreach ($columns as $action => $label)
                        <th style="width:7rem">
                            <div class="form-check d-inline-flex flex-column align-items-center gap-1 m-0 p-0">
                                <input class="form-check-input m-0" type="checkbox" data-col="{{ $action }}" id="col_{{ $action }}" aria-label="All {{ $label }}">
                                <label for="col_{{ $action }}" class="mb-0">{{ $label }}</label>
                            </div>
                        </th>
                    @endforeach
                    <th style="width:6rem">All</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($groups as $groupKey => $groupLabel)
                    @php $inGroup = collect($modules)->filter(fn ($m) => $m['group'] === $groupKey); @endphp
                    @continue($inGroup->isEmpty())
                    <tr class="group-row"><td colspan="{{ count($columns) + 2 }}">{{ $groupLabel }}</td></tr>
                    @foreach ($inGroup as $module => $def)
                        <tr>
                            <td><div class="d-flex align-items-center gap-3"><span class="ad-ico-tile" style="width:2.1rem;height:2.1rem"><i class="fa-solid {{ $def['icon'] }}"></i></span><span class="fw-medium">{{ $def['label'] }}</span></div></td>
                            @foreach ($columns as $action => $label)
                                <td>
                                    @if (isset($def['actions'][$action]))
                                        @php $key = "{$module}.{$action}"; $can = isset($held[$key]); @endphp
                                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $key }}"
                                               data-perm data-module="{{ $module }}" data-action="{{ $action }}"
                                               title="{{ $def['actions'][$action] }}{{ $can ? '' : ' — you do not have this permission, so you cannot grant it' }}"
                                               aria-label="{{ $def['label'] }}: {{ $def['actions'][$action] }}"
                                               @checked(isset($granted[$key])) @disabled(! $can && ! isset($granted[$key]))>
                                    @else
                                        <span class="na">—</span>
                                    @endif
                                </td>
                            @endforeach
                            <td><input class="form-check-input" type="checkbox" data-row="{{ $module }}" aria-label="All {{ $def['label'] }} permissions"></td>
                        </tr>
                    @endforeach
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="ad-card-foot">
            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> {{ $role->exists ? 'Save role' : 'Create role' }}</button>
        </div>
    </div>
</form>
@endsection
