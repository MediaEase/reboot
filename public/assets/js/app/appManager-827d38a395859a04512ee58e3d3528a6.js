import { initTable } from './table.js';
import AppStatusUpdater from './appStatusUpdater.js'
import { fetchData } from '../utils.js';

class AppManager {
    constructor(updateInterval = 5000, appData) {
        this.updateInterval = updateInterval;
        this.updater = new AppStatusUpdater();
        this.appData = appData;
        this.status = fetchData('/api/user/app/status');
    }

    async initialize() {
        initTable(this.appData);
        setInterval(() => this.updateAppStatus(), this.updateInterval);
    }

    async updateAppStatus() {
        try {
            const statusData = await fetchData('/api/user/app/status');
            this.updater.updateStatus(statusData);
        } catch (error) {
            console.error('Failed to update app status:', error);
        }
    }
}

export default AppManager;
