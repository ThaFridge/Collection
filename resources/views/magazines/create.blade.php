@extends('layouts.app')
@section('title', 'Magazine/Manual uploaden - GameVault')

@section('content')
<div class="toolbar">
    <h1>Magazine / Manual uploaden</h1>
    <a href="{{ route('magazines.index') }}" class="btn btn-secondary">Terug</a>
</div>

<div style="max-width:600px;">
    @if($errors->any())
        <div class="flash flash-error" style="margin-bottom:1rem;">
            <ul style="margin:0;padding-left:1.2rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('magazines.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label class="form-label">Type *</label>
            <select name="type" class="form-control" required>
                <option value="magazine" {{ old('type', 'magazine') == 'magazine' ? 'selected' : '' }}>Magazine</option>
                <option value="manual" {{ old('type') == 'manual' ? 'selected' : '' }}>Manual</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Titel *</label>
            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required placeholder="bijv. Power Unlimited">
            @error('title')<div class="form-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Uitgever</label>
                <input type="text" name="publisher" class="form-control" value="{{ old('publisher') }}" placeholder="bijv. Future Publishing">
            </div>
            <div class="form-group">
                <label class="form-label">Nummer</label>
                <input type="text" name="issue_number" class="form-control" value="{{ old('issue_number') }}" placeholder="bijv. 42">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Jaar *</label>
                <input type="number" name="year" class="form-control" value="{{ old('year', date('Y')) }}" required min="1970" max="2099">
                @error('year')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Publicatiedatum</label>
                <input type="date" name="publication_date" class="form-control" value="{{ old('publication_date') }}">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">PDF bestand *</label>
            <input type="file" name="pdf" class="form-control" accept="application/pdf" required>
            @error('pdf')<div class="form-error">{{ $message }}</div>@enderror
            <div style="font-size:0.75rem;color:var(--text-muted);margin-top:0.25rem;">Max 200 MB</div>
        </div>
        <div class="form-group">
            <label class="form-label">Cover afbeelding (optioneel)</label>
            <input type="file" name="cover" class="form-control" accept="image/*">
            <div style="font-size:0.75rem;color:var(--text-muted);margin-top:0.25rem;">Wordt automatisch uit PDF gehaald als je geen cover uploadt</div>
        </div>
        <div class="form-group">
            <label class="form-label">Notities</label>
            <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
        </div>
        <div style="margin-top:1.5rem;">
            <button type="submit" class="btn btn-primary">Uploaden</button>
        </div>
    </form>
</div>
@endsection
