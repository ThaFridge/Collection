<div class="form-row">
    <div class="form-group">
        <label class="form-label">Naam *</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $game->name ?? '') }}" required id="game-name">
        @error('name')<div class="form-error">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
        <label class="form-label">Genre</label>
        <input type="text" name="genre" class="form-control" value="{{ old('genre', $game->genre ?? '') }}">
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label">Release datum</label>
        <input type="date" name="release_date" class="form-control" value="{{ old('release_date', isset($game) && $game->release_date ? $game->release_date->format('Y-m-d') : '') }}">
    </div>
    <div class="form-group">
        <label class="form-label">Rating (1-10)</label>
        <input type="number" name="rating" class="form-control" min="1" max="10" value="{{ old('rating', $game->rating ?? '') }}">
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

<div class="form-group">
    <label class="form-label">Notities</label>
    <textarea name="notes" class="form-control">{{ old('notes', $game->notes ?? '') }}</textarea>
</div>

<input type="hidden" name="external_api_id" value="{{ old('external_api_id', $game->external_api_id ?? '') }}" id="external_api_id">
<input type="hidden" name="external_api_source" value="{{ old('external_api_source', $game->external_api_source ?? '') }}" id="external_api_source">
