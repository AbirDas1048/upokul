@extends('layouts.admin')
@section('title', ($item->exists ? 'Edit ' : 'Add ').strtolower($def['singular']))
@section('inline-errors', '1')

@section('content')
<div class="ad-page-head">
    <div>
        <div class="ad-crumbs"><a href="{{ route('admin.content.index', $type) }}">{{ $def['label'] }}</a> / {{ $item->exists ? 'Edit' : 'Add' }}</div>
        <h1>{{ $item->exists ? 'Edit '.strtolower($def['singular']) : 'Add '.strtolower($def['singular']) }}</h1>
    </div>
</div>

<form method="POST" enctype="multipart/form-data" data-once
      action="{{ $item->exists ? route('admin.content.update', [$type, $item->id]) : route('admin.content.store', $type) }}">
    @csrf
    @if ($item->exists) @method('PUT') @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="ad-card">
                <div class="ad-card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="title">{{ $def['title_label'] }} <span class="text-danger">*</span></label>
                            <input class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $item->title) }}" maxlength="255" required autofocus>
                            @error('title') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        @foreach ($def['fields'] as $field)
                            @include('admin.partials.field', ['field' => $field, 'value' => $item->data[$field['name']] ?? null])
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="ad-card">
                <div class="ad-card-head"><h2>Publishing</h2></div>
                <div class="ad-card-body">
                    <div class="form-check form-switch mb-3">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" @checked(old('is_active', $item->is_active))>
                        <label class="form-check-label fw-semibold" for="is_active">Visible on the website</label>
                    </div>
                    <label class="form-label" for="sort_order">Display order</label>
                    <input type="number" min="0" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" value="{{ old('sort_order', $item->sort_order) }}">
                    @error('sort_order') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    <div class="form-text">Lower numbers appear first.</div>
                </div>
                <div class="ad-card-foot">
                    <a href="{{ route('admin.content.index', $type) }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> {{ $item->exists ? 'Save changes' : 'Add '.strtolower($def['singular']) }}</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
