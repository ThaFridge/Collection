// Theme toggle - persist in localStorage
(function() {
    const saved = localStorage.getItem('gamevault-theme') || 'dark';
    if (saved === 'light') document.documentElement.setAttribute('data-theme', 'light');

    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.querySelector('.theme-toggle');
        if (!btn) return;

        function update() {
            const current = document.documentElement.getAttribute('data-theme');
            btn.textContent = current === 'light' ? '\u263E' : '\u2600';
            btn.title = current === 'light' ? 'Naar dark mode' : 'Naar light mode';
        }
        update();

        btn.addEventListener('click', function() {
            const current = document.documentElement.getAttribute('data-theme');
            const next = current === 'light' ? 'dark' : 'light';
            if (next === 'dark') {
                document.documentElement.removeAttribute('data-theme');
            } else {
                document.documentElement.setAttribute('data-theme', 'light');
            }
            localStorage.setItem('gamevault-theme', next);
            update();
        });
    });
})();
