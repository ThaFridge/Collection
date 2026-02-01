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
        @include('games._form', ['game' => null])
        <div style="margin-top:1.5rem;">
            <button type="submit" class="btn btn-primary">Opslaan</button>
        </div>
    </form>
</div>
@endsection
