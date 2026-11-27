@extends('layouts.admin')
@section('title', 'Message from '.$message->name)

@section('content')
<div class="ad-page-head">
    <div>
        <div class="ad-crumbs"><a href="{{ route('admin.contacts.index') }}">Messages</a> / #{{ $message->id }}</div>
        <h1>{{ $message->subject }}</h1>
        <p>From {{ $message->name }} · {{ $message->created_at->format('d M Y, H:i') }} ({{ $message->created_at->diffForHumans() }})</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: '.$message->subject) }}" class="btn btn-primary"><i class="fa-solid fa-reply me-1"></i> Reply by e-mail</a>
        @can('contacts.update')
            <form method="POST" action="{{ route('admin.contacts.toggle-read', $message) }}">@csrf
                <button class="btn btn-outline-secondary"><i class="fa-regular {{ $message->read_at ? 'fa-envelope' : 'fa-envelope-open' }} me-1"></i> Mark as {{ $message->read_at ? 'unread' : 'read' }}</button>
            </form>
        @endcan
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="ad-card">
            <div class="ad-card-body">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <span class="ad-avatar" style="width:3rem;height:3rem">{{ mb_strtoupper(mb_substr($message->name, 0, 1)) }}</span>
                    <div><div class="fw-semibold">{{ $message->name }}</div><div class="small text-secondary">{{ $message->email }} · {{ $message->phone }}</div></div>
                </div>
                <div style="white-space:pre-wrap;line-height:1.7">{{ $message->mail_message }}</div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="ad-card mb-4">
            <div class="ad-card-head"><h2>Notification e-mail</h2>
                @switch((int) $message->status)
                    @case(\App\Services\ContactService::MAIL_SEND_STATUS_SUCCESS) <span class="ad-badge ok">Sent</span> @break
                    @case(\App\Services\ContactService::MAIL_SEND_STATUS_FAILED) <span class="ad-badge bad">Failed</span> @break
                    @case(\App\Services\ContactService::MAIL_SEND_STATUS_PROCESSING) <span class="ad-badge warn">Sending</span> @break
                    @default <span class="ad-badge mute">Queued</span>
                @endswitch
            </div>
            <div class="ad-card-body small">
                @if ($message->sent_at) <div class="mb-2"><span class="text-secondary">Sent:</span> {{ $message->sent_at->format('d M Y, H:i') }}</div> @endif
                @if ($message->error_message) <div class="alert alert-danger small py-2 mb-3">{{ $message->error_message }}</div> @endif
                @can('contacts.update')
                    <form method="POST" action="{{ route('admin.contacts.resend', $message) }}" data-confirm="Send the notification e-mail for this message again?" data-confirm-button="Send again" data-confirm-title="Re-send e-mail">
                        @csrf
                        <button class="btn btn-soft btn-sm"><i class="fa-solid fa-paper-plane me-1"></i> Send again</button>
                    </form>
                @endcan
            </div>
        </div>

        @can('contacts.delete')
        <div class="ad-card">
            <div class="ad-card-body">
                <form method="POST" action="{{ route('admin.contacts.destroy', $message) }}" data-confirm="Delete this message permanently?" data-confirm-button="Delete">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger w-100"><i class="fa-solid fa-trash me-1"></i> Delete message</button>
                </form>
            </div>
        </div>
        @endcan
    </div>
</div>
@endsection
