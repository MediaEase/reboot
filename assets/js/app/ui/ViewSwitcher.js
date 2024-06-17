import { fetchData } from '../../utils.js';
import AppStatusUpdater from '../management/AppStatusUpdater.js';

class ViewSwitcher {
    constructor() {
        this.btnViewCards = document.getElementById('btnViewCards');
        this.btnViewTable = document.getElementById('btnViewTable');
        this.setupEventListeners();
        this.fetchData = fetchData;
    }

    getAppStatus() {
        this.fetchData('/api/me/services/status')
            .then(appStatusData => {
                const appStatusUpdater = new AppStatusUpdater(appStatusData);
                appStatusUpdater.updateStatus(appStatusData);
            })
            .catch(error => {
                console.error('Error fetching app status:', error);
            });
    }
    
    switchView(showId, hideId, displayType) {
        document.getElementById(showId).style.display = displayType;
        document.getElementById(hideId).style.display = 'none';
        this.fetchData(`/api/me/preferences/display?value=${showId}`, 'PATCH')
            .then(() => {
                this.getAppStatus();
            })
            .catch(error => {
                console.error('Error updating display preference:', error);
            });

        if (showId === 'grid') {
            document.getElementById('app-finder-card-container').style.display = 'block';
            document.getElementById('app-finder-list').style.display = 'none';
        } else if (showId === 'list') {
            document.getElementById('app-finder-card-container').style.display = 'none';
            document.getElementById('app-finder-list').style.display = 'block';
        }

        this.getAppStatus();
    }

    setupEventListeners() {
        this.btnViewCards.addEventListener('click', (event) => {
            event.preventDefault();
            this.switchView('grid', 'list', 'grid');
        });

        this.btnViewTable.addEventListener('click', (event) => {
            event.preventDefault();
            this.switchView('list', 'grid', 'block');
        });
    }
}

export default ViewSwitcher;
