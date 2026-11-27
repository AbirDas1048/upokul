@php
    $nonce = \Illuminate\Support\Facades\Vite::cspNonce();
    $user = auth()->user();
    $unread = $user->can('contacts.view') ? \App\Models\ContactMail::whereNull('read_at')->count() : 0;
    $contentTypes = collect(config('content_types'))->filter(fn ($def, $type) => $user->can("{$type}.view"));
    $canSettings = $user->can('settings.view') || $user->can('mail.view');
    $isActive = fn (string ...$patterns) => request()->routeIs(...$patterns);
    $canPublish = $user->can('publish.update');
    $pending = $canPublish && app(\App\Support\Publisher::class)->isPending();
@endphp
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · {{ config('app.name') }} Admin</title>
    <link rel="icon" href="{{ $site->image('favicon') }}">
    <script nonce="{{ $nonce }}">
        try { document.documentElement.setAttribute('data-bs-theme', localStorage.getItem('admin-theme') || (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')); } catch (e) {}
    </script>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ filemtime(public_path('css/admin.css')) }}">
    {{-- Styling above is plain static files, so the panel keeps its design even without a Vite build. --}}
    @php try { echo app(\Illuminate\Foundation\Vite::class)(['resources/js/admin.js']); } catch (\Throwable) { /* behaviour only */ } @endphp
</head>
<body class="admin">
<div class="ad-shell">

    <aside class="ad-sidebar" id="adSidebar" aria-label="Admin navigation">
        <a href="{{ route('admin.dashboard') }}" class="ad-brand">
            <img src="{{ $site->image('logo') }}" alt="">
            <span><b>{{ config('app.name') }}</b><small>Admin Console</small></span>
        </a>

        <nav class="ad-nav">
            @can('dashboard.view')
                <a href="{{ route('admin.dashboard') }}" class="ad-link {{ $isActive('admin.dashboard') ? 'active' : '' }}"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
            @endcan

            @can('contacts.view')
                <div class="ad-nav-title">Inbox</div>
                <a href="{{ route('admin.contacts.index') }}" class="ad-link {{ $isActive('admin.contacts.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-envelope-open-text"></i> Messages
                    @if ($unread) <span class="badge rounded-pill">{{ $unread }}</span> @endif
                </a>
            @endcan

            @if ($user->can('sections.view') || $contentTypes->isNotEmpty())
                <div class="ad-nav-title">Website content</div>
                @can('sections.view')
                    <a href="{{ route('admin.sections.index') }}" class="ad-link {{ $isActive('admin.sections.*') ? 'active' : '' }}"><i class="fa-solid fa-table-columns"></i> Page sections</a>
                @endcan
                @foreach ($contentTypes as $type => $def)
                    <a href="{{ route('admin.content.index', $type) }}" class="ad-link {{ request()->route('type') === $type ? 'active' : '' }}"><i class="fa-solid {{ $def['icon'] }}"></i> {{ $def['label'] }}</a>
                @endforeach
            @endif

            @if ($user->can('appearance.view') || $canSettings)
                <div class="ad-nav-title">Configuration</div>
                @can('appearance.view')
                    <a href="{{ route('admin.appearance.index') }}" class="ad-link {{ $isActive('admin.appearance.*') ? 'active' : '' }}"><i class="fa-solid fa-palette"></i> Appearance &amp; preview</a>
                @endcan
                @if ($canSettings)
                    <a href="{{ route('admin.settings.edit') }}" class="ad-link {{ $isActive('admin.settings.*') ? 'active' : '' }}"><i class="fa-solid fa-sliders"></i> Site settings</a>
                @endif
            @endif

            @if ($user->can('users.view') || $user->can('roles.view') || $user->can('activity.view'))
                <div class="ad-nav-title">Administration</div>
                @can('users.view')
                    <a href="{{ route('admin.users.index') }}" class="ad-link {{ $isActive('admin.users.*') ? 'active' : '' }}"><i class="fa-solid fa-users"></i> Admin users</a>
                @endcan
                @can('roles.view')
                    <a href="{{ route('admin.roles.index') }}" class="ad-link {{ $isActive('admin.roles.*') ? 'active' : '' }}"><i class="fa-solid fa-user-shield"></i> Roles &amp; permissions</a>
                @endcan
                @can('activity.view')
                    <a href="{{ route('admin.activity.index') }}" class="ad-link {{ $isActive('admin.activity.*') ? 'active' : '' }}"><i class="fa-solid fa-clock-rotate-left"></i> Activity log</a>
                @endcan
            @endif
        </nav>

        <div class="ad-sidebar-foot">
            <i class="fa-solid fa-shield-halved me-1"></i>
            {{ $user->isMaster() ? 'Master administrator' : ($user->role?->name ?? 'No role assigned') }}
        </div>
    </aside>
    <div class="ad-scrim" id="adScrim"></div>

    <div class="ad-main">
        <header class="ad-topbar">
            <button type="button" class="ad-icon-btn d-lg-none" data-sidebar-toggle aria-label="Open menu"><i class="fa-solid fa-bars"></i></button>
            <div class="me-auto">
                <div class="fw-semibold d-none d-sm-block">@yield('title', 'Dashboard')</div>
            </div>

            @if ($canPublish)
                @if ($pending)
                    <form method="POST" action="{{ route('admin.publish') }}" data-once data-confirm="Everything you changed since the last publish goes live on the website right now. Tip: check it first with Live preview." data-confirm-title="Publish your changes?" data-confirm-button="Publish now" data-confirm-color="#0f6d6f" data-confirm-icon="question">@csrf
                        <button class="btn btn-warning btn-sm fw-semibold"><i class="fa-solid fa-rocket me-1"></i> Publish changes</button>
                    </form>
                @else
                    <span class="ad-badge ok d-none d-md-inline-flex" title="The website shows everything you have saved"><i class="fa-solid fa-circle-check"></i> Live site up to date</span>
                @endif
            @endif
            <button type="button" class="btn btn-soft btn-sm d-none d-sm-inline-flex align-items-center gap-2" data-bs-toggle="offcanvas" data-bs-target="#adPreview">
                <i class="fa-solid fa-eye"></i> Preview draft
            </button>
            <a href="{{ url('/') }}" target="_blank" rel="noopener" class="ad-icon-btn" title="Open website" data-bs-toggle="tooltip"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
            <button type="button" class="ad-icon-btn" data-theme-toggle aria-label="Toggle dark mode"><i class="fa-solid fa-moon"></i></button>

            <div class="dropdown">
                <button class="btn p-0 border-0 d-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Account menu">
                    <span class="ad-avatar">{{ $user->initials() }}</span>
                    <span class="d-none d-md-block text-start lh-sm">
                        <span class="d-block fw-semibold small">{{ $user->name }}</span>
                        <span class="d-block text-secondary" style="font-size:.72rem">{{ $user->isMaster() ? 'Master admin' : ($user->role?->name ?? '—') }}</span>
                    </span>
                    <i class="fa-solid fa-chevron-down small text-secondary d-none d-md-block"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2" style="border-radius:1rem;min-width:220px">
                    <li class="px-2 pb-2 small text-secondary text-truncate">{{ $user->email }}</li>
                    <li><a class="dropdown-item rounded-3" href="{{ route('admin.profile.edit') }}"><i class="fa-solid fa-user-gear me-2"></i> My profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('admin.logout') }}">@csrf
                            <button class="dropdown-item rounded-3 text-danger"><i class="fa-solid fa-right-from-bracket me-2"></i> Sign out</button>
                        </form>
                    </li>
                </ul>
            </div>
        </header>

        <main class="ad-content" id="main">
            @if (session('status'))
                <div class="alert alert-success d-flex align-items-center gap-2 alert-dismissible fade show" role="status" data-autohide>
                    <i class="fa-solid fa-circle-check"></i> <div>{{ session('status') }}@if ($pending && ! str_contains(session('status'), 'live')) <span class="opacity-75"> It is saved as a draft — visitors see it after you press <b>Publish changes</b>.</span>@endif</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger d-flex align-items-center gap-2 alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-triangle-exclamation"></i> <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if ($errors->any() && ! View::hasSection('inline-errors'))
                <div class="alert alert-danger" role="alert">
                    <div class="fw-semibold mb-1"><i class="fa-solid fa-triangle-exclamation me-1"></i> Please fix the following:</div>
                    <ul class="mb-0 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="ad-footer">
            © {{ date('Y') }} {{ config('app.name') }} · Admin Console · Powered by <span class="fw-semibold">{{ config('product.name') }}</span>
        </footer>
    </div>
</div>

{{-- Live preview of the public website at desktop / tablet / phone width --}}
<div class="offcanvas offcanvas-end ad-preview" tabindex="-1" id="adPreview" aria-labelledby="adPreviewTitle">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="adPreviewTitle"><i class="fa-solid fa-eye me-2 text-primary"></i> Live preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        @include('admin.partials.preview')
    </div>
</div>
</body>
</html>
