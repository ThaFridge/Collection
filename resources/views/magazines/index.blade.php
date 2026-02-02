@extends('layouts.app')
@section('title', 'Magazines & Manuals - GameVault')

@section('content')
<div class="stats-bar">
    <div class="stat-item">
        <div class="stat-value">{{ $total }}</div>
        <div class="stat-label">Magazines</div>
    </div>
</div>

<div class="toolbar">
    <h1>Magazines & Manuals</h1>
    <div class="filters">
        <form method="GET" action="{{ route('magazines.index') }}" style="display:flex;gap:0.5rem;flex-wrap:wrap;">
            <input type="text" name="q" placeholder="Zoeken..." value="{{ request('q') }}" class="form-control" style="width:160px;">
            <select name="type" onchange="this.form.submit()">
                <option value="">Alle types</option>
                <option value="magazine" {{ request('type') == 'magazine' ? 'selected' : '' }}>Magazines</option>
                <option value="manual" {{ request('type') == 'manual' ? 'selected' : '' }}>Manuals</option>
            </select>
            <select name="year" onchange="this.form.submit()">
                <option value="">Alle jaren</option>
                @foreach($years as $y)
                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
            <select name="sort" onchange="this.form.submit()">
                <option value="year-desc" {{ request('sort', 'year-desc') == 'year-desc' ? 'selected' : '' }}>Nieuwste eerst</option>
                <option value="year" {{ request('sort') == 'year' ? 'selected' : '' }}>Oudste eerst</option>
                <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Titel A-Z</option>
                <option value="title-desc" {{ request('sort') == 'title-desc' ? 'selected' : '' }}>Titel Z-A</option>
            </select>
            <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
        </form>
    </div>
</div>

<div class="game-grid">
    @forelse($magazines as $magazine)
        <a href="{{ route('magazines.show', $magazine) }}" class="game-card">
            @if($magazine->cover_image_path)
                <img data-src="{{ asset('storage/' . $magazine->cover_image_path) }}" alt="{{ $magazine->title }}" class="game-card-cover" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7">
            @else
                <div class="game-card-cover-placeholder">{{ $magazine->type === 'manual' ? '📋' : '📖' }}</div>
            @endif
            <div class="game-card-body">
                <div class="game-card-title">{{ $magazine->title }}</div>
                <div class="game-card-meta">
                    <span class="badge badge-format">{{ $magazine->type === 'manual' ? 'Manual' : 'Magazine' }}</span>
                    {{ $magazine->year }}
                    @if($magazine->publisher)
                        &middot; {{ $magazine->publisher }}
                    @endif
                    @if($magazine->issue_number)
                        &middot; #{{ $magazine->issue_number }}
                    @endif
                </div>
            </div>
        </a>
    @empty
        <p style="grid-column:1/-1;text-align:center;color:var(--text-muted);padding:3rem;">
            Geen resultaten gevonden. <a href="{{ route('magazines.create') }}" style="color:var(--accent);">Upload je eerste!</a>
        </p>
    @endforelse
</div>

<div class="pagination">
    {{ $magazines->withQueryString()->links() }}
</div>
@endsection
