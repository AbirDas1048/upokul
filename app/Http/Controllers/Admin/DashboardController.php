<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMail;
use App\Models\ContentItem;
use App\Models\Role;
use App\Models\User;
use App\Services\ContactService;
use App\Support\Installer;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [];
        $chart = [];
        $recent = collect();

        if (Gate::allows('contacts.view')) {
            $stats['messages_total'] = ContactMail::count();
            $stats['messages_unread'] = ContactMail::whereNull('read_at')->count();
            $stats['messages_week'] = ContactMail::where('created_at', '>=', now()->subDays(7))->count();
            $stats['messages_failed'] = ContactMail::where('status', ContactService::MAIL_SEND_STATUS_FAILED)->count();
            $recent = ContactMail::latest('id')->limit(6)->get();
            $chart = $this->messagesPerDay(14);
        }

        $content = collect(config('content_types'))
            ->filter(fn ($def, $type) => Gate::allows("{$type}.view"))
            ->map(fn ($def, $type) => ['label' => $def['label'], 'icon' => $def['icon'], 'count' => ContentItem::ofType($type)->count()])
            ->all();

        if (Gate::allows('users.view')) {
            $stats['admins'] = User::count();
            $stats['roles'] = Role::count();
        }

        return view('admin.dashboard', [
            'stats' => $stats,
            'chart' => $chart,
            'chartMax' => max(1, ...array_column($chart ?: [['count' => 0]], 'count')),
            'recent' => $recent,
            'content' => $content,
            'health' => [
                'Installation' => app(Installer::class)->hasRun(),
                'Mail transport' => config('mail.default') !== 'log' && config('mail.default') !== 'array',
                'Queue worker mode' => config('queue.default') !== 'sync',
            ],
        ]);
    }

    /** @return list<array{label: string, day: string, count: int}> */
    private function messagesPerDay(int $days): array
    {
        $from = Carbon::today()->subDays($days - 1);

        $counts = ContactMail::query()
            ->where('created_at', '>=', $from)
            ->get(['created_at'])
            ->countBy(fn ($row) => $row->created_at->toDateString());

        return collect(range(0, $days - 1))->map(function (int $i) use ($from, $counts) {
            $day = $from->copy()->addDays($i);

            return ['label' => $day->format('d M'), 'day' => $day->format('D'), 'count' => (int) ($counts[$day->toDateString()] ?? 0)];
        })->all();
    }
}
