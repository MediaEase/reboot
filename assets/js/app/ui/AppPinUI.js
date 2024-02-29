/**
 * Class responsible for managing the UI for pinned applications.
 * 
 * @property {HTMLElement} container - The DOM element where pinned apps will be displayed.
 * 
 * @method initialize - Initializes and renders the pinned applications using the provided preferences data.
 * @method renderPinnedApps - Renders the UI for pinned applications. Clears existing content and creates new elements for each pinned app.
 * @method updatePins - Updates the UI for pinned applications. Typically called when there's a change in the pinned apps data.
 */
class AppPinsUI {
    /**
     * Constructs the AppPinsUI object and initializes the container for pinned apps.
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
     * @param {Object} preferencesData - User preferences data containing information about pinned apps.
     * @param {Array} appsData - An array of all application data objects.
     */
    initialize(preferencesData, appsData) {
        this.appsData = appsData;
        const pinnedAppsData = preferencesData.pinnedApps;
        if (pinnedAppsData && pinnedAppsData.length > 0) {
            this.renderPinnedApps(pinnedAppsData);
        }
    }    

    /**
     * Renders the UI for pinned applications. Clears existing content and creates new elements for each pinned app.
     *
     * @param {Array} pinnedAppsData - An array of pinned application data objects.
     */
    renderPinnedApps(pinnedAppsData) {
        this.container.innerHTML = '';
        pinnedAppsData.forEach(pinnedApp => {
            const appDetails = this.appsData.find(app => app.id === pinnedApp.id);
            if (appDetails) {
                const appLink = document.createElement('a');
                appLink.href = appDetails.configuration[0].root_url.toLowerCase();
                appLink.classList.add('flex', 'items-center', 'justify-center', 'w-12', 'h-12', 'mt-2', 'rounded', 'hover:bg-gray-700', 'hover:text-gray-300');
    
                const appImage = document.createElement('img');
                appImage.src = `/soft_logos/${appDetails.application.logo}`;
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
            const preferencesData = await fetchData('/api/me/preferences');
            const pinnedAppsData = preferencesData.pinnedApps;
            if (pinnedAppsData && pinnedAppsData.length > 0) {
                this.renderPinnedApps(pinnedAppsData);
            } else {
                this.container.innerHTML = '';
            }
        } catch (error) {
            console.error('Error updating pinned apps:', error);
        }
    }
}

export default AppPinsUI;
