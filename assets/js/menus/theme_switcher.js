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
    const theme = localStorage.getItem('theme') || 'dark';
    localStorage.setItem('theme', theme);
    document.documentElement.setAttribute('data-theme', theme);

    fetchData(`/api/me/preferences/theme?value=${theme}`, 'PATCH')
        .catch(error => {
            console.error("Failed to update theme:", error);
        });
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
