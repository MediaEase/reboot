import { Controller } from '@hotwired/stimulus';
import { fetchData } from '../js/utils';
import AppPinUI from '../js/app/ui/AppPinUI';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['container'];
    
    connect() {
        this.initializePinnedApps();
    }

    async initializePinnedApps() {
        const appsData = fetchData('/api/apps');
        const myProfile = await fetchData('/api/me');
        const pinnedAppsData = myProfile.preferences.pinnedApps;
        
        this.appPinUI = new AppPinUI('pinned-apps');
        this.appPinUI.initialize(pinnedAppsData, appsData);
    }

    updatePins() {
        if (this.appPinUI) {
            this.appPinUI.updatePins();
        }
    }
}
