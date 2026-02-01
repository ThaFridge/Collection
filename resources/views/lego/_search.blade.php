<div class="form-group" style="position:relative;">
    <label class="form-label">Zoek LEGO set via API</label>
    <input type="text" id="lego-search" class="form-control" placeholder="Zoek op naam of set nummer...">
    <div id="lego-search-results" style="display:none;position:absolute;top:100%;left:0;right:0;z-index:50;background:var(--bg-card);border:1px solid var(--border);border-radius:0 0 8px 8px;max-height:400px;overflow-y:auto;"></div>
</div>

@push('scripts')
<script>
(function() {
    var searchInput = document.getElementById('lego-search');
    var resultsDiv = document.getElementById('lego-search-results');
    var debounceTimer;

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        var q = this.value.trim();
        if (q.length < 2) { resultsDiv.style.display = 'none'; return; }

        debounceTimer = setTimeout(function() {
            fetch('/api/lego/search?q=' + encodeURIComponent(q))
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (!data.length) {
                        resultsDiv.innerHTML = '<div style="padding:1rem;color:var(--text-muted);">Geen resultaten gevonden</div>';
                        resultsDiv.style.display = 'block';
                        return;
                    }

                    resultsDiv.innerHTML = '';
                    data.forEach(function(s) {
                        var div = document.createElement('div');
                        div.style.cssText = 'display:flex;gap:0.75rem;padding:0.75rem;cursor:pointer;border-bottom:1px solid var(--border);';
                        div.onmouseover = function() { this.style.background = 'var(--bg-input)'; };
                        div.onmouseout = function() { this.style.background = 'transparent'; };

                        var imgHtml = s.image_url
                            ? '<img src="' + s.image_url + '" style="width:60px;height:60px;object-fit:contain;border-radius:4px;background:white;">'
                            : '<div style="width:60px;height:60px;background:var(--bg-input);border-radius:4px;display:flex;align-items:center;justify-content:center;">&#129521;</div>';

                        div.innerHTML = imgHtml + '<div><div style="font-weight:600;font-size:0.9rem;">' + s.name + '</div><div style="font-size:0.75rem;color:var(--text-muted);">#' + (s.set_number || '') + (s.release_year ? ' \u2022 ' + s.release_year : '') + (s.piece_count ? ' \u2022 ' + s.piece_count + ' pcs' : '') + (s.theme ? ' \u2022 ' + s.theme : '') + '</div></div>';

                        div.addEventListener('click', function() {
                            fillLegoForm(s);
                            resultsDiv.style.display = 'none';
                            searchInput.value = '';
                        });
                        resultsDiv.appendChild(div);
                    });
                    resultsDiv.style.display = 'block';
                })
                .catch(function(e) { console.error('Search error:', e); });
        }, 300);
    });

    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
            resultsDiv.style.display = 'none';
        }
    });

    function fillLegoForm(s) {
        var fields = {
            'set_number': s.set_number,
            'name': s.name,
            'theme': s.theme,
            'subtheme': s.subtheme,
            'piece_count': s.piece_count,
            'minifigure_count': s.minifigure_count,
            'release_year': s.release_year,
            'image_url': s.image_url
        };
        for (var name in fields) {
            if (!fields[name] && fields[name] !== 0) continue;
            var el = document.querySelector('[name="' + name + '"]');
            if (el) el.value = fields[name];
        }
    }
})();
</script>
@endpush
