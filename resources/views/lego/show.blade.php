@extends('layouts.app')
@section('title', $legoSet->name . ' - GameVault')

@section('content')
<div class="detail-header">
    <div class="detail-cover">
        @if($legoSet->image_path)
            <img src="{{ asset('storage/' . $legoSet->image_path) }}" alt="{{ $legoSet->name }}">
        @else
            <div class="game-card-cover-placeholder" style="height:300px;border-radius:12px;">&#129521;</div>
        @endif
    </div>
    <div class="detail-info">
        <h1 class="detail-title">{{ $legoSet->name }}</h1>
        <div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-bottom:0.5rem;">
            <span style="font-weight:700;font-size:1.1rem;color:var(--accent);">#{{ $legoSet->set_number }}</span>
            @if($legoSet->theme)<span class="badge badge-theme">{{ $legoSet->theme }}</span>@endif
            @if($legoSet->subtheme)<span class="badge badge-format">{{ $legoSet->subtheme }}</span>@endif
        </div>

        <div class="build-tracker">
            @php
                $steps = ['not_built' => 'Niet gebouwd', 'in_progress' => 'Bezig', 'built' => 'Gebouwd', 'displayed' => 'Tentoongesteld'];
                $reached = false;
                $currentFound = false;
            @endphp
            @foreach($steps as $key => $label)
                @php
                    if ($key === $legoSet->build_status) $currentFound = true;
                    $class = $currentFound && $key !== $legoSet->build_status ? '' : ($key === $legoSet->build_status ? 'active' : 'done');
                    if (!$currentFound && $key !== $legoSet->build_status) $class = 'done';
                @endphp
                <form method="POST" action="{{ route('lego.build-status', $legoSet) }}" style="flex:1;">
                    @csrf @method('PATCH')
                    <input type="hidden" name="build_status" value="{{ $key }}">
                    <button type="submit" class="build-step {{ $class }}" style="width:100%;cursor:pointer;">{{ $label }}</button>
                </form>
            @endforeach
        </div>

        <dl class="detail-meta">
            @if($legoSet->piece_count)<dt>Steentjes</dt><dd>{{ number_format($legoSet->piece_count) }}</dd>@endif
            @if($legoSet->minifigure_count)<dt>Minifiguren</dt><dd>{{ $legoSet->minifigure_count }}</dd>@endif
            @if($legoSet->release_year)<dt>Jaar</dt><dd>{{ $legoSet->release_year }}</dd>@endif
            @if($legoSet->retail_price)<dt>Adviesprijs</dt><dd>&euro;{{ number_format($legoSet->retail_price, 2, ',', '.') }}</dd>@endif
            @if($legoSet->purchase_price)<dt>Aankoopprijs</dt><dd>&euro;{{ number_format($legoSet->purchase_price, 2, ',', '.') }}</dd>@endif
            @if($legoSet->purchase_date)<dt>Aankoopdatum</dt><dd>{{ $legoSet->purchase_date->format('d-m-Y') }}</dd>@endif
            @if($legoSet->condition)<dt>Conditie</dt><dd>{{ $legoSet->condition }}</dd>@endif
        </dl>

        @if($legoSet->notes)
            <div style="margin-top:1rem;padding:1rem;background:var(--bg-input);border-radius:8px;">
                <strong>Notities:</strong><br>{{ $legoSet->notes }}
            </div>
        @endif

        <div style="margin-top:1rem;display:flex;gap:0.5rem;flex-wrap:wrap;">
            @if($legoSet->instructions_url)
                <a href="{{ $legoSet->instructions_url }}" target="_blank" class="btn btn-secondary">Bouwhandleiding</a>
            @endif
            @if($legoSet->bricklink_url)
                <a href="{{ $legoSet->bricklink_url }}" target="_blank" class="btn btn-secondary">BrickLink</a>
            @endif
        </div>

        <div class="detail-actions">
            <a href="{{ route('lego.edit', $legoSet) }}" class="btn btn-secondary">Bewerken</a>
            <form method="POST" action="{{ route('lego.toggle-status', $legoSet) }}" style="display:inline;">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-secondary">
                    {{ $legoSet->status === 'collection' ? 'Naar Wishlist' : 'Naar Collectie' }}
                </button>
            </form>
            <form method="POST" action="{{ route('lego.destroy', $legoSet) }}" style="display:inline;" onsubmit="return confirm('Weet je het zeker?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">Verwijderen</button>
            </form>
        </div>
    </div>
</div>
@endsection
