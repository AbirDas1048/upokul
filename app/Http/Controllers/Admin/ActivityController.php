<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Support\Activity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        return view('admin.activity', [
            'logs' => ActivityLog::query()
                ->when($search !== '', function ($q) use ($search) {
                    $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $search).'%';
                    $q->where(fn ($q) => $q->where('description', 'like', $like)->orWhere('user_name', 'like', $like)->orWhere('action', 'like', $like));
                })
                ->latest('id')->paginate(25)->withQueryString(),
            'search' => $search,
        ]);
    }

    public function clear(): RedirectResponse
    {
        ActivityLog::query()->delete();
        Activity::log('activity.clear', 'Cleared the activity log');

        return back()->with('status', 'Activity log cleared.');
    }
}
