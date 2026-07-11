(() => {
    const storageKey = 'wachplaner-theme';
    const root = document.documentElement;
    const toggle = document.getElementById('theme-toggle');
    const icon = document.getElementById('theme-icon');

    const applyTheme = (theme) => {
        root.setAttribute('data-bs-theme', theme);
        if (icon) {
            icon.className = theme === 'dark' ? 'bi bi-sun' : 'bi bi-moon-stars';
        }
    };

    const stored = localStorage.getItem(storageKey);
    applyTheme(stored === 'dark' ? 'dark' : 'light');

    toggle?.addEventListener('click', () => {
        const next = root.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
        localStorage.setItem(storageKey, next);
        applyTheme(next);
    });
})();
