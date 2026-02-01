@extends('layouts.app')
@section('title', 'LEGO set toevoegen - GameVault')

@section('content')
<div class="toolbar">
    <h1>LEGO set toevoegen</h1>
    <a href="{{ route('lego.index') }}" class="btn btn-secondary">Terug</a>
</div>

<div style="max-width:800px;">
    <form method="POST" action="{{ route('lego.store') }}">
        @csrf
        @include('lego._form', ['legoSet' => null])
        <div style="margin-top:1.5rem;">
            <button type="submit" class="btn btn-primary">Opslaan</button>
        </div>
    </form>
</div>
@endsection
