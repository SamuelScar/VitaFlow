<script>
    (() => {
        const storageKey = 'vitaflow-theme';
        const allowedThemes = ['light', 'dark', 'system'];
        let storedTheme = null;

        try {
            storedTheme = localStorage.getItem(storageKey);
        } catch {
            storedTheme = null;
        }

        const preference = allowedThemes.includes(storedTheme) ? storedTheme : 'system';
        const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        const resolvedTheme = preference === 'system' ? systemTheme : preference;

        document.documentElement.dataset.themePreference = preference;
        document.documentElement.dataset.bsTheme = resolvedTheme;
    })();
</script>
