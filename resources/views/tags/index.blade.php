@extends('layouts.app')
@section('title', 'Tags - GameVault')

@section('content')
<div class="toolbar">
    <h1>Tags</h1>
</div>

<div style="max-width:600px;">
    <form method="POST" action="{{ route('tags.store') }}" style="display:flex;gap:0.5rem;margin-bottom:1.5rem;">
        @csrf
        <input type="text" name="name" class="form-control" placeholder="Nieuwe tag..." required style="flex:1;">
        <button type="submit" class="btn btn-primary">Toevoegen</button>
    </form>

    @forelse($tags as $tag)
    <div style="display:flex;justify-content:space-between;align-items:center;padding:0.75rem;background:var(--bg-card);border:1px solid var(--border);border-radius:8px;margin-bottom:0.5rem;">
        <div>
            <span style="font-weight:600;">{{ $tag->name }}</span>
            <span style="font-size:0.8rem;color:var(--text-muted);margin-left:0.5rem;">
                {{ $tag->games_count }} games, {{ $tag->lego_sets_count }} LEGO sets
            </span>
        </div>
        <form id="delete-tag-{{ $tag->id }}" method="POST" action="{{ route('tags.destroy', $tag) }}">
            @csrf @method('DELETE')
            <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(document.getElementById('delete-tag-{{ $tag->id }}'), 'Tag verwijderen?', '{{ $tag->name }} wordt verwijderd van alle items.')">Verwijder</button>
        </form>
    </div>
    @empty
    <p style="color:var(--text-muted);">Nog geen tags aangemaakt.</p>
    @endforelse
</div>
@endsection
