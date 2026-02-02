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

        {{-- Platform badges --}}
        @if($game->platforms->count())
        <div style="display:flex;gap:0.35rem;flex-wrap:wrap;margin-bottom:0.75rem;">
            @foreach($game->platforms as $platform)
                <span class="badge badge-platform">{{ $platform->platform }}</span>
            @endforeach
        </div>
        @endif

        @if($game->rating)
            <div style="margin-bottom:0.5rem;">
                <span class="badge" style="background:var(--warning);color:#fff;">{{ $game->rating }}/10</span>
            </div>
        @endif

        @if($game->description)
            <p style="color:var(--text-muted);margin:0.75rem 0;line-height:1.6;">{{ $game->description }}</p>
        @endif

        <dl class="detail-meta">
            @if($game->developer)<dt>Developer</dt><dd>{{ $game->developer }}</dd>@endif
            @if($game->publisher)<dt>Publisher</dt><dd>{{ $game->publisher }}</dd>@endif
            @if($game->genre)<dt>Genre</dt><dd>{{ $game->genre }}</dd>@endif
            @if($game->release_date)<dt>Release</dt><dd>{{ $game->release_date->format('d-m-Y') }}</dd>@endif
        </dl>

        @if($game->notes)
            <div style="margin-top:1rem;padding:1rem;background:var(--bg-input);border-radius:8px;">
                <strong>Notities:</strong><br>{{ $game->notes }}
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
            <form id="delete-game" method="POST" action="{{ route('games.destroy', $game) }}" style="display:inline;">
                @csrf @method('DELETE')
                <button type="button" class="btn btn-danger" onclick="confirmDelete(document.getElementById('delete-game'), 'Game verwijderen?', '{{ $game->name }} en alle platforms worden permanent verwijderd.')">Verwijderen</button>
            </form>
        </div>
    </div>
</div>

{{-- Platforms --}}
@if($game->platforms->count())
<div style="margin-top:2rem;">
    <h2 style="margin-bottom:1rem;">Platforms</h2>
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:2px solid var(--border);text-align:left;">
                    <th style="padding:0.5rem;">Platform</th>
                    <th style="padding:0.5rem;">Format</th>
                    <th style="padding:0.5rem;">Voortgang</th>
                    <th style="padding:0.5rem;">Prijs</th>
                </tr>
            </thead>
            <tbody>
                @php $labels = ['not_played' => 'Niet gespeeld', 'playing' => 'Bezig', 'completed' => 'Uitgespeeld', 'platinum' => 'Platinum']; @endphp
                @foreach($game->platforms as $platform)
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:0.5rem;font-weight:600;">{{ $platform->platform }}</td>
                    <td style="padding:0.5rem;">{{ $platform->format === 'physical' ? 'Fysiek' : ($platform->format === 'digital' ? 'Digitaal' : 'Beide') }}</td>
                    <td style="padding:0.5rem;">
                        <span class="badge badge-completion badge-{{ $platform->completion_status }}">{{ $labels[$platform->completion_status] ?? $platform->completion_status }}</span>
                    </td>
                    <td style="padding:0.5rem;">{{ $platform->purchase_price ? '€' . number_format($platform->purchase_price, 2, ',', '.') : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Screenshots --}}
@if($game->images->count())
<div style="margin-top:2rem;">
    <h2 style="margin-bottom:1rem;">Screenshots</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(250px, 1fr));gap:0.75rem;">
        @foreach($game->images as $image)
            <div style="position:relative;border-radius:8px;overflow:hidden;border:1px solid var(--border);">
                <img src="{{ asset('storage/' . $image->image_path) }}" alt="Screenshot" style="width:100%;aspect-ratio:16/9;object-fit:cover;display:block;cursor:pointer;" onclick="openLightbox('{{ asset('storage/' . $image->image_path) }}')">
                <form method="POST" action="{{ route('games.screenshots.destroy', [$game, $image]) }}" style="position:absolute;top:0.35rem;right:0.35rem;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" style="padding:0.15rem 0.4rem;font-size:0.7rem;opacity:0.8;">X</button>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endif

@if($game->images->count() < 8)
<div style="margin-top:1rem;">
    <form method="POST" action="{{ route('games.screenshots.store', $game) }}" enctype="multipart/form-data" style="display:flex;gap:0.5rem;align-items:center;">
        @csrf
        <input type="file" name="screenshot" accept="image/*" class="form-control" style="max-width:300px;">
        <button type="submit" class="btn btn-secondary btn-sm">Screenshot uploaden</button>
    </form>
</div>
@endif

{{-- Lightbox --}}
<div id="lightbox" onclick="closeLightbox()" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.9);z-index:9999;align-items:center;justify-content:center;cursor:pointer;">
    <img id="lightbox-img" src="" alt="" style="max-width:90%;max-height:90%;border-radius:8px;">
</div>

@push('scripts')
<script>
function openLightbox(src) {
    document.getElementById('lightbox-img').src = src;
    document.getElementById('lightbox').style.display = 'flex';
}
function closeLightbox() {
    document.getElementById('lightbox').style.display = 'none';
}
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeLightbox(); });
</script>
@endpush

{{-- Achievements --}}
@if($game->achievements_fetched && $game->achievements_supported && $game->achievements->count())
<div style="margin-top:2rem;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
        <h2 style="margin:0;">Achievements <span style="font-size:0.9rem;font-weight:400;color:var(--text-muted);">({{ $game->achievements->count() }})</span></h2>
        @if($game->external_api_id && in_array($game->external_api_source, ['rawg', 'igdb']))
            <form method="POST" action="{{ route('games.refresh-achievements', $game) }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-secondary btn-sm">Vernieuwen</button>
            </form>
        @endif
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(300px, 1fr));gap:0.75rem;">
        @foreach($game->achievements as $achievement)
            <div style="display:flex;gap:0.75rem;padding:0.75rem;background:var(--bg-card);border:1px solid var(--border);border-radius:8px;align-items:center;">
                @if($achievement->image_url)
                    <img src="{{ $achievement->image_url }}" alt="" style="width:48px;height:48px;border-radius:6px;object-fit:cover;flex-shrink:0;">
                @else
                    <div style="width:48px;height:48px;border-radius:6px;background:var(--bg-input);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:1.2rem;">🏆</div>
                @endif
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:600;font-size:0.85rem;">{{ $achievement->name }}</div>
                    @if($achievement->description)
                        <div style="font-size:0.75rem;color:var(--text-muted);margin-top:0.15rem;">{{ $achievement->description }}</div>
                    @endif
                </div>
                @if($achievement->percent !== null)
                    <div style="flex-shrink:0;text-align:center;">
                        <div style="font-size:0.75rem;font-weight:700;color:var(--accent);">{{ number_format($achievement->percent, 1) }}%</div>
                        <div style="font-size:0.6rem;color:var(--text-muted);">spelers</div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endif
@endsection
