@extends('layouts.app')
@section('title', $magazine->title . ' - GameVault')

@section('content')
<div class="detail-header">
    <div class="detail-cover">
        @if($magazine->cover_image_path)
            <img src="{{ asset('storage/' . $magazine->cover_image_path) }}" alt="{{ $magazine->title }}">
        @else
            <div class="game-card-cover-placeholder" style="height:400px;border-radius:12px;">📖</div>
        @endif
    </div>
    <div class="detail-info">
        <h1 class="detail-title">{{ $magazine->title }}</h1>

        <span class="badge badge-platform">{{ $magazine->type === 'manual' ? 'Manual' : 'Magazine' }}</span>
        @if($magazine->issue_number)
            <span class="badge badge-platform">#{{ $magazine->issue_number }}</span>
        @endif
        <span class="badge badge-format">{{ $magazine->year }}</span>

        <dl class="detail-meta">
            @if($magazine->publisher)<dt>Uitgever</dt><dd>{{ $magazine->publisher }}</dd>@endif
            @if($magazine->publication_date)<dt>Publicatiedatum</dt><dd>{{ $magazine->publication_date->format('d-m-Y') }}</dd>@endif
        </dl>

        @if($magazine->notes)
            <div style="margin-top:1rem;padding:1rem;background:var(--bg-input);border-radius:8px;">
                <strong>Notities:</strong><br>{{ $magazine->notes }}
            </div>
        @endif

        <div class="detail-actions">
            <a href="{{ asset('storage/' . $magazine->pdf_path) }}" target="_blank" class="btn btn-primary">PDF openen</a>
            <a href="{{ asset('storage/' . $magazine->pdf_path) }}" download class="btn btn-secondary">Downloaden</a>
            <a href="{{ route('magazines.edit', $magazine) }}" class="btn btn-secondary">Bewerken</a>
            <form id="delete-magazine" method="POST" action="{{ route('magazines.destroy', $magazine) }}" style="display:inline;">
                @csrf @method('DELETE')
                <button type="button" class="btn btn-danger" onclick="confirmDelete(document.getElementById('delete-magazine'), 'Magazine verwijderen?', '{{ $magazine->title }} wordt permanent verwijderd.')">Verwijderen</button>
            </form>
        </div>
    </div>
</div>

{{-- Inline PDF viewer --}}
<div style="margin-top:2rem;">
    <h2 style="margin-bottom:1rem;">PDF Viewer</h2>
    <iframe src="{{ asset('storage/' . $magazine->pdf_path) }}" style="width:100%;height:80vh;border:1px solid var(--border);border-radius:8px;"></iframe>
</div>
@endsection
