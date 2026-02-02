@extends('layouts.app')
@section('title', $game->name . ' bewerken - GameVault')

@section('content')
<div class="toolbar">
    <h1>{{ $game->name }} bewerken</h1>
    <a href="{{ route('games.show', $game) }}" class="btn btn-secondary">Terug</a>
</div>

<div style="max-width:800px;">
    <form method="POST" action="{{ route('games.update', $game) }}">
        @csrf @method('PUT')
        @include("games._search")
        @include('games._form', ['game' => $game])
        <div style="margin-top:1.5rem;">
            <button type="submit" class="btn btn-primary">Bijwerken</button>
        </div>
    </form>

    {{-- Platforms beheren --}}
    <h3 style="margin:2rem 0 1rem;">Platforms</h3>

    @foreach($game->platforms as $platform)
    <div style="padding:1rem;background:var(--bg-card);border:1px solid var(--border);border-radius:8px;margin-bottom:0.75rem;">
        <form method="POST" action="{{ route('platforms.update', $platform) }}">
            @csrf @method('PATCH')
            <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.75rem;">
                <strong>{{ $platform->platform }}</strong>
                <span class="badge {{ $platform->status === 'collection' ? 'badge-platform' : 'badge-format' }}">{{ $platform->status === 'collection' ? 'Collectie' : 'Wishlist' }}</span>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="collection" {{ $platform->status == 'collection' ? 'selected' : '' }}>Collectie</option>
                        <option value="wishlist" {{ $platform->status == 'wishlist' ? 'selected' : '' }}>Wishlist</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Voortgang</label>
                    <select name="completion_status" class="form-control">
                        <option value="not_played" {{ $platform->completion_status == 'not_played' ? 'selected' : '' }}>Niet gespeeld</option>
                        <option value="playing" {{ $platform->completion_status == 'playing' ? 'selected' : '' }}>Bezig</option>
                        <option value="completed" {{ $platform->completion_status == 'completed' ? 'selected' : '' }}>Uitgespeeld</option>
                        <option value="platinum" {{ $platform->completion_status == 'platinum' ? 'selected' : '' }}>Platinum</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Aankoopprijs (&euro;)</label>
                    <input type="number" name="purchase_price" class="form-control" step="0.01" min="0" value="{{ $platform->purchase_price }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Aankoopdatum</label>
                    <input type="date" name="purchase_date" class="form-control" value="{{ $platform->purchase_date?->format('Y-m-d') }}">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Barcode</label>
                <input type="text" name="barcode" class="form-control" value="{{ $platform->barcode }}">
                @include('games._barcode')
            </div>
            <div style="display:flex;gap:0.5rem;margin-top:0.5rem;">
                <button type="submit" class="btn btn-primary btn-sm">Opslaan</button>
        </form>
                <form id="delete-platform-{{ $platform->id }}" method="POST" action="{{ route('platforms.destroy', $platform) }}">
                    @csrf @method('DELETE')
                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(document.getElementById('delete-platform-{{ $platform->id }}'), 'Platform verwijderen?', '{{ $platform->platform }} wordt verwijderd.')">Verwijderen</button>
                </form>
            </div>
    </div>
    @endforeach

    {{-- Platform toevoegen --}}
    <div style="padding:1rem;background:var(--bg-card);border:1px dashed var(--border);border-radius:8px;">
        <strong style="display:block;margin-bottom:0.75rem;">Platform toevoegen</strong>
        <form method="POST" action="{{ route('games.platforms.store', $game) }}">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Platform *</label>
                    <select name="platform" class="form-control" required>
                        <option value="">Selecteer...</option>
                        @foreach(config('platforms.game') as $p)
                            <option value="{{ $p }}">{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Format *</label>
                    <select name="format" class="form-control" required>
                        <option value="physical">Fysiek</option>
                        <option value="digital">Digitaal</option>
                        <option value="both">Beide</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control" required>
                        <option value="collection">Collectie</option>
                        <option value="wishlist">Wishlist</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Voortgang</label>
                    <select name="completion_status" class="form-control" required>
                        <option value="not_played">Niet gespeeld</option>
                        <option value="playing">Bezig</option>
                        <option value="completed">Uitgespeeld</option>
                        <option value="platinum">Platinum</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">+ Toevoegen</button>
        </form>
    </div>

    {{-- Screenshots beheren --}}
    <h3 style="margin:2rem 0 1rem;">Screenshots ({{ $game->images->count() }}/8)</h3>

    @if($game->images->count())
    <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(180px, 1fr));gap:0.5rem;margin-bottom:1rem;">
        @foreach($game->images as $image)
            <div style="position:relative;border-radius:8px;overflow:hidden;border:1px solid var(--border);">
                <img src="{{ asset('storage/' . $image->image_path) }}" alt="Screenshot" style="width:100%;aspect-ratio:16/9;object-fit:cover;display:block;">
                <form method="POST" action="{{ route('games.screenshots.destroy', [$game, $image]) }}" style="position:absolute;top:0.25rem;right:0.25rem;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" style="padding:0.15rem 0.4rem;font-size:0.7rem;">X</button>
                </form>
            </div>
        @endforeach
    </div>
    @endif

    @if($game->images->count() < 8)
    <form method="POST" action="{{ route('games.screenshots.store', $game) }}" enctype="multipart/form-data" style="display:flex;gap:0.5rem;align-items:center;">
        @csrf
        <input type="file" name="screenshot" accept="image/*" class="form-control" style="max-width:300px;">
        <button type="submit" class="btn btn-secondary btn-sm">Uploaden</button>
    </form>
    @endif
</div>
@endsection
