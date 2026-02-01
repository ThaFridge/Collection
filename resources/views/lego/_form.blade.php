<div class="form-row">
    <div class="form-group">
        <label class="form-label">Set nummer *</label>
        <input type="text" name="set_number" class="form-control" value="{{ old('set_number', $legoSet->set_number ?? '') }}" required placeholder="bijv. 75192">
        @error('set_number')<div class="form-error">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
        <label class="form-label">Naam *</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $legoSet->name ?? '') }}" required>
        @error('name')<div class="form-error">{{ $message }}</div>@enderror
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label">Thema</label>
        <input type="text" name="theme" class="form-control" value="{{ old('theme', $legoSet->theme ?? '') }}" placeholder="bijv. Star Wars, Technic">
    </div>
    <div class="form-group">
        <label class="form-label">Subthema</label>
        <input type="text" name="subtheme" class="form-control" value="{{ old('subtheme', $legoSet->subtheme ?? '') }}" placeholder="bijv. Ultimate Collector Series">
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label">Aantal steentjes</label>
        <input type="number" name="piece_count" class="form-control" min="0" value="{{ old('piece_count', $legoSet->piece_count ?? '') }}">
    </div>
    <div class="form-group">
        <label class="form-label">Aantal minifiguren</label>
        <input type="number" name="minifigure_count" class="form-control" min="0" value="{{ old('minifigure_count', $legoSet->minifigure_count ?? '') }}">
    </div>
</div>

<div class="form-group">
    <label class="form-label">Afbeelding URL</label>
    <input type="url" name="image_url" class="form-control" value="{{ old('image_url', $legoSet->image_url ?? '') }}" placeholder="https://...">
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label">Jaar van uitgave</label>
        <input type="number" name="release_year" class="form-control" min="1949" max="2030" value="{{ old('release_year', $legoSet->release_year ?? '') }}">
    </div>
    <div class="form-group">
        <label class="form-label">Adviesprijs (&euro;)</label>
        <input type="number" name="retail_price" class="form-control" step="0.01" min="0" value="{{ old('retail_price', $legoSet->retail_price ?? '') }}">
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label">Aankoopprijs (&euro;)</label>
        <input type="number" name="purchase_price" class="form-control" step="0.01" min="0" value="{{ old('purchase_price', $legoSet->purchase_price ?? '') }}">
    </div>
    <div class="form-group">
        <label class="form-label">Aankoopdatum</label>
        <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date', isset($legoSet) && $legoSet->purchase_date ? $legoSet->purchase_date->format('Y-m-d') : '') }}">
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label">Conditie</label>
        <select name="condition" class="form-control">
            <option value="">Selecteer...</option>
            @foreach(['new_sealed' => 'Nieuw (verzegeld)', 'new_open' => 'Nieuw (geopend)', 'built' => 'Gebouwd', 'used' => 'Gebruikt'] as $val => $label)
                <option value="{{ $val }}" {{ old('condition', $legoSet->condition ?? '') == $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label class="form-label">Status *</label>
        <select name="status" class="form-control">
            <option value="collection" {{ old('status', $legoSet->status ?? 'collection') == 'collection' ? 'selected' : '' }}>Collectie</option>
            <option value="wishlist" {{ old('status', $legoSet->status ?? '') == 'wishlist' ? 'selected' : '' }}>Wishlist</option>
        </select>
    </div>
</div>

<div class="form-group">
    <label class="form-label">Bouwstatus *</label>
    <select name="build_status" class="form-control">
        <option value="not_built" {{ old('build_status', $legoSet->build_status ?? 'not_built') == 'not_built' ? 'selected' : '' }}>Niet gebouwd</option>
        <option value="in_progress" {{ old('build_status', $legoSet->build_status ?? '') == 'in_progress' ? 'selected' : '' }}>Bezig</option>
        <option value="built" {{ old('build_status', $legoSet->build_status ?? '') == 'built' ? 'selected' : '' }}>Gebouwd</option>
        <option value="displayed" {{ old('build_status', $legoSet->build_status ?? '') == 'displayed' ? 'selected' : '' }}>Tentoongesteld</option>
    </select>
</div>

<div class="form-group">
    <label class="form-label">Notities</label>
    <textarea name="notes" class="form-control">{{ old('notes', $legoSet->notes ?? '') }}</textarea>
</div>
