@extends('layouts.admin')
@section('title', $def['label'].' section')
@section('inline-errors', '1')

@section('content')
@php $canEdit = auth()->user()->can('sections.update'); @endphp
<div class="ad-page-head">
    <div>
        <div class="ad-crumbs"><a href="{{ route('admin.sections.index') }}">Page sections</a> / {{ $def['label'] }}</div>
        <h1>{{ $def['label'] }} section</h1>
        <p>Text you type here appears on every design. Use <code>{app_name}</code> to insert your site name.</p>
    </div>
    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="offcanvas" data-bs-target="#adPreview"><i class="fa-solid fa-eye me-1"></i> Live preview</button>
</div>

<form method="POST" action="{{ route('admin.sections.update', $id) }}" data-once>
    @csrf @method('PUT')
    <fieldset @disabled(! $canEdit)>
    <div class="ad-card">
        @if ($hasToggle)
            <div class="ad-card-head">
                <div class="form-check form-switch m-0">
                    <input type="hidden" name="enabled" value="0">
                    <input class="form-check-input" type="checkbox" role="switch" id="enabled" name="enabled" value="1" @checked(old('enabled', $enabled))>
                    <label class="form-check-label fw-semibold" for="enabled">Show this section on the website</label>
                </div>
            </div>
        @endif
        <div class="ad-card-body">
            <div class="row g-3">
                @foreach ($def['fields'] as $field)
                    @include('admin.partials.field', ['field' => $field, 'value' => $values[$field['name']] ?? null])
                @endforeach
            </div>
        </div>
        @if ($canEdit)
            <div class="ad-card-foot">
                <a href="{{ route('admin.sections.index') }}" class="btn btn-outline-secondary">Back</a>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Save section</button>
            </div>
        @endif
    </div>
    </fieldset>
</form>

@if (! empty($def['content']) && auth()->user()->can($def['content'].'.view'))
    <div class="ad-card mt-4">
        <div class="ad-card-body d-flex align-items-center gap-3 flex-wrap">
            <span class="ad-ico-tile"><i class="fa-solid {{ config("content_types.{$def['content']}.icon") }}"></i></span>
            <div class="flex-grow-1"><div class="fw-semibold">{{ config("content_types.{$def['content']}.label") }}</div><div class="small text-secondary">The cards, photos or entries inside this section are managed separately.</div></div>
            <a href="{{ route('admin.content.index', $def['content']) }}" class="btn btn-soft">Manage {{ strtolower(config("content_types.{$def['content']}.label")) }}</a>
        </div>
    </div>
@endif
@endsection
