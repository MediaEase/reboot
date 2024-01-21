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
 * @method {Array} getAppTypeCounts - Gets the app type counts.
 * @method {Array} togglePopover - Toggles the popover.
 */
class AppStoreManager {
    constructor(storeData, appsData) {
        this.apps = appsData;
        this.storeData = storeData.filter(app => app.application.name !== 'AppStore');
    }

    filterAppsByType(type) {
        return this.storeData.filter(app => app.type === type);
    }

    handleSearch(query) {
        const lowerCaseQuery = query.toLowerCase();
        return this.storeData.filter(app => 
            app.application.name.toLowerCase().includes(lowerCaseQuery)
        );
    }    

    resetFilter() {
        const shuffledApps = this.storeData.sort(() => 0.5 - Math.random());
        return shuffledApps.slice(0, 6);
    }

    getHomeCategory() {
        return this.resetFilter();
    }

    getAppTypeCounts() {
        return this.storeData.reduce((acc, app) => {
            const type = app.type;
            acc[type] = (acc[type] || 0) + 1;
            return acc;
        }, {});
    }

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
