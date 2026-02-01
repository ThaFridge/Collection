@extends('layouts.app')
@section('title', 'Wishlist - GameVault')

@section('content')
<div class="toolbar">
    <h1>Wishlist</h1>
</div>

<div class="game-grid">
    @forelse($platforms as $platform)
        <a href="{{ route('games.show', $platform->game) }}" class="game-card">
            @if($platform->game->cover_image_path)
                <img src="{{ asset('storage/' . $platform->game->cover_image_path) }}" alt="{{ $platform->game->name }}" class="game-card-cover">
            @else
                <div class="game-card-cover-placeholder">🎮</div>
            @endif
            <div class="game-card-body">
                <div class="game-card-title">{{ $platform->game->name }}</div>
                <div class="game-card-meta">
                    <span class="badge badge-platform">{{ $platform->platform }}</span>
                    <span class="badge badge-format">{{ ucfirst($platform->format) }}</span>
                    <form method="POST" action="{{ route('platforms.toggle-status', $platform) }}" style="display:inline;" onclick="event.preventDefault(); event.stopPropagation(); this.submit();">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-primary btn-sm" style="margin-top:0.3rem;">Naar Collectie</button>
                    </form>
                </div>
            </div>
        </a>
    @empty
        <p style="grid-column:1/-1;text-align:center;color:var(--text-muted);padding:3rem;">
            Je wishlist is leeg!
        </p>
    @endforelse
</div>

<div class="pagination">
    {{ $platforms->links() }}
</div>
@endsection
