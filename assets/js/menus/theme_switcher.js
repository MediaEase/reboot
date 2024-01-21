import { fetchData } from '../utils.js';

export const toDarkMode = () => {
    localStorage.theme = 'dark';
    updateTheme();
    window.location.reload();
}

export const toLightMode = () => {
    localStorage.theme = 'light';
    updateTheme();
    window.location.reload();
}

function updateTheme() {
    if (!('theme' in localStorage)) {
        localStorage.theme = 'dark';
    }
    const theme = localStorage.theme;
    document.documentElement.setAttribute('data-theme', theme);
    const data = { theme: theme };
    fetchData('/api/me/preferences/theme', 'PATCH', data)
}

switch (localStorage.theme) {
    case 'dark':
        document.documentElement.classList.add('dark');
        document.documentElement.setAttribute('data-theme', 'dark');
        break;
    case 'light':
        document.documentElement.classList.remove('dark');
        document.documentElement.setAttribute('data-theme', 'light');
        break;
}

updateTheme();
