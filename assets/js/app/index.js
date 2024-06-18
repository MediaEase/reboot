import { fetchData } from '../utils.js';
import WidgetManager from '../widgets/WidgetManager';
import AppManager from './management/AppManager';
import AppStoreManager from './management/AppStoreManager';
import ViewSwitcher from './ui/ViewSwitcher';
import AppCardUI from './ui/AppCardUI';
import AppTableUI from './ui/AppTableUI';
import AppStoreUI from './ui/AppStoreUI';
import { processDataForCpuWidget } from '../widgets/cpuWidget.js';
import { processDataForRamWidget } from '../widgets/memoryWidget.js';
import { processDataForDiskWidget } from '../widgets/diskWidget.js';
import { processDataForClientWidget } from '../widgets/clientWidget.js';

function chooseProcessDataFunction(type) {
    switch (type) {
        case 'cpu':
            return processDataForCpuWidget;
        case 'mem':
            return processDataForRamWidget;
        case 'disk':
            return processDataForDiskWidget;
        case 'clients':
            return processDataForClientWidget;
        default:
            return null;
    }
}

(async () => {
    try {
        // Fetch data from the API
        const storeData = await fetchData('/api/store');
        const userData = await fetchData('/api/me');
        const widgetsData = await fetchData('/api/widgets');
        const globalSettings = await fetchData('/api/settings');
        const appStoreBanner = globalSettings.appstore;
        const appsData = userData.services.sort((a, b) => a.application.name.localeCompare(b.application.name));
        const userGroup = userData.group.name;
        const isFullAppListing = userData.preferences.isFullAppListingEnabled;
        const preferencesData = userData.preferences;
        const isVerbosityEnabled = userData.isVerbosityEnabled;

        // Initialize the application
        initApplication(storeData, appsData, userGroup, preferencesData, widgetsData, isFullAppListing, isVerbosityEnabled, appStoreBanner);
    } catch (error) {
        console.error('Error initializing application:', error);
    }
})();

function initApplication(storeData, appsData, userGroup, preferencesData, widgetsData, isFullAppListing, isVerbosityEnabled, appStoreBanner) {
    const dashboard = document.querySelector('[data-widget-panel]');
    if (dashboard) {
        const transformedWidgets = widgetsData.widgets.map(widget => ({
            type: widget.type,
            name: widget.altName,
            url: `/api/metric/${widget.type}`,
            processData: chooseProcessDataFunction(widget.type)
        }));

        const widgetManager = new WidgetManager(transformedWidgets, preferencesData);
        widgetManager.initialize();
        const container = document.querySelector('#appStoreModal #appStoreContainer');
        const appStoreManager = new AppStoreManager(storeData, appsData, userGroup, isFullAppListing);
        const appStoreUI = new AppStoreUI(appStoreManager, container, appStoreBanner, isVerbosityEnabled);
        appStoreUI.initialize();
    
        const appManager = new AppManager(5000, appsData, preferencesData);
        appManager.initialize().then(() => {
            const processedData = appManager.processData(appsData);
    
            const appCardUI = new AppCardUI('grid');
            appCardUI.initialize(processedData, preferencesData);
            const appTableUI = new AppTableUI('my-app-list');
            appTableUI.initialize(processedData, preferencesData);
        });
        new ViewSwitcher();
    }
}
