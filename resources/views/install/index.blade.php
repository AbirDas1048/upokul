@extends('layouts.auth')
@section('title', 'Install')
@section('product', '1')

@php
    $v = fn (string $key, $default = '') => old($key, $defaults[$key] ?? $default);
@endphp

@section('content')
<div class="ad-auth-card wide">
    {{-- The PRODUCT's brand. The customer's own brand is entered below and replaces nothing here. --}}
    <div class="ad-product mb-4">
        <img class="ad-auth-logo" src="{{ asset(config('product.logo')) }}" alt="{{ config('product.name') }} logo" width="64" height="64">
        <div>
            <div class="ad-product-name">{{ config('product.name') }}</div>
            <div class="small text-secondary">{{ config('product.tagline') }}</div>
        </div>
        <span class="ad-badge ms-auto d-none d-sm-inline-flex"><i class="fa-solid fa-wand-magic-sparkles"></i> Setup</span>
    </div>

    <h1 class="h4 mb-1">Welcome — let's set up your website</h1>
    <p class="text-secondary small mb-4">One-time installation. Takes about a minute, and everything can be changed later in the admin panel.</p>

    @error('install') <div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $message }}</div> @enderror

    <div class="ad-card mb-4">
        <div class="ad-card-head">
            <h2><i class="fa-solid fa-server me-2 text-primary"></i> 1 · Server check</h2>
            <span class="ad-badge {{ $ready ? 'ok' : 'bad' }}">{{ $ready ? 'All good' : 'Action needed' }}</span>
        </div>
        <div class="ad-card-body py-2">
            <div class="row">
                @foreach (collect($requirements)->chunk((int) ceil(count($requirements) / 2)) as $chunk)
                    <div class="col-md-6">
                        @foreach ($chunk as $row)
                            <div class="ad-req">
                                <i class="fa-solid {{ $row['ok'] ? 'fa-circle-check ok' : 'fa-circle-xmark no' }}"></i>
                                <span class="flex-grow-1">{{ $row['label'] }}</span>
                                <small class="text-secondary">{{ $row['detail'] }}</small>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('install.store') }}" enctype="multipart/form-data" data-once>
        @csrf
        <fieldset @disabled(! $ready)>

            <div class="ad-card mb-4">
                <div class="ad-card-head"><h2><i class="fa-solid fa-signature me-2 text-primary"></i> 2 · Project &amp; owner</h2></div>
                <div class="ad-card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="logo">Your logo <span class="text-secondary fw-normal">(optional)</span></label>
                            <div class="ad-img-field">
                                <img id="logo_img" src="{{ asset('images/default-logo.svg') }}" alt="" width="76" height="76">
                                <div class="flex-grow-1">
                                    <input class="form-control form-control-sm @error('logo') is-invalid @enderror" type="file" id="logo" name="logo" accept="image/png,image/jpeg,image/webp" data-preview="#logo_img">
                                    @error('logo') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    <div class="form-text">Square PNG, JPG or WebP, at least 420 × 420 px, up to 2 MB. Shown on your website and admin panel. You can add or change it later under Site settings → Logo &amp; images.</div>
                                </div>
                            </div>
                        </div>
                        @include('admin.partials.field', ['field' => ['name' => 'app_name', 'label' => 'Your business / site name', 'type' => 'text', 'rules' => 'required', 'placeholder' => 'e.g. Sunrise Travels & Tours', 'help' => 'Saved as APP_NAME in .env and used for your website, e-mails and admin panel.'], 'value' => $v('app_name')])
                        @include('admin.partials.field', ['field' => ['name' => 'tagline', 'label' => 'Tagline', 'type' => 'text', 'rules' => 'nullable', 'col' => 12], 'value' => $v('tagline')])
                        @include('admin.partials.field', ['field' => ['name' => 'owner_name', 'label' => 'Owner name', 'type' => 'text', 'rules' => 'required', 'col' => 6], 'value' => $v('owner_name')])
                        @include('admin.partials.field', ['field' => ['name' => 'owner_designation', 'label' => 'Owner designation', 'type' => 'text', 'rules' => 'nullable', 'col' => 6], 'value' => $v('owner_designation', 'Owner')])
                        @include('admin.partials.field', ['field' => ['name' => 'owner_email', 'label' => 'Owner email', 'type' => 'email', 'rules' => 'required', 'col' => 6], 'value' => $v('owner_email')])
                        @include('admin.partials.field', ['field' => ['name' => 'owner_phone', 'label' => 'Owner phone', 'type' => 'text', 'rules' => 'nullable', 'col' => 6], 'value' => $v('owner_phone')])
                    </div>
                </div>
            </div>

            <div class="ad-card mb-4">
                <div class="ad-card-head"><h2><i class="fa-solid fa-address-book me-2 text-primary"></i> 3 · Business contact info</h2></div>
                <div class="ad-card-body">
                    <div class="row g-3">
                        @include('admin.partials.field', ['field' => ['name' => 'office_email', 'label' => 'Office email', 'type' => 'email', 'rules' => 'required', 'col' => 6, 'placeholder' => 'hello@yourbusiness.com', 'help' => 'Contact-form messages are delivered here.'], 'value' => $v('office_email')])
                        @include('admin.partials.field', ['field' => ['name' => 'office_phone', 'label' => 'Office phone', 'type' => 'text', 'rules' => 'nullable', 'col' => 6], 'value' => $v('office_phone')])
                        @include('admin.partials.field', ['field' => ['name' => 'whatsapp_number', 'label' => 'WhatsApp number', 'type' => 'text', 'rules' => 'nullable', 'col' => 6], 'value' => $v('whatsapp_number')])
                        @include('admin.partials.field', ['field' => ['name' => 'office_address', 'label' => 'Office address', 'type' => 'text', 'rules' => 'nullable', 'col' => 6], 'value' => $v('office_address')])
                    </div>
                </div>
            </div>

            <div class="ad-card mb-4">
                <div class="ad-card-head"><h2><i class="fa-solid fa-palette me-2 text-primary"></i> 4 · Home page design</h2></div>
                <div class="ad-card-body">
                    <div class="row g-3 mb-3">
                        @foreach (['aurora' => ['Aurora', 'Bright &amp; airy', 'linear-gradient(135deg,#d9f6f6,#23cdd2)'], 'noir' => ['Noir', 'Dark &amp; elegant', 'linear-gradient(135deg,#04181a,#0f6d6f)'], 'glass' => ['Glass', 'Modern translucent', 'linear-gradient(135deg,#0f6d6f,#c9a24e)']] as $key => [$label, $hint, $bg])
                            <div class="col-md-4">
                                <input type="radio" class="btn-check" name="home_theme" id="theme_{{ $key }}" value="{{ $key }}" @checked(old('home_theme', 'aurora') === $key)>
                                <label class="theme-card" for="theme_{{ $key }}">
                                    <div class="theme-swatch" style="background:{{ $bg }}"></div>
                                    <div class="fw-semibold">{{ $label }}</div>
                                    <div class="small text-secondary">{!! $hint !!}</div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <p class="small text-secondary mb-0"><i class="fa-solid fa-circle-info me-1"></i> Your website goes live with the standard landing page (services, journeys, FAQs, reviews, gallery) using the details above. Changes you make later in the admin panel stay as drafts until you press <b>Publish</b>.</p>
                </div>
            </div>

            <div class="ad-card mb-4">
                <div class="ad-card-head">
                    <h2><i class="fa-solid fa-crown me-2" style="color:var(--ad-gold)"></i> 5 · Master administrator</h2>
                    <span class="ad-badge warn">Full access</span>
                </div>
                <div class="ad-card-body">
                    <p class="small text-secondary">The master admin can do everything, needs no role and cannot be removed or limited by other admins. Keep these details safe.</p>
                    <div class="row g-3">
                        @include('admin.partials.field', ['field' => ['name' => 'admin_name', 'label' => 'Your name', 'type' => 'text', 'rules' => 'required', 'col' => 6], 'value' => $v('admin_name')])
                        @include('admin.partials.field', ['field' => ['name' => 'admin_email', 'label' => 'Login email', 'type' => 'email', 'rules' => 'required', 'col' => 6], 'value' => $v('admin_email')])
                        <div class="col-md-6">
                            <label class="form-label" for="admin_password">Password <span class="text-danger">*</span></label>
                            <input class="form-control @error('admin_password') is-invalid @enderror" type="password" id="admin_password" name="admin_password" autocomplete="new-password" required>
                            @error('admin_password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            <div class="form-text">At least 10 characters with upper &amp; lower case letters and a number.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="admin_password_confirmation">Confirm password <span class="text-danger">*</span></label>
                            <input class="form-control" type="password" id="admin_password_confirmation" name="admin_password_confirmation" autocomplete="new-password" required>
                        </div>
                    </div>
                </div>
            </div>

            @if ($errors->any() && ! $errors->has('install'))
                <div class="alert alert-danger"><ul class="mb-0 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary btn-lg px-4"><i class="fa-solid fa-rocket me-2"></i> Install now</button>
            </div>
        </fieldset>
    </form>
</div>

<script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
    document.getElementById('logo')?.addEventListener('change', function () {
        var file = this.files && this.files[0];
        if (file) document.getElementById('logo_img').src = URL.createObjectURL(file);
    });
</script>
@endsection
