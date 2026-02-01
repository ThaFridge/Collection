@extends('layouts.app')
@section('title', 'Wishlist - GameVault')

@section('content')
<div class="toolbar">
    <h1>Wishlist</h1>
</div>

<div class="game-grid">
    @forelse($games as $game)
        <a href="{{ route('games.show', $game) }}" class="game-card">
            @if($game->cover_image_path)
                <img src="{{ asset('storage/' . $game->cover_image_path) }}" alt="{{ $game->name }}" class="game-card-cover">
            @else
                <div class="game-card-cover-placeholder">🎮</div>
            @endif
            <div class="game-card-body">
                <div class="game-card-title">{{ $game->name }}</div>
                <div class="game-card-meta">
                    @if($game->platform)<span class="badge badge-platform">{{ $game->platform }}</span>@endif
                    <form method="POST" action="{{ route('games.toggle-status', $game) }}" style="display:inline;">
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
    {{ $games->links() }}
</div>
@endsection
