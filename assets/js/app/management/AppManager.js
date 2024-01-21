import { initTable } from '../ui/table.js';
import AppStatusUpdater from './AppStatusUpdater.js'
import { fetchData } from '../../utils.js';
import { initCards } from '../ui/appCard.js';

class AppManager {
    constructor(updateInterval = 5000, appData, preferencesData) {
        this.updateInterval = updateInterval;
        this.updater = new AppStatusUpdater();
        this.appData = appData;
        this.status = fetchData('/api/me/services/status');
        this.preferences = preferencesData;
    }

    async initialize() {
        const processedData = this.processData(this.appData);
        initCards(processedData, this.preferences);
        initTable(processedData, this.preferences);
        setInterval(() => this.updateAppStatus(), this.updateInterval);
    }

    async updateAppStatus() {
        try {
            await fetchData('/api/me/services/status');
            const appData = await fetchData('/api/me/my_apps');
            this.updater.updateStatus(appData);
        } catch (error) {
            console.error('Failed to update app status:', error);
        }
    }

    processData(appData) {
        const groupedByApp = {};
        appData.forEach(service => {
            const appName = service.application.name;
            if (!groupedByApp[appName]) {
                groupedByApp[appName] = [];
            }
            groupedByApp[appName].push(service);
        });
        const processedData = {};
        Object.keys(groupedByApp).forEach(appName => {
            const services = groupedByApp[appName];
            const parentService = services.find(s => !s.name.includes('-web'));
            const childServices = services.filter(s => s.name.includes('-web') || s.name.includes('mergerfs'));
            const mergedService = {
                ...parentService,
                childServices: childServices.length > 0 ? childServices : parentService.childServices
            };
            processedData[appName] = [mergedService];
        });
        return processedData;
    }
}

export default AppManager;
