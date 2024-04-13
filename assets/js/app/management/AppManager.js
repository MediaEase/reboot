import AppStatusUpdater from './AppStatusUpdater.js';
import { fetchData } from '../../utils.js';

class AppManager {
    constructor(updateInterval = 3000, appData, preferencesData) {
        this.updateInterval = updateInterval;
        this.appData = appData;
        this.updater = new AppStatusUpdater(appData);
        this.preferencesData = preferencesData;
        //this.status = fetchData('/api/me/services/status');
    }

    async initialize() {
        setInterval(() => this.updateAppStatus(), this.updateInterval);
    }

    async updateAppStatus() {
        try {
            await fetchData('/api/me/services/status');
            this.updater.updateStatus(this.appData);
        } catch (error) {
            console.error('Failed to update app status:', error);
        }
    }

    processData(appData) {
        const groupedByApp = {};
            appData.forEach(service => {
            const appName = service.application.name;
            if (!groupedByApp[appName]) {
                groupedByApp[appName] = {
                    application: service.application,
                    services: [],
                };
            }
            groupedByApp[appName].services.push(service);
        });
    
        const processedData = Object.values(groupedByApp).map(appGroup => ({
            ...appGroup.application,
            services: appGroup.services,
        }));
    
        return processedData;
    }
}

export default AppManager;
