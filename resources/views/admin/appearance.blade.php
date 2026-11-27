@extends('layouts.admin')
@section('title', 'Appearance & preview')

@section('content')
@php $canEdit = auth()->user()->can('appearance.update'); @endphp
<div class="ad-page-head">
    <div>
        <h1>Appearance &amp; preview</h1>
        <p>Choose the design visitors see on your home page and preview every design before you decide.</p>
    </div>
</div>

<form method="POST" action="{{ route('admin.appearance.update') }}" class="mb-4" data-once>
    @csrf @method('PUT')
    <fieldset @disabled(! $canEdit)>
    <div class="ad-card">
        <div class="ad-card-head"><h2>Home page design</h2>
            @if ($canEdit) <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-floppy-disk me-1"></i> Save</button>
            @else <span class="ad-badge mute"><i class="fa-solid fa-lock"></i> Read only</span> @endif
        </div>
        <div class="ad-card-body">
            @php
                $swatches = ['chooser' => 'linear-gradient(135deg,#d9f6f6 0 33%,#04181a 33% 66%,#0f6d6f 66%)', 'aurora' => 'linear-gradient(135deg,#d9f6f6,#23cdd2)', 'noir' => 'linear-gradient(135deg,#04181a,#0f6d6f)', 'glass' => 'linear-gradient(135deg,#0f6d6f,#c9a24e)'];
            @endphp
            <div class="row g-3">
                @foreach ($field['options'] as $key => $label)
                    <div class="col-sm-6 col-xl-3">
                        <input type="radio" class="btn-check" name="home_theme" id="t_{{ $key }}" value="{{ $key }}" @checked(old('home_theme', $current) === $key)>
                        <label class="theme-card" for="t_{{ $key }}">
                            <div class="theme-swatch" style="background:{{ $swatches[$key] }}"></div>
                            <div class="fw-semibold small">{{ $label }}</div>
                        </label>
                    </div>
                @endforeach
            </div>
            @error('home_theme') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
        </div>
    </div>
    </fieldset>
</form>

<div class="ad-card">
    <div class="ad-card-head"><h2><i class="fa-solid fa-eye me-2 text-primary"></i> Preview</h2><span class="small text-secondary">Shows your saved draft. Nothing reaches visitors until you press Publish.</span></div>
    <div class="ad-card-body">
        @include('admin.partials.preview')
    </div>
</div>
@endsection
