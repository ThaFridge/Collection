@extends('layouts.app')
@section('title', $magazine->title . ' bewerken - GameVault')

@section('content')
<div class="toolbar">
    <h1>{{ $magazine->title }} bewerken</h1>
    <a href="{{ route('magazines.show', $magazine) }}" class="btn btn-secondary">Terug</a>
</div>

<div style="max-width:600px;">
    <form method="POST" action="{{ route('magazines.update', $magazine) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="form-group">
            <label class="form-label">Type *</label>
            <select name="type" class="form-control" required>
                <option value="magazine" {{ old('type', $magazine->type) == 'magazine' ? 'selected' : '' }}>Magazine</option>
                <option value="manual" {{ old('type', $magazine->type) == 'manual' ? 'selected' : '' }}>Manual</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Titel *</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $magazine->title) }}" required>
            @error('title')<div class="form-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Uitgever</label>
                <input type="text" name="publisher" class="form-control" value="{{ old('publisher', $magazine->publisher) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Nummer</label>
                <input type="text" name="issue_number" class="form-control" value="{{ old('issue_number', $magazine->issue_number) }}">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Jaar *</label>
                <input type="number" name="year" class="form-control" value="{{ old('year', $magazine->year) }}" required min="1970" max="2099">
                @error('year')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Publicatiedatum</label>
                <input type="date" name="publication_date" class="form-control" value="{{ old('publication_date', $magazine->publication_date?->format('Y-m-d')) }}">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Nieuwe cover (optioneel)</label>
            <input type="file" name="cover" class="form-control" accept="image/*">
            @if($magazine->cover_image_path)
                <div style="font-size:0.75rem;color:var(--text-muted);margin-top:0.25rem;">Huidige cover wordt vervangen bij upload</div>
            @endif
        </div>
        <div class="form-group">
            <label class="form-label">Notities</label>
            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $magazine->notes) }}</textarea>
        </div>
        <div style="margin-top:1.5rem;">
            <button type="submit" class="btn btn-primary">Bijwerken</button>
        </div>
    </form>
</div>
@endsection
