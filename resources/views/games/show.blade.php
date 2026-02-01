@extends('layouts.app')
@section('title', $game->name . ' - GameVault')

@section('content')
<div class="detail-header">
    <div class="detail-cover">
        @if($game->cover_image_path)
            <img src="{{ asset('storage/' . $game->cover_image_path) }}" alt="{{ $game->name }}">
        @else
            <div class="game-card-cover-placeholder" style="height:400px;border-radius:12px;">🎮</div>
        @endif
    </div>
    <div class="detail-info">
        <h1 class="detail-title">{{ $game->name }}</h1>
        <div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-bottom:0.5rem;">
            @if($game->platform)<span class="badge badge-platform">{{ $game->platform }}</span>@endif
            <span class="badge badge-format">{{ ucfirst($game->format) }}</span>
            @if($game->completion_status !== 'not_played')
                <span class="badge badge-completion badge-{{ $game->completion_status }}">{{ ucfirst($game->completion_status) }}</span>
            @endif
            @if($game->rating)
                <span class="badge" style="background:var(--warning);color:#fff;">{{ $game->rating }}/10</span>
            @endif
        </div>

        @if($game->description)
            <p style="color:var(--text-muted);margin:1rem 0;line-height:1.6;">{{ $game->description }}</p>
        @endif

        <dl class="detail-meta">
            @if($game->developer)<dt>Developer</dt><dd>{{ $game->developer }}</dd>@endif
            @if($game->publisher)<dt>Publisher</dt><dd>{{ $game->publisher }}</dd>@endif
            @if($game->genre)<dt>Genre</dt><dd>{{ $game->genre }}</dd>@endif
            @if($game->release_date)<dt>Release</dt><dd>{{ $game->release_date->format('d-m-Y') }}</dd>@endif
            @if($game->purchase_price)<dt>Aankoopprijs</dt><dd>&euro;{{ number_format($game->purchase_price, 2, ',', '.') }}</dd>@endif
            @if($game->purchase_date)<dt>Aankoopdatum</dt><dd>{{ $game->purchase_date->format('d-m-Y') }}</dd>@endif
            @if($game->condition)<dt>Conditie</dt><dd>{{ $game->condition }}</dd>@endif
            @if($game->barcode)<dt>Barcode</dt><dd>{{ $game->barcode }}</dd>@endif
        </dl>

        @if($game->notes)
            <div style="margin-top:1rem;padding:1rem;background:var(--bg-input);border-radius:8px;">
                <strong>Notities:</strong><br>{{ $game->notes }}
            </div>
        @endif

        @if($otherPlatforms->count())
            <div style="margin-top:1rem;">
                <strong>Ook op:</strong>
                @foreach($otherPlatforms as $other)
                    <a href="{{ route('games.show', $other) }}" class="badge badge-platform" style="text-decoration:none;">{{ $other->platform }} ({{ ucfirst($other->format) }})</a>
                @endforeach
            </div>
        @endif

        @if($allTags->count())
            <div style="margin-top:0.75rem;">
                <strong style="font-size:0.85rem;">Tags:</strong>
                <div style="display:flex;gap:0.35rem;flex-wrap:wrap;margin-top:0.35rem;">
                    @foreach($allTags as $tag)
                        <form method="POST" action="{{ route('games.toggle-tag', $game) }}" style="display:inline;">
                            @csrf
                            <input type="hidden" name="tag_id" value="{{ $tag->id }}">
                            <button type="submit" class="badge {{ $game->tags->contains($tag->id) ? 'badge-platform' : 'badge-format' }}" style="cursor:pointer;border:none;font-family:inherit;">{{ $tag->name }}</button>
                        </form>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="detail-actions">
            <a href="{{ route('games.edit', $game) }}" class="btn btn-secondary">Bewerken</a>
            <form method="POST" action="{{ route('games.toggle-status', $game) }}" style="display:inline;">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-secondary">
                    {{ $game->status === 'collection' ? 'Naar Wishlist' : 'Naar Collectie' }}
                </button>
            </form>
            <form id="delete-game" method="POST" action="{{ route('games.destroy', $game) }}" style="display:inline;">
                @csrf @method('DELETE')
                <button type="button" class="btn btn-danger" onclick="confirmDelete(document.getElementById('delete-game'), 'Game verwijderen?', '{{ $game->name }} wordt permanent verwijderd.')">Verwijderen</button>
            </form>
        </div>
    </div>
</div>
@endsection
