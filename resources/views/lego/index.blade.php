@extends('layouts.app')
@section('title', 'LEGO Collectie - GameVault')

@section('content')
<div class="stats-bar">
    <div class="stat-item">
        <div class="stat-value">{{ $stats['total'] }}</div>
        <div class="stat-label">Sets</div>
    </div>
    <div class="stat-item">
        <div class="stat-value">&euro;{{ number_format($stats['total_value'], 2, ',', '.') }}</div>
        <div class="stat-label">Totale waarde</div>
    </div>
    <div class="stat-item">
        <div class="stat-value">{{ number_format($stats['total_pieces']) }}</div>
        <div class="stat-label">Steentjes</div>
    </div>
    <div class="stat-item">
        <div class="stat-value">{{ $stats['built'] }}</div>
        <div class="stat-label">Gebouwd</div>
    </div>
</div>

<div class="toolbar">
    <h1>LEGO Collectie</h1>
    <div class="filters">
        <form method="GET" action="{{ route('lego.index') }}" style="display:flex;gap:0.5rem;flex-wrap:wrap;">
            <input type="text" name="q" placeholder="Zoeken..." value="{{ request('q') }}" class="form-control" style="width:160px;">
            <select name="theme" onchange="this.form.submit()">
                <option value="">Alle thema's</option>
                @foreach($themes as $t)
                    <option value="{{ $t }}" {{ request('theme') == $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
            <select name="build_status" onchange="this.form.submit()">
                <option value="">Alle statussen</option>
                <option value="not_built" {{ request('build_status') == 'not_built' ? 'selected' : '' }}>Niet gebouwd</option>
                <option value="in_progress" {{ request('build_status') == 'in_progress' ? 'selected' : '' }}>Bezig</option>
                <option value="built" {{ request('build_status') == 'built' ? 'selected' : '' }}>Gebouwd</option>
                <option value="displayed" {{ request('build_status') == 'displayed' ? 'selected' : '' }}>Tentoongesteld</option>
            </select>
            <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
        </form>
    </div>
</div>

<div class="game-grid">
    @forelse($sets as $set)
        <a href="{{ route('lego.show', $set) }}" class="game-card">
            @if($set->image_path)
                <img src="{{ asset('storage/' . $set->image_path) }}" alt="{{ $set->name }}" class="game-card-cover" style="aspect-ratio:1/1;">
            @else
                <div class="game-card-cover-placeholder" style="aspect-ratio:1/1;">&#129521;</div>
            @endif
            <div class="game-card-body">
                <div class="game-card-title">{{ $set->name }}</div>
                <div class="game-card-meta">
                    <span style="font-weight:600;color:var(--text);">#{{ $set->set_number }}</span>
                    @if($set->theme)<span class="badge badge-theme">{{ $set->theme }}</span>@endif
                    <span class="badge badge-{{ $set->build_status }}">
                        @switch($set->build_status)
                            @case('not_built') Niet gebouwd @break
                            @case('in_progress') Bezig @break
                            @case('built') Gebouwd @break
                            @case('displayed') Tentoongesteld @break
                        @endswitch
                    </span>
                    @if($set->piece_count)
                        <span style="font-size:0.7rem;">{{ number_format($set->piece_count) }} pcs</span>
                    @endif
                </div>
            </div>
        </a>
    @empty
        <p style="grid-column:1/-1;text-align:center;color:var(--text-muted);padding:3rem;">
            Geen LEGO sets gevonden. <a href="{{ route('lego.create') }}" style="color:var(--accent);">Voeg je eerste set toe!</a>
        </p>
    @endforelse
</div>

<div class="pagination">
    {{ $sets->withQueryString()->links() }}
</div>
@endsection
