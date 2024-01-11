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

function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}

function updateTheme() {
    if (!('theme' in localStorage)) {
        localStorage.theme = 'dark';
    }
    const token = getCookie('thegate');
    const theme = localStorage.theme;
    document.documentElement.setAttribute('color-theme', theme);
    const url = '/api/user/preferences/theme';
    const data = { theme: theme };
    fetchData('/api/user/preferences/theme', 'PUT', data)
}

switch (localStorage.theme) {
    case 'dark':
        document.documentElement.classList.add('dark');
        document.documentElement.setAttribute('color-theme', 'dark');
        break;
    case 'light':
        document.documentElement.classList.remove('dark');
        document.documentElement.setAttribute('color-theme', 'light');
        break;
}

updateTheme();
