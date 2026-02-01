<div class="form-row">
    <div class="form-group">
        <label class="form-label">Naam *</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $game->name ?? '') }}" required id="game-name">
        @error('name')<div class="form-error">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
        <label class="form-label">Platform</label>
        <select name="platform" class="form-control" id="game-platform">
            <option value="">Selecteer...</option>
            @foreach(['PS5','PS4','PS3','PS2','PS1','PSP','PS Vita','Xbox Series X','Xbox One','Xbox 360','Xbox','Nintendo Switch','Wii U','Wii','GameCube','N64','SNES','NES','3DS','DS','Game Boy','Game Boy Advance','PC','Steam Deck','Sega Mega Drive','Sega Dreamcast'] as $p)
                <option value="{{ $p }}" {{ old('platform', $game->platform ?? '') == $p ? 'selected' : '' }}>{{ $p }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label">Genre</label>
        <input type="text" name="genre" class="form-control" value="{{ old('genre', $game->genre ?? '') }}">
    </div>
    <div class="form-group">
        <label class="form-label">Release datum</label>
        <input type="date" name="release_date" class="form-control" value="{{ old('release_date', isset($game) && $game->release_date ? $game->release_date->format('Y-m-d') : '') }}">
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label">Developer</label>
        <input type="text" name="developer" class="form-control" value="{{ old('developer', $game->developer ?? '') }}">
    </div>
    <div class="form-group">
        <label class="form-label">Publisher</label>
        <input type="text" name="publisher" class="form-control" value="{{ old('publisher', $game->publisher ?? '') }}">
    </div>
</div>

<div class="form-group">
    <label class="form-label">Beschrijving</label>
    <textarea name="description" class="form-control">{{ old('description', $game->description ?? '') }}</textarea>
</div>

<div class="form-group">
    <label class="form-label">Cover afbeelding URL</label>
    <input type="url" name="cover_image_url" class="form-control" value="{{ old('cover_image_url', $game->cover_image_url ?? '') }}" placeholder="https://...">
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label">Aankoopprijs (&euro;)</label>
        <input type="number" name="purchase_price" class="form-control" step="0.01" min="0" value="{{ old('purchase_price', $game->purchase_price ?? '') }}">
    </div>
    <div class="form-group">
        <label class="form-label">Aankoopdatum</label>
        <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date', isset($game) && $game->purchase_date ? $game->purchase_date->format('Y-m-d') : '') }}">
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label">Format *</label>
        <select name="format" class="form-control">
            <option value="physical" {{ old('format', $game->format ?? 'physical') == 'physical' ? 'selected' : '' }}>Fysiek</option>
            <option value="digital" {{ old('format', $game->format ?? '') == 'digital' ? 'selected' : '' }}>Digitaal</option>
            <option value="both" {{ old('format', $game->format ?? '') == 'both' ? 'selected' : '' }}>Beide</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label">Conditie</label>
        <select name="condition" class="form-control">
            <option value="">Selecteer...</option>
            @foreach(['New', 'Good', 'Fair', 'Poor'] as $c)
                <option value="{{ $c }}" {{ old('condition', $game->condition ?? '') == $c ? 'selected' : '' }}>{{ $c }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label">Status *</label>
        <select name="status" class="form-control">
            <option value="collection" {{ old('status', $game->status ?? 'collection') == 'collection' ? 'selected' : '' }}>Collectie</option>
            <option value="wishlist" {{ old('status', $game->status ?? '') == 'wishlist' ? 'selected' : '' }}>Wishlist</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label">Voortgang *</label>
        <select name="completion_status" class="form-control">
            <option value="not_played" {{ old('completion_status', $game->completion_status ?? 'not_played') == 'not_played' ? 'selected' : '' }}>Niet gespeeld</option>
            <option value="playing" {{ old('completion_status', $game->completion_status ?? '') == 'playing' ? 'selected' : '' }}>Bezig</option>
            <option value="completed" {{ old('completion_status', $game->completion_status ?? '') == 'completed' ? 'selected' : '' }}>Uitgespeeld</option>
            <option value="platinum" {{ old('completion_status', $game->completion_status ?? '') == 'platinum' ? 'selected' : '' }}>Platinum</option>
        </select>
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label">Rating (1-10)</label>
        <input type="number" name="rating" class="form-control" min="1" max="10" value="{{ old('rating', $game->rating ?? '') }}">
    </div>
    <div class="form-group">
        <label class="form-label">Barcode (EAN/UPC)</label>
        <input type="text" name="barcode" class="form-control" value="{{ old('barcode', $game->barcode ?? '') }}">
    </div>
</div>

<div class="form-group">
    <label class="form-label">Notities</label>
    <textarea name="notes" class="form-control">{{ old('notes', $game->notes ?? '') }}</textarea>
</div>

<input type="hidden" name="external_api_id" value="{{ old('external_api_id', $game->external_api_id ?? '') }}" id="external_api_id">
<input type="hidden" name="external_api_source" value="{{ old('external_api_source', $game->external_api_source ?? '') }}" id="external_api_source">
