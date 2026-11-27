<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendContactMailJob;
use App\Models\ContactMail;
use App\Services\ContactService;
use App\Support\Activity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));
        $filter = (string) $request->query('filter', 'all');

        $messages = ContactMail::query()
            ->when($search !== '', fn ($q) => $q->where(function ($q) use ($search) {
                $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $search).'%';
                $q->where('name', 'like', $like)->orWhere('email', 'like', $like)
                    ->orWhere('subject', 'like', $like)->orWhere('mail_message', 'like', $like);
            }))
            ->when($filter === 'unread', fn ($q) => $q->whereNull('read_at'))
            ->when($filter === 'failed', fn ($q) => $q->where('status', ContactService::MAIL_SEND_STATUS_FAILED))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.contacts.index', [
            'messages' => $messages,
            'search' => $search,
            'filter' => $filter,
            'unread' => ContactMail::whereNull('read_at')->count(),
        ]);
    }

    public function show(Request $request, ContactMail $contact): View
    {
        // Opening a message marks it read (unless the viewer cannot change it).
        if ($contact->read_at === null && $request->user()->can('contacts.update')) {
            $contact->forceFill(['read_at' => now()])->save();
        }

        return view('admin.contacts.show', ['message' => $contact]);
    }

    public function toggleRead(ContactMail $contact): RedirectResponse
    {
        $contact->forceFill(['read_at' => $contact->read_at ? null : now()])->save();

        return back()->with('status', $contact->read_at ? 'Marked as read.' : 'Marked as unread.');
    }

    /** Queue the notification mail for this message again (e.g. after fixing the SMTP settings). */
    public function resend(ContactMail $contact): RedirectResponse
    {
        if ((int) $contact->status === ContactService::MAIL_SEND_STATUS_PROCESSING) {
            return back()->with('error', 'This message is being sent right now.');
        }

        $contact->forceFill(['status' => ContactService::MAIL_SEND_STATUS_PENDING, 'error_message' => null])->save();
        SendContactMailJob::dispatch($contact);

        Activity::log('contacts.resend', "Queued contact message #{$contact->id} again");

        return back()->with('status', 'The message was queued for sending again.');
    }

    public function destroy(ContactMail $contact): RedirectResponse
    {
        $id = $contact->id;
        $contact->delete();
        Activity::log('contacts.delete', "Deleted contact message #{$id}");

        return redirect()->route('admin.contacts.index')->with('status', 'Message deleted.');
    }
}
