@extends('layouts.admin')
@section('title', 'Messages')

@section('content')
<div class="ad-page-head">
    <div>
        <h1>Contact messages</h1>
        <p>Enquiries sent through the website contact form.</p>
    </div>
</div>

<div class="ad-card">
    <div class="ad-card-head flex-wrap">
        <ul class="nav nav-pills gap-1">
            @foreach (['all' => 'All', 'unread' => 'Unread ('.$unread.')', 'failed' => 'Mail failed'] as $key => $label)
                <li class="nav-item"><a class="nav-link py-1 px-3 {{ $filter === $key ? 'active' : '' }}" style="{{ $filter === $key ? 'background:var(--ad-brand)' : '' }}" href="{{ route('admin.contacts.index', ['filter' => $key, 'q' => $search ?: null]) }}">{{ $label }}</a></li>
            @endforeach
        </ul>
        <form method="GET" class="d-flex gap-2" role="search">
            <input type="hidden" name="filter" value="{{ $filter }}">
            <input type="search" name="q" value="{{ $search }}" class="form-control form-control-sm" placeholder="Search name, e-mail, message…" style="min-width:16rem">
            <button class="btn btn-soft btn-sm" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
    </div>

    @if ($messages->isEmpty())
        <div class="ad-empty"><i class="fa-regular fa-envelope-open"></i> {{ $search || $filter !== 'all' ? 'No messages match this view.' : 'No messages yet.' }}</div>
    @else
        <div class="table-responsive">
            <table class="table ad-table">
                <thead><tr><th>From</th><th>Subject</th><th>Mail</th><th>Received</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                @foreach ($messages as $m)
                    <tr class="{{ $m->read_at ? '' : 'unread' }}">
                        <td>
                            <a class="text-decoration-none text-reset fw-title" href="{{ route('admin.contacts.show', $m) }}">{{ $m->name }}</a>
                            <div class="small text-secondary">{{ $m->email }}</div>
                        </td>
                        <td><div class="ad-trunc" style="max-width:22rem">{{ $m->subject }}</div></td>
                        <td>
                            @switch((int) $m->status)
                                @case(\App\Services\ContactService::MAIL_SEND_STATUS_SUCCESS) <span class="ad-badge ok">Sent</span> @break
                                @case(\App\Services\ContactService::MAIL_SEND_STATUS_FAILED) <span class="ad-badge bad">Failed</span> @break
                                @case(\App\Services\ContactService::MAIL_SEND_STATUS_PROCESSING) <span class="ad-badge warn">Sending</span> @break
                                @default <span class="ad-badge mute">Queued</span>
                            @endswitch
                        </td>
                        <td class="text-secondary small text-nowrap" title="{{ $m->created_at }}">{{ $m->created_at->diffForHumans() }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.contacts.show', $m) }}" class="btn btn-soft btn-sm" title="Open"><i class="fa-solid fa-eye"></i></a>
                            @can('contacts.delete')
                                <form method="POST" action="{{ route('admin.contacts.destroy', $m) }}" class="d-inline" data-confirm="Delete the message from {{ $m->name }}? This cannot be undone." data-confirm-button="Delete">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @if ($messages->hasPages())
            <div class="ad-card-foot justify-content-between align-items-center">
                <span class="small text-secondary">Showing {{ $messages->firstItem() }}–{{ $messages->lastItem() }} of {{ $messages->total() }}</span>
                {{ $messages->links('pagination::bootstrap-5') }}
            </div>
        @endif
    @endif
</div>
@endsection
