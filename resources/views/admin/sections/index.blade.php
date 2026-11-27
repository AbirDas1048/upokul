@extends('layouts.admin')
@section('title', 'Page sections')

@section('content')
<div class="ad-page-head">
    <div>
        <h1>Page sections</h1>
        <p>Edit the headings and wording of each section of your landing pages, or switch a section off.</p>
    </div>
    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="offcanvas" data-bs-target="#adPreview"><i class="fa-solid fa-eye me-1"></i> Live preview</button>
</div>

<div class="row g-3">
    @foreach ($sections as $id => $def)
        @php $on = $enabled($id); $toggle = ($def['toggle'] ?? true) !== false; @endphp
        <div class="col-md-6 col-xl-4">
            <div class="ad-card h-100">
                <div class="ad-card-body d-flex flex-column h-100">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <span class="ad-ico-tile"><i class="fa-solid {{ $def['icon'] }}"></i></span>
                        <div class="flex-grow-1">
                            <h2 class="h6 mb-1">{{ $def['label'] }}</h2>
                            @if ($toggle)
                                <span class="ad-badge {{ $on ? 'ok' : 'mute' }}">{{ $on ? 'Visible' : 'Hidden' }}</span>
                            @else
                                <span class="ad-badge">Always shown</span>
                            @endif
                        </div>
                    </div>
                    <div class="mt-auto d-flex flex-wrap gap-2">
                        @can('sections.view')
                            <a href="{{ route('admin.sections.edit', $id) }}" class="btn btn-soft btn-sm"><i class="fa-solid fa-pen me-1"></i> {{ auth()->user()->can('sections.update') ? 'Edit text' : 'View' }}</a>
                        @endcan
                        @if (! empty($def['content']) && auth()->user()->can($def['content'].'.view'))
                            <a href="{{ route('admin.content.index', $def['content']) }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid {{ config("content_types.{$def['content']}.icon") }} me-1"></i> {{ config("content_types.{$def['content']}.label") }}</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
