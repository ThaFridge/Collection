@extends('layouts.app')
@section('title', 'GameVault - Collectie Beheer')

@section('content')
<div style="text-align:center;padding:3rem 1rem;">
    <h1 style="font-size:2.5rem;margin-bottom:0.5rem;">GameVault</h1>
    <p style="color:var(--text-muted);font-size:1.1rem;margin-bottom:3rem;">Beheer je games en LEGO collectie op één plek</p>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;max-width:800px;margin:0 auto;">
        <!-- Games -->
        <a href="{{ route('games.index') }}" style="text-decoration:none;color:var(--text);">
            <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:16px;padding:2rem;transition:transform 0.2s,box-shadow 0.2s;"
                 onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 25px var(--shadow)'"
                 onmouseout="this.style.transform='';this.style.boxShadow=''">
                <div style="font-size:4rem;margin-bottom:1rem;">🎮</div>
                <h2 style="margin-bottom:0.5rem;">Games</h2>
                <p style="color:var(--text-muted);font-size:0.9rem;">{{ $gameCount }} in collectie, {{ $gameWishlist }} op wishlist</p>
                <div style="margin-top:1rem;color:var(--accent);font-weight:600;">&euro;{{ number_format($gameValue, 2, ',', '.') }} waarde</div>
            </div>
        </a>

        <!-- LEGO -->
        <a href="{{ route('lego.index') }}" style="text-decoration:none;color:var(--text);">
            <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:16px;padding:2rem;transition:transform 0.2s,box-shadow 0.2s;"
                 onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 25px var(--shadow)'"
                 onmouseout="this.style.transform='';this.style.boxShadow=''">
                <div style="font-size:4rem;margin-bottom:1rem;">&#129521;</div>
                <h2 style="margin-bottom:0.5rem;">LEGO</h2>
                <p style="color:var(--text-muted);font-size:0.9rem;">{{ $legoCount }} sets, {{ number_format($legoPieces) }} steentjes</p>
                <div style="margin-top:1rem;color:var(--accent);font-weight:600;">&euro;{{ number_format($legoValue, 2, ',', '.') }} waarde</div>
            </div>
        </a>
    </div>

    <div style="display:flex;gap:1rem;justify-content:center;margin-top:2rem;">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Dashboard</a>
        <a href="{{ route('export.index') }}" class="btn btn-secondary">Import/Export</a>
        <a href="{{ route('admin.index') }}" class="btn btn-secondary">Admin</a>
    </div>
</div>
@endsection
