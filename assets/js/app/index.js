import { fetchData } from '../utils.js';
import AppStoreManager from './management/AppStoreManager';
import AppStoreUI from './ui/AppStoreUI';

const storeData = await fetchData('/api/store');
const appsData = await fetchData('/api/me/my_apps');

const container = document.querySelector('#appStoreModal #appStoreContainer');

const appStoreManager = new AppStoreManager(storeData, appsData);
const appStoreUI = new AppStoreUI(appStoreManager, container, storeData);

appStoreUI.initialize();
