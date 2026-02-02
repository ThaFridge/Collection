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

        <h3 style="margin:1.5rem 0 0.75rem;">Game informatie</h3>
        @include('games._form', ['game' => null])

        <h3 style="margin:1.5rem 0 0.75rem;">Platform</h3>
        <div class="form-group">
            <label class="form-label">Platform *</label>
            <select name="platforms[]" class="form-control" required id="primary-platform">
                <option value="">Selecteer...</option>
                @foreach(config('platforms.game') as $p)
                    <option value="{{ $p }}" {{ old('platforms.0') == $p ? 'selected' : '' }}>{{ $p }}</option>
                @endforeach
            </select>
            @error('platforms')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div id="extra-platforms"></div>

        <button type="button" class="btn btn-secondary btn-sm" onclick="addPlatform()" style="margin-bottom:1.5rem;">+ Extra platform toevoegen</button>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Format *</label>
                <select name="format" class="form-control" required>
                    <option value="physical" {{ old('format', 'physical') == 'physical' ? 'selected' : '' }}>Fysiek</option>
                    <option value="digital" {{ old('format') == 'digital' ? 'selected' : '' }}>Digitaal</option>
                    <option value="both" {{ old('format') == 'both' ? 'selected' : '' }}>Beide</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Status *</label>
                <select name="status" class="form-control" required>
                    <option value="collection" {{ old('status', 'collection') == 'collection' ? 'selected' : '' }}>Collectie</option>
                    <option value="wishlist" {{ old('status') == 'wishlist' ? 'selected' : '' }}>Wishlist</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Voortgang *</label>
                <select name="completion_status" class="form-control" required>
                    <option value="not_played" {{ old('completion_status', 'not_played') == 'not_played' ? 'selected' : '' }}>Niet gespeeld</option>
                    <option value="playing" {{ old('completion_status') == 'playing' ? 'selected' : '' }}>Bezig</option>
                    <option value="completed" {{ old('completion_status') == 'completed' ? 'selected' : '' }}>Uitgespeeld</option>
                    <option value="platinum" {{ old('completion_status') == 'platinum' ? 'selected' : '' }}>Platinum</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Barcode (EAN/UPC)</label>
                <input type="text" name="barcode" class="form-control" value="{{ old('barcode') }}">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Aankoopprijs (&euro;)</label>
                <input type="number" name="purchase_price" class="form-control" step="0.01" min="0" value="{{ old('purchase_price') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Aankoopdatum</label>
                <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date') }}">
            </div>
        </div>

        <div style="margin-top:1.5rem;">
            <button type="submit" class="btn btn-primary">Opslaan</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
var platformOptions = document.getElementById('primary-platform').innerHTML;

function addPlatform() {
    var container = document.getElementById('extra-platforms');
    var div = document.createElement('div');
    div.style.cssText = 'display:flex;gap:0.5rem;align-items:center;margin-bottom:0.5rem;';
    div.innerHTML = '<select name="platforms[]" class="form-control" style="flex:1;">' + platformOptions + '</select>' +
        '<button type="button" class="btn btn-danger btn-sm" onclick="this.parentElement.remove()">X</button>';
    container.appendChild(div);
}
</script>
@endpush
@endsection
