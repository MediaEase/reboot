import { fetchData } from '../utils.js';
import { updateTable } from './table.js';
import { getAppStatus } from './metrics.js';

export function processDataForApps(data) {
    if (!data || typeof data.apps !== 'object') {
        console.error('Invalid app status data');
        return;
    }
    if (Array.isArray(data.apps)) {
        data.apps.forEach(app => {
            const appPanel = document.querySelector(`[data-app-panel="${app.name}"]`);
            updateAppPanel(appPanel, app);
        });
    } else {
        updateTable(data.apps);
    }
}

function updateAppPanel(appPanel, app) {
    if (!appPanel) {
        console.error(`Panel not found for app: ${app.name}`);
        return;
    }
    const liquidElement = appPanel.querySelector('#liquid');
    const ringElement = appPanel.querySelector('.app-ring');
    const menuButton = appPanel.querySelector(`#start-stop-button`);
    if (!liquidElement || !ringElement || !menuButton) {
        console.error(`Required element(s) not found for app: ${app.name}`);
        return;
    }

    const stopIcon = `
                            <svg class="w-4 h-4 fill-stone-800 hover:motion-reduce:animate-spin" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M4.5 7.5a3 3 0 013-3h9a3 3 0 013 3v9a3 3 0 01-3 3h-9a3 3 0 01-3-3v-9z" clip-rule="evenodd"></path>
                            </svg>`

    const startIcon = `
                            <svg class="w-4 h-4 fill-stone-800 hover:motion-reduce:animate-spin" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M4.5 5.653c0-1.426 1.529-2.33 2.779-1.643l11.54 6.348c1.295.712 1.295 2.573 0 3.285L7.28 19.991c-1.25.687-2.779-.217-2.779-1.643V5.653z" clip-rule="evenodd"></path>
                            </svg>`
    const isActive = app.services.some(service => service.status === 'active');

    updateElementClasses(liquidElement, ringElement, menuButton, isActive);
    menuButton.innerHTML = isActive ? stopIcon : startIcon;
}

function updateElementClasses(liquidElement, ringElement, menuButton, isActive) {
    liquidElement.classList.remove('liquid-green', 'liquid-red');
    liquidElement.classList.add(isActive ? 'liquid-green' : 'liquid-red');

    ringElement.classList.remove('ring-green-400', 'ring-red-400');
    ringElement.classList.add(isActive ? 'ring-green-500' : 'ring-red-500');

    menuButton.classList.remove('bg-red-300', 'bg-green-300');
    menuButton.classList.add(isActive ? 'bg-red-300' : 'bg-green-300');
}
