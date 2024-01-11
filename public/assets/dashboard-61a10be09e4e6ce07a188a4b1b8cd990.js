/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
// styles
import './styles/app.css';
import './styles/app-cards.css';
import './styles/progress-circle.css';

import './js/clipboard.js';
import WidgetManager from './js/widgets/widgetManager.js';
import AppManager from './js/app/appManager.js';
import { fetchData } from './js/utils.js';
import { processDataForCpuWidget } from './js/widgets/cpuWidget.js';
import { processDataForRamWidget } from './js/widgets/memoryWidget.js';
import { processDataForDiskWidget } from './js/widgets/diskWidget.js';
import { processDataForClientWidget } from './js/widgets/clientWidget.js';
import ViewSwitcher from './js/app/viewSwitcher.js';

const widgets = [
    { type: 'clients', name: 'clients_widget', url: '/api/metrics/clients', processData: processDataForClientWidget },
    { type: 'cpu', name: 'cpu_widget', url: '/api/metrics/cpu', processData: processDataForCpuWidget },
    { type: 'memory', name: 'memory_widget', url: '/api/metrics/ram', processData: processDataForRamWidget },
    { type: 'disk', name: 'disk_widget', url: '/api/metrics/disk', processData: processDataForDiskWidget },
    { type: 'network', name: 'network_widget', url: '/api/metrics/network', processData: null }
];
const dashboard = document.querySelector('[data-widget-panel]');
const preferencesData = await fetchData('/api/user/preferences');
const appStatusData = await fetchData('/api/user/app/list');
const timeInterval = 5000;

if (dashboard && preferencesData?.preferences?.widgets) {
    const widgetManager = new WidgetManager(widgets, preferencesData.preferences);
    widgetManager.initialize();
    const appManager = new AppManager(timeInterval, appStatusData);
    appManager.initialize();
    new ViewSwitcher();
}

import {
    Tab,
    initTE,
} from "tw-elements";

initTE({ Tab });

import { toDarkMode, toLightMode } from './js/menus/theme_switcher.js';

window.toDarkMode = toDarkMode;
window.toLightMode = toLightMode;
