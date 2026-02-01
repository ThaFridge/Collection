@extends('layouts.app')
@section('title', 'Game Collectie - GameVault')

@section('content')
<div class="stats-bar">
    <div class="stat-item">
        <div class="stat-value">{{ $stats['total'] }}</div>
        <div class="stat-label">Games</div>
    </div>
    <div class="stat-item">
        <div class="stat-value">&euro;{{ number_format($stats['total_value'], 2, ',', '.') }}</div>
        <div class="stat-label">Totale waarde</div>
    </div>
    <div class="stat-item">
        <div class="stat-value">{{ $stats['physical'] }}</div>
        <div class="stat-label">Fysiek</div>
    </div>
    <div class="stat-item">
        <div class="stat-value">{{ $stats['digital'] }}</div>
        <div class="stat-label">Digitaal</div>
    </div>
</div>

<div class="toolbar">
    <h1>Collectie</h1>
    <div class="filters">
        <form method="GET" action="{{ route('games.index') }}" style="display:flex;gap:0.5rem;flex-wrap:wrap;">
            <input type="text" name="q" placeholder="Zoeken..." value="{{ request('q') }}" class="form-control" style="width:160px;">
            <select name="platform" onchange="this.form.submit()">
                <option value="">Alle platforms</option>
                @foreach($platforms as $p)
                    <option value="{{ $p }}" {{ request('platform') == $p ? 'selected' : '' }}>{{ $p }}</option>
                @endforeach
            </select>
            <select name="completion_status" onchange="this.form.submit()">
                <option value="">Alle statussen</option>
                <option value="not_played" {{ request('completion_status') == 'not_played' ? 'selected' : '' }}>Niet gespeeld</option>
                <option value="playing" {{ request('completion_status') == 'playing' ? 'selected' : '' }}>Bezig</option>
                <option value="completed" {{ request('completion_status') == 'completed' ? 'selected' : '' }}>Uitgespeeld</option>
                <option value="platinum" {{ request('completion_status') == 'platinum' ? 'selected' : '' }}>Platinum</option>
            </select>
            <select name="sort" onchange="this.form.submit()">
                <option value="name" {{ request('sort', 'name') == 'name' ? 'selected' : '' }}>Naam A-Z</option>
                <option value="name-desc" {{ request('sort') == 'name-desc' ? 'selected' : '' }}>Naam Z-A</option>
                <option value="created_at-desc" {{ request('sort') == 'created_at-desc' ? 'selected' : '' }}>Nieuwste eerst</option>
                <option value="purchase_price-desc" {{ request('sort') == 'purchase_price-desc' ? 'selected' : '' }}>Prijs hoog-laag</option>
                <option value="release_date-desc" {{ request('sort') == 'release_date-desc' ? 'selected' : '' }}>Release nieuwst</option>
            </select>
            <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
        </form>
    </div>
</div>

<div class="game-grid">
    @forelse($games as $game)
        <a href="{{ route('games.show', $game) }}" class="game-card">
            @if($game->cover_image_path)
                <img data-src="{{ asset('storage/' . $game->cover_image_path) }}" alt="{{ $game->name }}" class="game-card-cover" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7">
            @else
                <div class="game-card-cover-placeholder">🎮</div>
            @endif
            <div class="game-card-body">
                <div class="game-card-title">{{ $game->name }}</div>
                <div class="game-card-meta">
                    @if($game->platform)<span class="badge badge-platform">{{ $game->platform }}</span>@endif
                    <span class="badge badge-format">{{ ucfirst($game->format) }}</span>
                    @if($game->completion_status !== 'not_played')
                        <span class="badge badge-completion badge-{{ $game->completion_status }}">{{ ucfirst($game->completion_status) }}</span>
                    @endif
                </div>
            </div>
        </a>
    @empty
        <p style="grid-column:1/-1;text-align:center;color:var(--text-muted);padding:3rem;">
            Geen games gevonden. <a href="{{ route('games.create') }}" style="color:var(--accent);">Voeg je eerste game toe!</a>
        </p>
    @endforelse
</div>

<div class="pagination">
    {{ $games->withQueryString()->links() }}
</div>
@endsection
