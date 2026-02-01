@extends('layouts.app')
@section('title', 'Game toevoegen - GameVault')

@section('content')
<div class="toolbar">
    <h1>Game toevoegen</h1>
    <a href="{{ route('games.index') }}" class="btn btn-secondary">Terug</a>
</div>

<div style="max-width:800px;">
    <form method="POST" action="{{ route('games.store') }}">
        @csrf
        @include("games._search")

        <h3 style="margin:1.5rem 0 0.75rem;">Game informatie</h3>
        @include('games._form', ['game' => null])

        <h3 style="margin:1.5rem 0 0.75rem;">Platform</h3>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Platform *</label>
                <select name="platform" class="form-control" required id="game-platform">
                    <option value="">Selecteer...</option>
                    @foreach(['PS5','PS4','PS3','PS2','PS1','PSP','PS Vita','Xbox Series X','Xbox One','Xbox 360','Xbox','Nintendo Switch','Wii U','Wii','GameCube','N64','SNES','NES','3DS','DS','Game Boy','Game Boy Advance','PC','Steam Deck','Sega Mega Drive','Sega Dreamcast'] as $p)
                        <option value="{{ $p }}" {{ old('platform') == $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Format *</label>
                <select name="format" class="form-control" required>
                    <option value="physical" {{ old('format', 'physical') == 'physical' ? 'selected' : '' }}>Fysiek</option>
                    <option value="digital" {{ old('format') == 'digital' ? 'selected' : '' }}>Digitaal</option>
                    <option value="both" {{ old('format') == 'both' ? 'selected' : '' }}>Beide</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Status *</label>
                <select name="status" class="form-control" required>
                    <option value="collection" {{ old('status', 'collection') == 'collection' ? 'selected' : '' }}>Collectie</option>
                    <option value="wishlist" {{ old('status') == 'wishlist' ? 'selected' : '' }}>Wishlist</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Voortgang *</label>
                <select name="completion_status" class="form-control" required>
                    <option value="not_played" {{ old('completion_status', 'not_played') == 'not_played' ? 'selected' : '' }}>Niet gespeeld</option>
                    <option value="playing" {{ old('completion_status') == 'playing' ? 'selected' : '' }}>Bezig</option>
                    <option value="completed" {{ old('completion_status') == 'completed' ? 'selected' : '' }}>Uitgespeeld</option>
                    <option value="platinum" {{ old('completion_status') == 'platinum' ? 'selected' : '' }}>Platinum</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Aankoopprijs (&euro;)</label>
                <input type="number" name="purchase_price" class="form-control" step="0.01" min="0" value="{{ old('purchase_price') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Aankoopdatum</label>
                <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date') }}">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Conditie</label>
                <select name="condition" class="form-control">
                    <option value="">Selecteer...</option>
                    @foreach(['New', 'Good', 'Fair', 'Poor'] as $c)
                        <option value="{{ $c }}" {{ old('condition') == $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Barcode (EAN/UPC)</label>
                <input type="text" name="barcode" class="form-control" value="{{ old('barcode') }}">
            </div>
        </div>

        <div id="duplicate-warning" style="display:none;padding:0.75rem 1rem;border-radius:8px;background:rgba(243,156,18,0.15);color:var(--warning);border:1px solid var(--warning);margin-bottom:1rem;font-size:0.9rem;">
            Deze game bestaat al in je collectie met hetzelfde platform en format.
        </div>

        <div style="margin-top:1.5rem;">
            <button type="submit" class="btn btn-primary">Opslaan</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
(function() {
    var nameEl = document.querySelector('[name="name"]');
    var platformEl = document.querySelector('[name="platform"]');
    var formatEl = document.querySelector('[name="format"]');
    var warning = document.getElementById('duplicate-warning');
    var checkTimer;

    function checkDuplicate() {
        clearTimeout(checkTimer);
        var name = nameEl ? nameEl.value : '';
        var platform = platformEl ? platformEl.value : '';
        var format = formatEl ? formatEl.value : '';
        if (!name || !platform) { warning.style.display = 'none'; return; }

        checkTimer = setTimeout(function() {
            fetch('/api/games/check-duplicate?name=' + encodeURIComponent(name) + '&platform=' + encodeURIComponent(platform) + '&format=' + encodeURIComponent(format))
                .then(function(r) { return r.json(); })
                .then(function(d) { warning.style.display = d.exists ? 'block' : 'none'; })
                .catch(function() {});
        }, 500);
    }

    if (nameEl) nameEl.addEventListener('change', checkDuplicate);
    if (platformEl) platformEl.addEventListener('change', checkDuplicate);
    if (formatEl) formatEl.addEventListener('change', checkDuplicate);
})();
</script>
@endpush
@endsection
