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

{{-- Platforms sectie --}}
<div style="margin-top:2rem;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
        <h2 style="margin:0;">Platforms</h2>
        <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('add-platform-form').style.display = document.getElementById('add-platform-form').style.display === 'none' ? 'block' : 'none'">+ Platform toevoegen</button>
    </div>

    {{-- Add platform form --}}
    <div id="add-platform-form" style="display:none;padding:1rem;background:var(--bg-card);border:1px solid var(--border);border-radius:8px;margin-bottom:1rem;">
        <form method="POST" action="{{ route('games.platforms.store', $game) }}">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Platform *</label>
                    <select name="platform" class="form-control" required>
                        <option value="">Selecteer...</option>
                        @foreach(['PS5','PS4','PS3','PS2','PS1','PSP','PS Vita','Xbox Series X','Xbox One','Xbox 360','Xbox','Nintendo Switch','Wii U','Wii','GameCube','N64','SNES','NES','3DS','DS','Game Boy','Game Boy Advance','PC','Steam Deck','Sega Mega Drive','Sega Dreamcast'] as $p)
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
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-control" required>
                        <option value="collection">Collectie</option>
                        <option value="wishlist">Wishlist</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Voortgang *</label>
                    <select name="completion_status" class="form-control" required>
                        <option value="not_played">Niet gespeeld</option>
                        <option value="playing">Bezig</option>
                        <option value="completed">Uitgespeeld</option>
                        <option value="platinum">Platinum</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Aankoopprijs (&euro;)</label>
                    <input type="number" name="purchase_price" class="form-control" step="0.01" min="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Aankoopdatum</label>
                    <input type="date" name="purchase_date" class="form-control">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Conditie</label>
                    <select name="condition" class="form-control">
                        <option value="">Selecteer...</option>
                        @foreach(['New', 'Good', 'Fair', 'Poor'] as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Barcode</label>
                    <input type="text" name="barcode" class="form-control">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Platform toevoegen</button>
        </form>
    </div>

    {{-- Platforms tabel --}}
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:2px solid var(--border);text-align:left;">
                    <th style="padding:0.75rem 0.5rem;">Platform</th>
                    <th style="padding:0.75rem 0.5rem;">Format</th>
                    <th style="padding:0.75rem 0.5rem;">Status</th>
                    <th style="padding:0.75rem 0.5rem;">Voortgang</th>
                    <th style="padding:0.75rem 0.5rem;">Prijs</th>
                    <th style="padding:0.75rem 0.5rem;">Conditie</th>
                    <th style="padding:0.75rem 0.5rem;">Acties</th>
                </tr>
            </thead>
            <tbody>
                @foreach($game->platforms as $platform)
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:0.75rem 0.5rem;font-weight:600;">{{ $platform->platform }}</td>
                    <td style="padding:0.75rem 0.5rem;">{{ ucfirst($platform->format) }}</td>
                    <td style="padding:0.75rem 0.5rem;">
                        <span class="badge {{ $platform->status === 'collection' ? 'badge-platform' : 'badge-format' }}">
                            {{ $platform->status === 'collection' ? 'Collectie' : 'Wishlist' }}
                        </span>
                    </td>
                    <td style="padding:0.75rem 0.5rem;">
                        @php
                            $labels = ['not_played' => 'Niet gespeeld', 'playing' => 'Bezig', 'completed' => 'Uitgespeeld', 'platinum' => 'Platinum'];
                        @endphp
                        {{ $labels[$platform->completion_status] ?? $platform->completion_status }}
                    </td>
                    <td style="padding:0.75rem 0.5rem;">
                        @if($platform->purchase_price)
                            &euro;{{ number_format($platform->purchase_price, 2, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                    <td style="padding:0.75rem 0.5rem;">{{ $platform->condition ?? '-' }}</td>
                    <td style="padding:0.75rem 0.5rem;">
                        <div style="display:flex;gap:0.25rem;">
                            <form method="POST" action="{{ route('platforms.toggle-status', $platform) }}" style="display:inline;">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-secondary btn-sm">
                                    {{ $platform->status === 'collection' ? 'Wishlist' : 'Collectie' }}
                                </button>
                            </form>
                            <form id="delete-platform-{{ $platform->id }}" method="POST" action="{{ route('platforms.destroy', $platform) }}" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(document.getElementById('delete-platform-{{ $platform->id }}'), 'Platform verwijderen?', '{{ $platform->platform }} wordt verwijderd.')">X</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
