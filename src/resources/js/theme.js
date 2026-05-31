const themeStorageKey = 'vitaflow-theme';
const themeOptions = ['system', 'light', 'dark'];
const themeLabels = {
    system: 'Sistema',
    light: 'Claro',
    dark: 'Escuro',
};
const themeIcons = {
    system: 'bi-circle-half',
    light: 'bi-sun',
    dark: 'bi-moon-stars',
};
const systemThemeQuery = window.matchMedia('(prefers-color-scheme: dark)');

const isValidTheme = (theme) => themeOptions.includes(theme);

const getStoredTheme = () => {
    try {
        const storedTheme = localStorage.getItem(themeStorageKey);

        return isValidTheme(storedTheme) ? storedTheme : 'system';
    } catch {
        return 'system';
    }
};

const storeTheme = (theme) => {
    try {
        localStorage.setItem(themeStorageKey, theme);
    } catch {
        // localStorage can be unavailable in restrictive browser settings.
    }
};

const resolveTheme = (theme) => {
    if (theme === 'system') {
        return systemThemeQuery.matches ? 'dark' : 'light';
    }

    return theme;
};

const updateThemeControls = (theme) => {
    document.querySelectorAll('[data-theme-value]').forEach((button) => {
        const active = button.dataset.themeValue === theme;

        button.classList.toggle('active', active);
        button.setAttribute('aria-pressed', active ? 'true' : 'false');
        button.querySelector('[data-theme-check]')?.classList.toggle('d-none', !active);
    });

    document.querySelectorAll('[data-theme-toggle-label]').forEach((label) => {
        label.textContent = themeLabels[theme] || themeLabels.system;
    });

    document.querySelectorAll('[data-theme-toggle-icon]').forEach((icon) => {
        icon.className = `bi ${themeIcons[theme] || themeIcons.system}`;
    });

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        const label = `Tema: ${themeLabels[theme] || themeLabels.system}`;

        button.setAttribute('aria-label', label);
        button.setAttribute('title', label);
    });
};

const applyTheme = (theme, persist = false) => {
    const preference = isValidTheme(theme) ? theme : 'system';
    const resolvedTheme = resolveTheme(preference);

    document.documentElement.dataset.themePreference = preference;
    document.documentElement.dataset.bsTheme = resolvedTheme;
    document.documentElement.style.colorScheme = resolvedTheme;

    if (persist) {
        storeTheme(preference);
    }

    updateThemeControls(preference);
};

const refreshSystemTheme = () => {
    if (document.documentElement.dataset.themePreference === 'system') {
        applyTheme('system');
    }
};

const bindThemeControls = () => {
    document.querySelectorAll('[data-theme-value]').forEach((button) => {
        button.addEventListener('click', () => {
            applyTheme(button.dataset.themeValue, true);
        });
    });
};

const watchSystemTheme = () => {
    if (systemThemeQuery.addEventListener) {
        systemThemeQuery.addEventListener('change', refreshSystemTheme);
        return;
    }

    systemThemeQuery.addListener(refreshSystemTheme);
};

const initTheme = () => {
    applyTheme(getStoredTheme());
    bindThemeControls();
    watchSystemTheme();
};

export { initTheme };
