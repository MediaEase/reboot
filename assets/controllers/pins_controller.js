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
        const appsData = await fetchData('/api/me/my_apps');
        const preferencesData = await fetchData('/api/me/preferences');
        this.appPinUI = new AppPinUI('pinned-apps');
        this.appPinUI.initialize(preferencesData, appsData);
    }

    updatePins() {
        if (this.appPinUI) {
            this.appPinUI.updatePins();
        }
    }
}
