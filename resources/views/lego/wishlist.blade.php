@extends('layouts.app')
@section('title', 'LEGO Wishlist - GameVault')

@section('content')
<div class="toolbar">
    <h1>LEGO Wishlist</h1>
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
                    <span style="font-weight:600;">#{{ $set->set_number }}</span>
                    @if($set->theme)<span class="badge badge-theme">{{ $set->theme }}</span>@endif
                    <form method="POST" action="{{ route('lego.toggle-status', $set) }}" style="display:inline;">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-primary btn-sm" style="margin-top:0.3rem;">Naar Collectie</button>
                    </form>
                </div>
            </div>
        </a>
    @empty
        <p style="grid-column:1/-1;text-align:center;color:var(--text-muted);padding:3rem;">
            Je LEGO wishlist is leeg!
        </p>
    @endforelse
</div>

<div class="pagination">
    {{ $sets->links() }}
</div>
@endsection
