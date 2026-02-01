<div class="form-group" style="position:relative;">
    <label class="form-label">Zoek game via API</label>
    <div style="position:relative;">
        <input type="text" id="api-search" class="form-control" placeholder="Zoek op naam...">
        <span id="search-spinner" style="display:none;position:absolute;right:10px;top:50%;transform:translateY(-50%);"><span class="spinner"></span></span>
    </div>
    <div id="search-results" style="display:none;position:absolute;top:100%;left:0;right:0;z-index:50;background:var(--bg-card);border:1px solid var(--border);border-radius:0 0 8px 8px;max-height:400px;overflow-y:auto;"></div>
</div>

@push('scripts')
<script>
(function() {
    var searchInput = document.getElementById('api-search');
    var resultsDiv = document.getElementById('search-results');
    var debounceTimer;

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        var q = this.value.trim();
        if (q.length < 2) { resultsDiv.style.display = 'none'; return; }

        debounceTimer = setTimeout(function() {
            document.getElementById('search-spinner').style.display = 'inline';
            var platform = document.getElementById('game-platform') ? document.getElementById('game-platform').value : '';
            fetch('/api/games/search?q=' + encodeURIComponent(q) + '&platform=' + encodeURIComponent(platform))
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    document.getElementById('search-spinner').style.display = 'none';
                    if (!data.length) {
                        resultsDiv.innerHTML = '<div style="padding:1rem;color:var(--text-muted);">Geen resultaten gevonden</div>';
                        resultsDiv.style.display = 'block';
                        return;
                    }

                    resultsDiv.innerHTML = '';
                    data.forEach(function(g) {
                        var div = document.createElement('div');
                        div.style.cssText = 'display:flex;gap:0.75rem;padding:0.75rem;cursor:pointer;border-bottom:1px solid var(--border);';
                        div.onmouseover = function() { this.style.background = 'var(--bg-input)'; };
                        div.onmouseout = function() { this.style.background = 'transparent'; };

                        var coverHtml = g.cover_url
                            ? '<img src="' + g.cover_url + '" style="width:50px;height:67px;object-fit:cover;border-radius:4px;">'
                            : '<div style="width:50px;height:67px;background:var(--bg-input);border-radius:4px;display:flex;align-items:center;justify-content:center;">🎮</div>';

                        div.innerHTML = coverHtml + '<div><div style="font-weight:600;font-size:0.9rem;">' + g.name + '</div><div style="font-size:0.75rem;color:var(--text-muted);">' + (g.release_date || '') + (g.genre ? ' • ' + g.genre : '') + '</div></div>';

                        div.addEventListener('click', function() {
                            fillForm(g);
                            resultsDiv.style.display = 'none';
                            searchInput.value = '';
                        });
                        resultsDiv.appendChild(div);
                    });
                    resultsDiv.style.display = 'block';
                })
                .catch(function(e) { document.getElementById('search-spinner').style.display = 'none'; console.error('Search error:', e); });
        }, 300);
    });

    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
            resultsDiv.style.display = 'none';
        }
    });

    function fillForm(g) {
        var fields = {
            'name': g.name,
            'genre': g.genre,
            'developer': g.developer,
            'publisher': g.publisher,
            'description': g.description,
            'release_date': g.release_date,
            'cover_image_url': g.cover_url,
            'external_api_id': g.external_id,
            'external_api_source': g.source
        };
        for (var name in fields) {
            if (!fields[name]) continue;
            var el = document.querySelector('[name="' + name + '"]');
            if (el) el.value = fields[name];
        }
    }
})();
</script>
@endpush
