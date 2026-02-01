@extends('layouts.app')
@section('title', 'Import / Export - GameVault')

@section('content')
<div class="toolbar">
    <h1>Import / Export</h1>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;max-width:1000px;">
    <!-- Export -->
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:12px;padding:1.25rem;">
        <h3 style="margin-bottom:1rem;">Export</h3>
        <div style="display:flex;flex-direction:column;gap:0.75rem;">
            <a href="{{ route('export.games.csv') }}" class="btn btn-secondary">Games CSV downloaden</a>
            <a href="{{ route('export.lego.csv') }}" class="btn btn-secondary">LEGO CSV downloaden</a>
            <a href="{{ route('export.json') }}" class="btn btn-primary">Volledige backup (JSON)</a>
        </div>
    </div>

    <!-- Import -->
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:12px;padding:1.25rem;">
        <h3 style="margin-bottom:1rem;">Import</h3>
        <form method="POST" action="{{ route('import.json') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">JSON backup bestand</label>
                <input type="file" name="file" accept=".json,.txt" class="form-control" required>
            </div>
            <p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:1rem;">
                Duplicaten worden automatisch overgeslagen (games: naam+platform+format, LEGO: set nummer).
            </p>
            <button type="submit" class="btn btn-primary">Importeren</button>
        </form>
    </div>
</div>
@endsection
