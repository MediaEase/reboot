/**
 * AppStoreManager
 * 
 * This class is responsible for managing the data for the App Store.
 * 
 * @property {Array} apps - The apps data from the API.
 * @property {Array} storeData - The store data from the API.
 * 
 * @method {Array} filterAppsByType - Filters the apps by type.
 * @method {Array} handleSearch - Handles the search functionality.
 * @method {Array} resetFilter - Resets the filter.
 * @method {Array} getHomeCategory - Gets the home category.
 * @method {Object} getAppTypeCounts - Gets the app type counts.
 * @method {void} togglePopover - Toggles the popover.
 */
class AppStoreManager {
    /**
     * Creates an instance of AppStoreManager.
     * @param {Array} storeData - The store data from the API.
     * @param {Array} appsData - The apps data from the API.
     * @param {Array} userGroup - The user group data from the API.
     */
    constructor(storeData, appsData, userGroup) {
        this.apps = appsData;
        this.storeData = storeData.filter(app => app.application.name !== 'AppStore');
        this.userGroup = userGroup;
    }

    /**
     * Filters the apps by type.
     * @param {string} type - The type of apps to filter.
     * @returns {Array} - An array of filtered apps.
     */
    filterAppsByType(type) {
        return this.storeData.filter(app => app.type === type);
    }

    /**
     * Handles the search functionality.
     * @param {string} query - The search query.
     * @returns {Array} - An array of search results.
     */
    handleSearch(query) {
        const lowerCaseQuery = query.toLowerCase();
        return this.storeData.filter(app => 
            app.application.name.toLowerCase().includes(lowerCaseQuery)
        );
    }    

    /**
     * Resets the filter.
     * @returns {Array} - An array of randomly shuffled apps.
     */
    resetFilter() {
        const shuffledApps = this.storeData.sort(() => 0.5 - Math.random());
        return shuffledApps.slice(0, 6);
    }

    /**
     * Gets the home category.
     * @returns {Array} - An array of apps for the home category.
     */
    getHomeCategory() {
        return this.resetFilter();
    }

    /**
     * Gets the app type counts.
     * @returns {Object} - An object containing app type counts.
     */
    getAppTypeCounts() {
        return this.storeData.reduce((acc, app) => {
            const type = app.type;
            acc[type] = (acc[type] || 0) + 1;
            return acc;
        }, {});
    }

    /**
     * Toggles the popover associated with a button.
     * @param {HTMLElement} button - The button element triggering the popover.
     */
    togglePopover(button) {
        // Get the popover id from the button's data attribute
        const popoverId = button.getAttribute('data-popover-id');
        const popover = document.getElementById(popoverId);
    
        if (popover) {
            popover.classList.toggle('hidden');
        } else {
            console.error('Popover not found for id:', popoverId);
        }
    }
}

export default AppStoreManager;
