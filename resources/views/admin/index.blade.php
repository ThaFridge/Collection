@extends('layouts.app')
@section('title', 'Admin Panel - GameVault')

@section('content')
<div class="toolbar">
    <h1>Admin Panel</h1>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;max-width:1000px;">
    <div style="grid-column:1/-1;">
        <h2 style="margin-bottom:1rem;">API Providers</h2>
    </div>

    @foreach($providers as $provider)
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:12px;padding:1.25rem;">
        <form method="POST" action="{{ route('admin.providers.update', $provider) }}">
            @csrf @method('PATCH')
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
                <h3>{{ $provider->name }}</h3>
                <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ $provider->is_active ? 'checked' : '' }}
                           style="width:18px;height:18px;accent-color:var(--accent);">
                    <span style="font-size:0.85rem;">Actief</span>
                </label>
            </div>

            <div class="form-group">
                <label class="form-label">Prioriteit</label>
                <input type="number" name="priority" class="form-control" value="{{ $provider->priority }}" min="0">
            </div>

            <div class="form-group">
                <label class="form-label">API Key</label>
                <input type="password" name="api_key" class="form-control" placeholder="{{ is_array($provider->credentials_json) && !empty($provider->credentials_json['api_key']) ? '••••••••' : 'Nog niet ingesteld' }}">
            </div>

            @if(in_array($provider->slug, ['igdb']))
            <div class="form-group">
                <label class="form-label">Client ID</label>
                <input type="text" name="client_id" class="form-control" value="{{ is_array($provider->credentials_json) ? ($provider->credentials_json['client_id'] ?? '') : '' }}" placeholder="Twitch Client ID">
            </div>
            <div class="form-group">
                <label class="form-label">Client Secret</label>
                <input type="password" name="client_secret" class="form-control" placeholder="{{ is_array($provider->credentials_json) && !empty($provider->credentials_json['client_secret']) ? '••••••••' : 'Twitch Client Secret' }}">
            </div>
            @endif

            <div style="display:flex;gap:0.5rem;align-items:center;">
                <button type="submit" class="btn btn-primary btn-sm">Opslaan</button>
            </div>
        </form>

        @if($provider->credentials_json)
        <div style="margin-top:0.75rem;padding-top:0.75rem;border-top:1px solid var(--border);">
            <form method="POST" action="{{ route('admin.providers.test', $provider) }}">
                @csrf
                <div style="display:flex;gap:0.5rem;align-items:center;">
                    <button type="submit" class="btn btn-secondary btn-sm">Test verbinding</button>
                    @if(session('test_result_' . $provider->slug))
                        @php $result = session('test_result_' . $provider->slug); @endphp
                        <span style="font-size:0.8rem;font-weight:600;color:{{ $result['success'] ? 'var(--success)' : 'var(--accent)' }};">
                            {{ $result['message'] }}
                        </span>
                    @endif
                </div>
            </form>
        </div>
        @endif
    </div>
    @endforeach
</div>

<div style="margin-top:2rem;max-width:1000px;">
    <h2 style="margin-bottom:1rem;">Database Info</h2>
    <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:12px;padding:1.25rem;">
        <dl class="detail-meta">
            <dt>Driver</dt><dd>{{ $dbInfo['driver'] }}</dd>
            <dt>Database</dt><dd>{{ $dbInfo['database'] }}</dd>
        </dl>
    </div>
</div>
@endsection
