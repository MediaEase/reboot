import { fetchData } from '../utils.js';
import AppStoreManager from './management/AppStoreManager';
import AppStoreUI from './ui/AppStoreUI';
import AppManager from './management/AppManager';
import AppCardUI from './ui/AppCardUI';
import AppTableUI from './ui/AppTableUI';

(async () => {
    try {
        /* Fetch data from the API */
        const storeData = await fetchData('/api/store');
        const appsData = await fetchData('/api/me/my_apps');
        const userData = await fetchData('/api/me');
        const userGroup = userData.group.name;
        const preferencesData = userData.preference;
        /* Initialize the application */
        const container = document.querySelector('#appStoreModal #appStoreContainer');

        const appStoreManager = new AppStoreManager(storeData, appsData, userGroup);
        const appStoreUI = new AppStoreUI(appStoreManager, container);
        appStoreUI.initialize();

        const appManager = new AppManager(5000, appsData, preferencesData);
        await appManager.initialize();
        const processedData = appManager.processData(appsData);

        const appCardUI = new AppCardUI('grid');
        appCardUI.initialize(processedData, preferencesData);
        const appTableUI = new AppTableUI('my-app-list');
        appTableUI.initialize(processedData, preferencesData);
    } catch (error) {
        console.error('Error initializing application:', error);
    }
})();
