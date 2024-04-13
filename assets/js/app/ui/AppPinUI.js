/**
 * Class responsible for managing the UI for pinned applications.
 * 
 * @property {HTMLElement} container - The DOM element where pinned apps will be displayed.
 * 
 * @method initialize - Initializes and renders the pinned applications using the provided preferences data.
 * @method renderPinnedApps - Renders the UI for pinned applications. Clears existing content and creates new elements for each pinned app.
 * @method updatePins - Updates the UI for pinned applications. Typically called when there's a change in the pinned apps data.
 */
class AppPinUI {
    /**
     * Constructs the AppPinUI object and initializes the container for pinned apps.
     *
     * @param {string} containerId - The ID of the DOM element where pinned apps will be displayed.
     * @throws Will throw an error if the container element is not found.
     */
    constructor(containerId = 'pinned-apps') {
        this.container = document.getElementById(containerId);
        if (!this.container) {
            console.error('Pinned Apps container not found');
            throw new Error('Pinned Apps container not found');
        }
    }

    /**
     * Initializes and renders the pinned applications using the provided preferences data.
     *
     * @param {Object} pinnedAppsData - User preferences data containing information about pinned apps.
     * @param {Array} appsData - An array of all application data objects.
     */
    async initialize(pinnedAppsData, appsData) {
        try {
            this.appsData = await appsData;
            if (pinnedAppsData && pinnedAppsData.length > 0) {
                this.renderPinnedApps(pinnedAppsData);
            }
        } catch (error) {
            console.error('Error loading application data:', error);
            this.appsData = [];
        }
    }  

    /**
     * Renders the UI for pinned applications. Clears existing content and creates new elements for each pinned app.
     *
     * @param {Array} pinnedAppsData - An array of pinned application data objects.
     */
    renderPinnedApps(pinnedAppsData) {
        this.container.innerHTML = '';
        if (!Array.isArray(pinnedAppsData) || pinnedAppsData.length === 0) {
            const emptyMessage = document.createElement('p');
            emptyMessage.textContent = 'No pinned apps available.';
            this.container.appendChild(emptyMessage);
            return;
        }

        if (!Array.isArray(this.appsData) || !this.appsData.length) {
            console.error('Application data is not available or not an array');
            return;
        }
    
        pinnedAppsData.forEach(pinnedApp => {
            const appDetails = this.appsData.find(app => app.altname === pinnedApp.name);
            if (appDetails) {
                const appLink = document.createElement('a');
                appLink.href = pinnedApp.configuration[0].root_url.toLowerCase();
                appLink.classList.add('flex', 'items-center', 'justify-center', 'w-12', 'h-12', 'mt-2', 'rounded', 'hover:bg-gray-700', 'hover:text-gray-300');
    
                const appImage = document.createElement('img');
                appImage.src = `/soft_logos/${appDetails.logo}`;
                appImage.alt = `${appDetails.name} logo`;
                appImage.classList.add('w-8', 'h-8', 'rounded-full');
    
                appLink.appendChild(appImage);
                this.container.appendChild(appLink);
            }
        });
    }    
    
    /**
     * Updates the UI for pinned applications. Typically called when there's a change in the pinned apps data.
     */
    async updatePins() {
        try {
            const myProfile = await fetchData('/api/me');
            const pinnedAppsData = myProfile.preferences.pinnedApps;
            if (pinnedAppsData && pinnedAppsData.length > 0) {
                this.renderPinnedApps(pinnedAppsData);
            } else {
                this.container.innerHTML = 'No pinned apps available.';
            }
        } catch (error) {
            console.error('Error updating pinned apps:', error);
            this.container.innerHTML = 'Failed to load pinned apps.';
        }
    }
}

export default AppPinUI;
