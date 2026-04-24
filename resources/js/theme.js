const THEME_KEY = 'alordisha-theme';

const resolveSystemTheme = () =>
    window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';

const getStoredTheme = () => {
    const savedTheme = localStorage.getItem(THEME_KEY);
    return savedTheme === 'dark' || savedTheme === 'light' ? savedTheme : null;
};

const applyTheme = (theme) => {
    document.documentElement.setAttribute('data-theme', theme);
    document.documentElement.style.colorScheme = theme;
};

const initTheme = () => {
    const initialTheme = getStoredTheme() ?? resolveSystemTheme();
    applyTheme(initialTheme);

    document.querySelectorAll('[data-theme-toggle]').forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
            const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';

            localStorage.setItem(THEME_KEY, nextTheme);
            applyTheme(nextTheme);
        });
    });

    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    mediaQuery.addEventListener('change', () => {
        if (!getStoredTheme()) {
            applyTheme(resolveSystemTheme());
        }
    });
};

const initPwa = () => {
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker
                .register('/service-worker.js')
                .catch(() => navigator.serviceWorker.register('/sw.js').catch(() => {}));
        });
    }
};

window.appUiState = () => ({
    fabOpen: false,
    moreOpen: false,
    toggleFab() {
        this.moreOpen = false;
        this.fabOpen = !this.fabOpen;
    },
    closeFab() {
        this.fabOpen = false;
    },
    toggleMore() {
        this.fabOpen = false;
        this.moreOpen = !this.moreOpen;
    },
    closeMore() {
        this.moreOpen = false;
    },
    closeOverlays() {
        this.fabOpen = false;
        this.moreOpen = false;
    },
});

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initPwa();
});
