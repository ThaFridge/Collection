@extends('layouts.app')
@section('title', 'Dashboard - GameVault')

@section('content')
<div class="toolbar">
    <h1>Dashboard</h1>
</div>

<!-- Game Statistics -->
<h2 style="margin-bottom:1rem;">Games</h2>
<div class="stats-bar">
    <div class="stat-item">
        <div class="stat-value">{{ $gameStats['total'] }}</div>
        <div class="stat-label">In collectie</div>
    </div>
    <div class="stat-item">
        <div class="stat-value">{{ $gameStats['wishlist'] }}</div>
        <div class="stat-label">Op wishlist</div>
    </div>
    <div class="stat-item">
        <div class="stat-value">&euro;{{ number_format($gameStats['total_value'], 2, ',', '.') }}</div>
        <div class="stat-label">Totale waarde</div>
    </div>
    <div class="stat-item">
        <div class="stat-value">{{ $gameStats['physical'] }}</div>
        <div class="stat-label">Fysiek</div>
    </div>
    <div class="stat-item">
        <div class="stat-value">{{ $gameStats['digital'] }}</div>
        <div class="stat-label">Digitaal</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:2rem;">
    <!-- Games per platform -->
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:12px;padding:1.25rem;">
        <h3 style="margin-bottom:1rem;">Per platform</h3>
        @forelse($gamesByPlatform as $item)
        <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.5rem;">
            <div style="width:100px;font-size:0.85rem;">{{ $item->platform }}</div>
            <div style="flex:1;height:24px;background:var(--bg-input);border-radius:4px;overflow:hidden;">
                <div style="height:100%;background:var(--accent);border-radius:4px;width:{{ $gameStats['total'] > 0 ? round($item->count / $gameStats['total'] * 100) : 0 }}%;display:flex;align-items:center;padding-left:6px;">
                    <span style="font-size:0.75rem;color:#fff;font-weight:600;">{{ $item->count }}</span>
                </div>
            </div>
        </div>
        @empty
        <p style="color:var(--text-muted);font-size:0.85rem;">Nog geen games</p>
        @endforelse
    </div>

    <!-- Completion status -->
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:12px;padding:1.25rem;">
        <h3 style="margin-bottom:1rem;">Voortgang</h3>
        @php
            $completionLabels = ['not_played' => 'Niet gespeeld', 'playing' => 'Bezig', 'completed' => 'Uitgespeeld', 'platinum' => 'Platinum'];
            $completionColors = ['not_played' => 'var(--text-muted)', 'playing' => 'var(--warning)', 'completed' => 'var(--success)', 'platinum' => '#3498db'];
        @endphp
        @foreach($completionLabels as $key => $label)
        @php $count = $gamesByCompletion[$key] ?? 0; @endphp
        <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.5rem;">
            <div style="width:110px;font-size:0.85rem;">{{ $label }}</div>
            <div style="flex:1;height:24px;background:var(--bg-input);border-radius:4px;overflow:hidden;">
                <div style="height:100%;background:{{ $completionColors[$key] }};border-radius:4px;width:{{ $gameStats['total'] > 0 ? round($count / $gameStats['total'] * 100) : 0 }}%;display:flex;align-items:center;padding-left:6px;">
                    <span style="font-size:0.75rem;color:#fff;font-weight:600;">{{ $count }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Top genres -->
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:12px;padding:1.25rem;">
        <h3 style="margin-bottom:1rem;">Top genres</h3>
        @forelse($gamesByGenre as $item)
        <div style="display:flex;justify-content:space-between;padding:0.3rem 0;font-size:0.85rem;border-bottom:1px solid var(--border);">
            <span>{{ $item->genre }}</span>
            <span style="color:var(--accent);font-weight:600;">{{ $item->count }}</span>
        </div>
        @empty
        <p style="color:var(--text-muted);font-size:0.85rem;">Nog geen genres</p>
        @endforelse
    </div>
</div>

<!-- LEGO Statistics -->
<h2 style="margin-bottom:1rem;">LEGO</h2>
<div class="stats-bar">
    <div class="stat-item">
        <div class="stat-value">{{ $legoStats['total'] }}</div>
        <div class="stat-label">In collectie</div>
    </div>
    <div class="stat-item">
        <div class="stat-value">{{ $legoStats['wishlist'] }}</div>
        <div class="stat-label">Op wishlist</div>
    </div>
    <div class="stat-item">
        <div class="stat-value">&euro;{{ number_format($legoStats['total_value'], 2, ',', '.') }}</div>
        <div class="stat-label">Aankoopwaarde</div>
    </div>
    <div class="stat-item">
        <div class="stat-value">{{ number_format($legoStats['total_pieces']) }}</div>
        <div class="stat-label">Steentjes</div>
    </div>
    <div class="stat-item">
        <div class="stat-value">{{ $legoStats['total_minifigs'] }}</div>
        <div class="stat-label">Minifiguren</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:2rem;">
    <!-- LEGO per theme -->
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:12px;padding:1.25rem;">
        <h3 style="margin-bottom:1rem;">Per thema</h3>
        @forelse($legoByTheme as $item)
        <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.5rem;">
            <div style="width:120px;font-size:0.85rem;">{{ $item->theme }}</div>
            <div style="flex:1;height:24px;background:var(--bg-input);border-radius:4px;overflow:hidden;">
                <div style="height:100%;background:#9b59b6;border-radius:4px;width:{{ $legoStats['total'] > 0 ? round($item->count / $legoStats['total'] * 100) : 0 }}%;display:flex;align-items:center;padding-left:6px;">
                    <span style="font-size:0.75rem;color:#fff;font-weight:600;">{{ $item->count }}</span>
                </div>
            </div>
            <span style="font-size:0.75rem;color:var(--text-muted);min-width:60px;">{{ number_format($item->pieces) }} pcs</span>
        </div>
        @empty
        <p style="color:var(--text-muted);font-size:0.85rem;">Nog geen LEGO sets</p>
        @endforelse
    </div>

    <!-- Build status -->
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:12px;padding:1.25rem;">
        <h3 style="margin-bottom:1rem;">Bouwstatus</h3>
        @php
            $buildLabels = ['not_built' => 'Niet gebouwd', 'in_progress' => 'Bezig', 'built' => 'Gebouwd', 'displayed' => 'Tentoongesteld'];
            $buildColors = ['not_built' => 'var(--text-muted)', 'in_progress' => 'var(--warning)', 'built' => 'var(--success)', 'displayed' => '#3498db'];
        @endphp
        @foreach($buildLabels as $key => $label)
        @php $count = $legoByBuildStatus[$key] ?? 0; @endphp
        <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.5rem;">
            <div style="width:120px;font-size:0.85rem;">{{ $label }}</div>
            <div style="flex:1;height:24px;background:var(--bg-input);border-radius:4px;overflow:hidden;">
                <div style="height:100%;background:{{ $buildColors[$key] }};border-radius:4px;width:{{ $legoStats['total'] > 0 ? round($count / $legoStats['total'] * 100) : 0 }}%;display:flex;align-items:center;padding-left:6px;">
                    <span style="font-size:0.75rem;color:#fff;font-weight:600;">{{ $count }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
