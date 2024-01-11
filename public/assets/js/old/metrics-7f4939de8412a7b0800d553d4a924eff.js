import { updateWidget, fetchData, headers } from '../utils.js';

import { initializeNetworkChart, startNetworkDataFetch } from '../widgets/networkWidget.js';
import { processDataForCpuWidget } from '../widgets/cpuWidget.js';
import { processDataForRamWidget } from '../widgets/memoryWidget.js';
import { processDataForDiskWidget } from '../widgets/diskWidget.js';
import { processDataForClientWidget } from '../widgets/clientWidget.js';
import { processDataForApps } from './app/status.js';
import { initTable } from './app/table.js';

const widgets = [
    { type: 'clients', name: 'clients_widget', url: '/api/metrics/clients', processData: processDataForClientWidget },
    { type: 'cpu', name: 'cpu_widget', url: '/api/metrics/cpu', processData: processDataForCpuWidget },
    { type: 'memory', name: 'memory_widget', url: '/api/metrics/ram', processData: processDataForRamWidget },
    { type: 'disk', name: 'disk_widget', url: '/api/metrics/disk', processData: processDataForDiskWidget },
    { type: 'network', name: 'network_widget', url: '/api/metrics/network', processData: null }
];

const dashboard = document.querySelector('[data-widget-panel]');
const preferencesData = await fetchData('/api/user/preferences');

async function initializeWidgets() {
    if (!preferencesData?.preferences?.widgets) {
        console.error('Invalid user preferences data');
        return;
    }
    const userPreferences = preferencesData.preferences;
    widgets.forEach(({ name, type, url, processData }) => {
        const isWidgetEnabled = userPreferences.widgets.some(uniqueName =>
            uniqueName.replace(/\d+$/, '') === type
        );
        if (!isWidgetEnabled) return;

        if (name === 'network_widget') {
            initializeNetworkChart();
            startNetworkDataFetch(url, headers);
        } else if (processData) {
            setInterval(() => updateWidget(name, url, processData, headers), 5000);
        }
    });
}

if (dashboard) {
    initializeWidgets();
    initTable();
}

