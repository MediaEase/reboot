import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

import './styles/app.css';
import './js/clipboard.js';
import './js/routing/links.js';
import { toDarkMode, toLightMode } from './js/menus/theme_switcher.js';
import { fetchData } from './js/utils.js';
import AccessLogsTableUI from './js/logs/tables/AccessLogsTableUI.js';

window.toDarkMode = toDarkMode;
window.toLightMode = toLightMode;

import { Tab, Modal, Popover, Ripple, Tooltip, initTE } from "tw-elements";
document.addEventListener('DOMContentLoaded', () => {
    initTE({ Tab, Modal, Popover, Ripple, Tooltip });
});

(async () => {
    try {
        // Fetch data from the API
        const logsData = await fetchData('/api/logs/access-logs');
        initLogs(logsData);
    } catch (error) {
        console.error('Error initializing application:', error);
    }
})();

function initLogs(logsData) {
    const logsTableUI = new AccessLogsTableUI('access-logs-table');
    logsTableUI.initialize(logsData);
}
