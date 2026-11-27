@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
@php $hour = (int) now()->format('G'); @endphp

<div class="ad-hero mb-4">
    <h2>{{ $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening') }}, {{ \Illuminate\Support\Str::before(auth()->user()->name, ' ') }} 👋</h2>
    <p>Here's what's happening with {{ config('app.name') }} today.</p>
    <div class="d-flex flex-wrap gap-2 mt-3 position-relative" style="z-index:1">
        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="offcanvas" data-bs-target="#adPreview"><i class="fa-solid fa-eye me-1"></i> Preview draft</button>
        @can('sections.view') <a href="{{ route('admin.sections.index') }}" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-pen-ruler me-1"></i> Edit page text</a> @endcan
        @can('contacts.view') <a href="{{ route('admin.contacts.index', ['filter' => 'unread']) }}" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-inbox me-1"></i> Unread messages</a> @endcan
    </div>
</div>

@can('publish.update')
    @if (app(\App\Support\Publisher::class)->isPending())
        <div class="ad-card mb-4" style="border-color:var(--ad-gold)">
            <div class="ad-card-body d-flex align-items-center gap-3 flex-wrap">
                <span class="ad-ico-tile" style="background:rgba(201,162,78,.18);color:var(--ad-gold)"><i class="fa-solid fa-rocket"></i></span>
                <div class="flex-grow-1"><div class="fw-semibold">You have unpublished changes</div><div class="small text-secondary">Visitors still see the previous version. Review your changes with the draft preview, then publish when you are happy.</div></div>
                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="offcanvas" data-bs-target="#adPreview"><i class="fa-solid fa-eye me-1"></i> Preview draft</button>
                <form method="POST" action="{{ route('admin.publish') }}" data-once data-confirm="Everything you changed since the last publish goes live on the website right now." data-confirm-title="Publish your changes?" data-confirm-button="Publish now" data-confirm-color="#0f6d6f" data-confirm-icon="question">@csrf
                    <button class="btn btn-primary"><i class="fa-solid fa-rocket me-1"></i> Publish changes</button>
                </form>
            </div>
        </div>
    @endif
@endcan

@if ($stats)
<div class="row g-3 mb-4">
    @isset($stats['messages_total'])
        <div class="col-6 col-xl-3"><div class="ad-card ad-stat"><span class="ad-stat-ico"><i class="fa-solid fa-envelope"></i></span><div><b>{{ number_format($stats['messages_total']) }}</b><span class="l">Total messages</span></div></div></div>
        <div class="col-6 col-xl-3"><div class="ad-card ad-stat"><span class="ad-stat-ico gold"><i class="fa-solid fa-envelope-circle-check"></i></span><div><b>{{ number_format($stats['messages_unread']) }}</b><span class="l">Unread</span></div></div></div>
        <div class="col-6 col-xl-3"><div class="ad-card ad-stat"><span class="ad-stat-ico ink"><i class="fa-solid fa-calendar-week"></i></span><div><b>{{ number_format($stats['messages_week']) }}</b><span class="l">Last 7 days</span></div></div></div>
        <div class="col-6 col-xl-3"><div class="ad-card ad-stat"><span class="ad-stat-ico rose"><i class="fa-solid fa-triangle-exclamation"></i></span><div><b>{{ number_format($stats['messages_failed']) }}</b><span class="l">Failed to e-mail</span></div></div></div>
    @endisset
</div>
@endif

<div class="row g-4">
    <div class="col-xl-8">
        @if ($chart)
        <div class="ad-card mb-4">
            <div class="ad-card-head"><h2>Messages · last 14 days</h2><span class="ad-badge">{{ array_sum(array_column($chart, 'count')) }} received</span></div>
            <div class="ad-card-body">
                <div class="ad-chart" role="img" aria-label="Contact messages per day for the last 14 days">
                    @foreach ($chart as $bar)
                        <div class="ad-bar" title="{{ $bar['label'] }}: {{ $bar['count'] }}">
                            <b>{{ $bar['count'] ?: '' }}</b>
                            <i data-n="{{ $bar['count'] }}" style="height: {{ max(2, round($bar['count'] / $chartMax * 100)) }}%; animation-delay: {{ $loop->index * 40 }}ms"></i>
                            <em>{{ \Illuminate\Support\Str::before($bar['label'], ' ') }}</em>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="ad-card">
            <div class="ad-card-head"><h2>Latest messages</h2><a href="{{ route('admin.contacts.index') }}" class="btn btn-soft btn-sm">View all</a></div>
            @if ($recent->isEmpty())
                <div class="ad-empty"><i class="fa-regular fa-envelope-open"></i> No messages yet. They'll show up here when visitors use the contact form.</div>
            @else
                <div class="table-responsive">
                    <table class="table ad-table">
                        <tbody>
                        @foreach ($recent as $m)
                            <tr class="{{ $m->read_at ? '' : 'unread' }}">
                                <td><a class="text-decoration-none fw-title text-reset" href="{{ route('admin.contacts.show', $m) }}">{{ $m->name }}</a><div class="small text-secondary">{{ $m->email }}</div></td>
                                <td class="d-none d-md-table-cell"><div class="ad-trunc" style="max-width:18rem">{{ $m->subject }}</div></td>
                                <td class="text-end text-secondary small text-nowrap">{{ $m->created_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @endif
    </div>

    <div class="col-xl-4">
        <div class="ad-card mb-4">
            <div class="ad-card-head"><h2>Website content</h2></div>
            <div class="list-group list-group-flush">
                @forelse ($content as $type => $c)
                    <a href="{{ route('admin.content.index', $type) }}" class="list-group-item list-group-item-action d-flex align-items-center gap-3 bg-transparent border-color">
                        <span class="ad-ico-tile"><i class="fa-solid {{ $c['icon'] }}"></i></span>
                        <span class="flex-grow-1 fw-medium">{{ $c['label'] }}</span>
                        <span class="ad-badge">{{ $c['count'] }}</span>
                    </a>
                @empty
                    <div class="ad-empty py-4">You don't have access to any content modules.</div>
                @endforelse
            </div>
        </div>

        <div class="ad-card">
            <div class="ad-card-head"><h2>System status</h2></div>
            <div class="ad-card-body">
                @foreach ($health as $label => $ok)
                    <div class="d-flex align-items-center justify-content-between py-2 {{ ! $loop->last ? 'border-bottom' : '' }}">
                        <span>{{ $label }}</span>
                        <span class="ad-badge {{ $ok ? 'ok' : 'warn' }}"><i class="fa-solid {{ $ok ? 'fa-circle-check' : 'fa-circle-exclamation' }}"></i> {{ $ok ? 'OK' : 'Check' }}</span>
                    </div>
                @endforeach
                @isset($stats['admins'])
                    <div class="d-flex align-items-center justify-content-between pt-3 small text-secondary">
                        <span>{{ $stats['admins'] }} admin user(s)</span><span>{{ $stats['roles'] }} role(s)</span>
                    </div>
                @endisset
            </div>
        </div>
    </div>
</div>
@endsection
