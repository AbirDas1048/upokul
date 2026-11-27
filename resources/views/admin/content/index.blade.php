@extends('layouts.admin')
@section('title', $def['label'])

@section('content')
@php
    $imageField = $def['image_field'] ?? null;
    $iconField = $def['icon_field'] ?? null;
    $summaryField = $def['summary'] ?? null;
@endphp
<div class="ad-page-head">
    <div>
        <div class="ad-crumbs">Website content / {{ $def['label'] }}</div>
        <h1>{{ $def['label'] }}</h1>
        <p>Shown in the "{{ config("site_settings.sections.{$def['section']}.label") }}" section of the website.
            @can('sections.view') <a href="{{ route('admin.sections.edit', $def['section']) }}">Edit that section's heading</a>. @endcan</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="offcanvas" data-bs-target="#adPreview"><i class="fa-solid fa-eye me-1"></i> Preview</button>
        @can("{$type}.create")
            <a href="{{ route('admin.content.create', $type) }}" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i> Add {{ strtolower($def['singular']) }}</a>
        @endcan
    </div>
</div>

<div class="ad-card">
    @if ($items->isEmpty())
        <div class="ad-empty"><i class="fa-solid {{ $def['icon'] }}"></i> Nothing here yet.
            @can("{$type}.create") <div class="mt-3"><a href="{{ route('admin.content.create', $type) }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i> Add the first {{ strtolower($def['singular']) }}</a></div> @endcan
        </div>
    @else
        <div class="table-responsive">
            <table class="table ad-table">
                <thead><tr><th style="width:4.5rem">Order</th><th>{{ $def['title_label'] }}</th><th class="d-none d-md-table-cell">Details</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                @foreach ($items as $item)
                    @php
                        $data = $item->data ?? [];
                        $summary = $summaryField ? (string) ($data[$summaryField] ?? '') : '';
                    @endphp
                    <tr>
                        <td class="text-secondary fw-semibold">{{ $item->sort_order }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                @if ($imageField && ! empty($data[$imageField]))
                                    <img class="ad-thumb" src="{{ $site->url($data[$imageField]) }}" alt="" loading="lazy">
                                @elseif ($iconField && ! empty($data[$iconField]))
                                    <span class="ad-ico-tile"><i class="fa-solid {{ preg_match('/^fa-[a-z0-9-]+$/', $data[$iconField]) ? $data[$iconField] : 'fa-question' }}"></i></span>
                                @endif
                                <span class="fw-semibold">{{ $item->title }}</span>
                            </div>
                        </td>
                        <td class="d-none d-md-table-cell"><div class="ad-trunc">{{ \Illuminate\Support\Str::limit($summary, 90) }}</div></td>
                        <td><span class="ad-badge {{ $item->is_active ? 'ok' : 'mute' }}">{{ $item->is_active ? 'Visible' : 'Hidden' }}</span></td>
                        <td class="text-end text-nowrap">
                            @can("{$type}.update")
                                <a href="{{ route('admin.content.edit', [$type, $item->id]) }}" class="btn btn-soft btn-sm" title="Edit"><i class="fa-solid fa-pen"></i></a>
                            @endcan
                            @can("{$type}.delete")
                                <form method="POST" action="{{ route('admin.content.destroy', [$type, $item->id]) }}" class="d-inline" data-confirm="Delete &quot;{{ $item->title }}&quot;? It disappears from the website immediately." data-confirm-button="Delete">
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
    @endif
</div>
@endsection
