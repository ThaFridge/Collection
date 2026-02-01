@extends('layouts.app')
@section('title', $legoSet->name . ' bewerken - GameVault')

@section('content')
<div class="toolbar">
    <h1>{{ $legoSet->name }} bewerken</h1>
    <a href="{{ route('lego.show', $legoSet) }}" class="btn btn-secondary">Terug</a>
</div>

<div style="max-width:800px;">
    <form method="POST" action="{{ route('lego.update', $legoSet) }}">
        @csrf @method('PUT')
        @include('lego._form', ['legoSet' => $legoSet])
        <div style="margin-top:1.5rem;">
            <button type="submit" class="btn btn-primary">Bijwerken</button>
        </div>
    </form>
</div>
@endsection
