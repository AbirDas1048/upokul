@extends('layouts.admin')
@section('title', 'Site settings')
@section('inline-errors', '1')

@section('content')
<div class="ad-page-head">
    <div>
        <h1>Site settings</h1>
        <p>{{ $def['description'] }}</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-3">
        <div class="ad-card p-2">
            @foreach ($groups as $key => $g)
                <a href="{{ route('admin.settings.edit', $key) }}" class="ad-link {{ $group === $key ? 'active' : '' }}" style="color:var(--ad-text)">
                    <i class="fa-solid {{ $g['icon'] }}"></i> {{ $g['label'] }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="col-lg-9">
        <form method="POST" action="{{ route('admin.settings.update', $group) }}" enctype="multipart/form-data" data-once>
            @csrf @method('PUT')
            <fieldset @disabled(! $canEdit)>
            <div class="ad-card">
                <div class="ad-card-head"><h2><i class="fa-solid {{ $def['icon'] }} me-2 text-primary"></i> {{ $def['label'] }}</h2>
                    @unless ($canEdit) <span class="ad-badge mute"><i class="fa-solid fa-lock"></i> Read only</span> @endunless
                </div>
                <div class="ad-card-body">
                    <div class="row g-3">
                        @foreach ($def['fields'] as $field)
                            @include('admin.partials.field', ['field' => $field, 'value' => $values[$field['name']] ?? null, 'resettable' => $group === 'branding'])
                        @endforeach
                    </div>
                </div>
                @if ($canEdit)
                    <div class="ad-card-foot">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-1"></i> Save {{ strtolower($def['label']) }}</button>
                    </div>
                @endif
            </div>
            </fieldset>
        </form>

        @if ($group === 'mail' && $canEdit)
            <div class="ad-card mt-4">
                <div class="ad-card-body d-flex align-items-center gap-3 flex-wrap">
                    <span class="ad-ico-tile"><i class="fa-solid fa-vial"></i></span>
                    <div class="flex-grow-1"><div class="fw-semibold">Send a test email</div><div class="small text-secondary">Save first, then send a message to {{ auth()->user()->email }} to check the connection.</div></div>
                    <form method="POST" action="{{ route('admin.settings.mail-test') }}" data-once>@csrf
                        <button class="btn btn-soft"><i class="fa-solid fa-paper-plane me-1"></i> Send test</button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
