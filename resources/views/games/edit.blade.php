@extends('layouts.app')
@section('title', $game->name . ' bewerken - GameVault')

@section('content')
<div class="toolbar">
    <h1>{{ $game->name }} bewerken</h1>
    <a href="{{ route('games.show', $game) }}" class="btn btn-secondary">Terug</a>
</div>

<div style="max-width:800px;">
    <form method="POST" action="{{ route('games.update', $game) }}">
        @csrf @method('PUT')
        @include("games._search")
        @include('games._form', ['game' => $game])
        <div style="margin-top:1.5rem;">
            <button type="submit" class="btn btn-primary">Bijwerken</button>
        </div>
    </form>
</div>
@endsection
