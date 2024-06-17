/**
 * AppStoreManager
 * 
 * This class is responsible for managing the data for the App Store.
 * 
 * @property {Array} apps - The apps data from the API.
 * @property {Array} storeData - The store data from the API.
 * 
 * @method {Array} filterAppsByType - Filters the apps by type.
 * @method {Array} filterAppsByUserGroup - Filters the apps based on user group and full app listing status.
 * @method {Array} handleSearch - Handles the search functionality.
 * @method {Array} resetFilter - Resets the filter.
 * @method {Array} getDiscoverCategory - Gets the discover category.
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
    constructor(storeData, appsData, userGroup, isFullAppListing) {
        this.apps = appsData;
        this.storeData = storeData.filter(app => app.name !== 'AppStore');
        this.userGroup = userGroup;
        this.isFullAppListing = isFullAppListing;
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
     * Filters the apps based on user group and full app listing status.
     * @param {Array} apps - The apps to filter.
     * @returns {Array} Filtered apps.
     */
    filterAppsByUserGroup(apps) {
        if (this.isFullAppListing) {
            return apps;
        }
        return apps.filter(app => app.type === this.userGroup || this.userGroup === "full");
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
    resetFilter(apps) {
        const shuffledApps = apps.sort(() => 0.5 - Math.random());
        return shuffledApps.slice(0, 6);
    }

    /**
     * Gets the discover category.
     * @returns {Array} - An array of apps for the discover category.
     */
    getDiscoverCategory() {
        const apps = this.isFullAppListing ? this.storeData : this.filterAppsByUserGroup(this.storeData);
        return this.resetFilter(apps);
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
    
}

export default AppStoreManager;
