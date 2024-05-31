/**
 * AppStoreUI
 * 
 * Class representing the user interface of the app store.
 * 
 * @property {Object} appStoreManager - The app store manager.
 * @property {HTMLElement} container - The DOM container where the UI will be rendered.
 * 
 * @method {void} initialize - Initializes the user interface of the app store.
 * @method {void} renderNavigation - Renders the navigation of the app store UI.
 * @method {void} renderMainContent - Renders the main content of the app store UI.
 * @method {string} renderAppCard - Renders an individual app card.
 * @method {string} renderActionButton - Renders the action button for an app (e.g., install/uninstall).
 * @method {void} updateMainContent - Updates the main content of the UI with the filtered apps.
 * @method {void} setupEventListeners - Sets up event listeners for the app store UI.
 * @method {string} createNavItems - Creates navigation items for each type of app.
 * @method {void} handleCategoryChange - Handles category change in the app store UI.
 * @method {void} executeBashScript - Executes a bash script to install/uninstall an app.
 */

import { fetchData } from '../../utils.js';
class AppStoreUI {
    /**
     * Creates an instance of AppStoreUI.
     * @param {Object} appStoreManager - The app store manager.
     * @param {HTMLElement} container - The DOM container where the UI will be rendered.
     */
    constructor(appStoreManager, container) {
        this.appStoreManager = appStoreManager;
        this.container = container;
        this.storeData = appStoreManager.storeData
        this.apps = appStoreManager.apps;
        this.userGroup = appStoreManager.userGroup;
    }

    /**
     * Initializes the user interface of the app store.
     */
    initialize() {
        const appStoreContainer = document.createElement('div');
        appStoreContainer.className = "flex bg-gray-200/55 dark:bg-gray-900/85";
        const modalTitle = document.querySelector('#appStoreModalLabel');
        modalTitle.innerHTML = `
        <div class="flex justify-center items-center">
            <div class="py-1 rounded-md w-4/12 space-x-6 flex items-left">
                <input type="search" id="appSearch" class="w-full border-none rounded-lg bg-base-100 text-sm focus:outline-none" placeholder="Search an app...">
                <svg class="w-8 h-8 fill-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10.5 3.75a6.75 6.75 0 1 0 0 13.5 6.75 6.75 0 0 0 0-13.5ZM2.25 10.5a8.25 8.25 0 1 1 14.59 5.28l4.69 4.69a.75.75 0 1 1-1.06 1.06l-4.69-4.69A8.25 8.25 0 0 1 2.25 10.5Z" clip-rule="evenodd"></path>
                </svg>
            </div>
        </div>
        `;
        const searchInput = document.querySelector('#appSearch');
        if (searchInput) {
            searchInput.addEventListener('input', (event) => {
                const searchResults = this.appStoreManager.handleSearch(event.target.value);
                this.updateMainContent(searchResults);
            });
        }
        this.renderNavigation(appStoreContainer);
        this.renderMainContent(appStoreContainer);
        this.container.appendChild(appStoreContainer);
        this.setupEventListeners();
        this.updateMainContent(this.appStoreManager.getHomeCategory());
    }

    /**
     * Renders the navigation of the app store UI.
     * @param {HTMLElement} parentContainer - The parent container where the navigation will be added.
     */
    renderNavigation(parentContainer) {
        const appTypeCounts = this.appStoreManager.getAppTypeCounts();
        const types = Object.keys(appTypeCounts).length > 0 ? Object.keys(appTypeCounts).sort() : ['home'];
        let navItems = this.createNavItems(types, appTypeCounts);

        const nav = document.createElement('nav');
        nav.className = "w-48 flex-shrink-0 pb-4 border-r border-gray-500 border-opacity-75 rounded-bl-xl";
        nav.innerHTML = `<div class="px-2 pt-3">
            <h2 class="text-md font-medium">Categories</h2>
            <ul class="mt-4" id="navItems">${navItems}</ul>
        </div>`;

        parentContainer.appendChild(nav);
    }
    
    /**
     * Renders the main content of the app store UI.
     * @param {HTMLElement} parentContainer - The parent container where the main content will be added.
     */
    renderMainContent(parentContainer) {
        const mainContent = document.createElement('div');
        mainContent.className = "flex-grow ml-2 md:px-24 pt-4 mb-8 modal-content";
        const bannerPath = `/uploads/brand/app_store_banner.png`;
        let appCards = this.storeData.map(app => this.renderAppCard(app)).join('');
        mainContent.innerHTML = `
            <div class="console-output max-w-5xl mx-auto hidden content-background text-white p-2 pt-4 rounded-xl relative mb-4">
                <button type="button"
                    class="box-content rounded-none border-none hover:no-underline hover:opacity-75 focus:opacity-100 focus:shadow-none focus:outline-none absolute top-2 right-2"
                    id="closeConsoleButton" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="w-8 h-8 fill-red-800 font-bold dark:fill-white pr-4"><path fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L17.94 6M18 18L6.06 6"/></svg>
                </button>
            </div>
            <img src="${bannerPath}" alt="" class="rounded-xl max-w-5xl mx-auto mb-4 banner-path" data-controller="installApp" data-install-app-target="banner">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-6">
                    ${appCards}
                </div>
        `;
        parentContainer.appendChild(mainContent);
    }

    /**
     * Renders an individual app card.
     * @param {Object} app - The data of the app to display.
     * @returns {string} HTML representing the app card.
     */
    renderAppCard(app) {
        const shouldApplyBlur = this.userGroup !== "full" && this.userGroup !== app.type;
        const application = app.application;
        const logoPath = `/uploads/soft_logos/${application.logo}`;
        const cardClasses = `shadow-md gap-x-8 lg:gap-x-8 text-lg content-background dark:content-background light:border-2 border-gray-500 border-opacity-75 rounded-xl hover:shadow-xl p-4`;
        return `
            <div class="${cardClasses}">
                <div class="flex">
                    <div class="flex-shrink-0 w-20 h-20 mb-4 rounded-full mr-4 pr-4">    
                        <img class="h-auto max-w-[70px] p-1 rounded-full" src="${logoPath}" alt="${application.name}_logo">
                    </div>
                    <div class="flex-grow ml-4 text-left">
                        <h3 class="mb-2 font-semibold text-xl leading-tight text-gray-900 dark:text-neutral-200">${application.name}</h3>
                        <p class="text-gray-500 text-md mb-2 pb-2 ${shouldApplyBlur ? 'blur-[1px]' : ''}">${app.description}</p>
                        <span class="prose text-sm mt-4 py-3 text-gray-500">Category: 
                            <a href="#" class="text-blue-500 text-sm category-link"data-type="${app.type}" target="blank">
                                ${app.type}
                            </a>
                        </span>
                    </div>
                </div>
                ${this.renderActionButton(app)}
            </div>
        `;
    }

    /**
     * Renders the action button for an app (e.g., install/uninstall).
     * @param {Object} app - The app data for which to generate the button.
     * @returns {string} HTML representing the action button.
     */
    renderActionButton(app) {
        const isAppInstalled = this.apps.some(installedApp => installedApp.application.id === app.id);
        const isButtonDisabled = this.userGroup !== "full" && this.userGroup !== app.type;
        const disabledClass = isButtonDisabled ? 'blur-[1px] cursor-not-allowed' : '';
        const actionButtonClasses = isAppInstalled ? 'bg-red-500 hover:bg-red-600 pl-4' : 'px-4 bg-green-500 hover:bg-green-600 rounded-r-lg';
        const svgPath = isAppInstalled
            ? '<path fill-rule="evenodd" d="M10.5 3.75a6 6 0 0 0-5.98 6.496A5.25 5.25 0 0 0 6.75 20.25H18a4.5 4.5 0 0 0 2.206-8.423 3.75 3.75 0 0 0-4.133-4.303A6.001 6.001 0 0 0 10.5 3.75Zm2.25 6a.75.75 0 0 0-1.5 0v4.94l-1.72-1.72a.75.75 0 0 0-1.06 1.06l3 3a.75.75 0 0 0 1.06 0l3-3a.75.75 0 1 0-1.06-1.06l-1.72 1.72V9.75Z" clip-rule="evenodd"></path>'
            : '<path fill-rule="evenodd" d="M10.5 3.75a6 6 0 0 0-5.98 6.496A5.25 5.25 0 0 0 6.75 20.25H18a4.5 4.5 0 0 0 2.206-8.423 3.75 3.75 0 0 0-4.133-4.303A6.001 6.001 0 0 0 10.5 3.75Zm2.25 6a.75.75 0 0 0-1.5 0v4.94l-1.72-1.72a.75.75 0 0 0-1.06 1.06l3 3a.75.75 0 0 0 1.06 0l3-3a.75.75 0 1 0-1.06-1.06l-1.72 1.72V9.75Z" clip-rule="evenodd"></path>';
        const popoverArrowSvg = '<path d="m19.5 8.25-7.5 7.5-7.5-7.5" stroke-linecap="round" stroke-linejoin="round"></path>';
        const popoverClasses = isAppInstalled ? 'bg-red-500 hover:bg-red-600 popover-arrow-button' : 'hidden';
        const links = isButtonDisabled ? ['Remove'] : ['Backup', 'Reinstall', 'Remove', 'Reset', 'Update'];
        const listItems = links.map(link => `<li><a data-action="${link.toLowerCase()}" class="action-link">${link}</a></li>`).join('');
        const popoverButton = `
        <div class="dropdown dropdown-bottom">
            <div tabindex="0" role="button" class="rounded-r-lg inline-flex items-center py-2 ${popoverClasses} ${isButtonDisabled ? 'blur-[2px]' : ''}">
                <svg class="w-7 h-7 fill-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon">
                    ${popoverArrowSvg}
                </svg>
            </div>
            <ul tabindex="0" class="dropdown-content z-[1000] menu p-2 shadow w-52 text-xs bg-base-100 bg-opacity-95 blur-none">
                ${listItems}
            </ul>
        </div>
        `;
    
        const action = isAppInstalled ? 'uninstall' : 'install';
        const actionButton = `
        <div class="relative inline-flex items-center">
            <a class="${actionButtonClasses} ${disabledClass} rounded-l-lg inline-flex items-center cursor-pointer py-1 action-button"
                ${isButtonDisabled ? 'disabled' : ''}
                data-app-id="${app.id}"
                data-app-name="${app.application.altname}"
                data-action="${action}">
                <svg class="w-8 h-8 fill-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon">
                    ${svgPath}
                </svg>
                <span class="ml-4 mr-2 flex items-start flex-col leading-none">
                    <span class="text-xs text-teal-100">${isAppInstalled ? 'Uninstall' : 'Install'}</span>
                    <span class="title-font text-sm text-gray-100 font-bold">${app.application.name}</span>
                </span>
            </a>
            ${isAppInstalled ? popoverButton : ''}
        </div>`;
    
        return actionButton;
    }
    

    /**
     * Updates the main content of the UI with the filtered apps.
     * @param {Array} filteredApps - The apps to display.
     */
    updateMainContent(filteredApps) {
        const mainContent = this.container.querySelector('.grid');
        let appCards = filteredApps.map(app => this.renderAppCard(app)).join('');
        mainContent.innerHTML = appCards;
    }

    /**
     * Sets up event listeners for the app store UI.
     */
    setupEventListeners() {
        // Handling clicks on category links
        this.container.addEventListener('click', (event) => {
            if (event.target.matches('#navItems a, .category-link') || event.target.closest('.category-link')) {
                event.preventDefault();
                const target = event.target.closest('.category-link');
                this.handleCategoryChange(target.dataset.type);
            }
        });
        // Handling clicks on action buttons
        this.container.addEventListener('click', (event) => {
            if (event.target.closest('.action-button')) {
                event.preventDefault();
                const appId = event.target.closest('[data-app-id]').dataset.appId;
                const action = event.target.closest('.action-button').dataset.action;
                this.executeBashScript(appId, action);
            }
        });
        // Handling clicks on the close button of the console view
        this.container.addEventListener('click', (event) => {
            if (event.target.closest('#closeConsoleButton')) {
                event.preventDefault();
                this.hideConsole();
            }
        });
    }

    /**
     * Creates navigation items for each type of app.
     * @param {Array} types - The available types of apps.
     * @param {Object} appTypeCounts - The count of apps for each type.
     * @returns {string} HTML representing the navigation items.
     */
    createNavItems(types, appTypeCounts) {
        const icons = {
            home: `<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="w-6 h-6"><path fill="currentColor" d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>`,
            media: `<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 48 48" class="w-6 h-6"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M8.797 8.797c8.397-8.396 22.01-8.396 30.406 0s8.396 22.01 0 30.406s-22.01 8.396-30.406 0"/><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="m34 24l-16-9.238v18.476z"/></svg>`,
            remote: `<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 48 48" class="w-6 h-6"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M37.849 20.953L27.098 10.2c-.44-.44-1.332-.26-1.992.4L4.182 31.524c-.66.66-.839 1.552-.399 1.992l10.751 10.751c.44.44 1.332.261 1.992-.399h0L37.45 22.945c.66-.66.839-1.553.399-1.992"/><circle cx="14.943" cy="33.042" r="2.484" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M31.171 6.002a10.8 10.8 0 0 1 7.702 3.318h0a10.8 10.8 0 0 1 3.323 7.697m-10.204-7.88a7.115 7.115 0 0 1 7.068 7.077m-6.324-4.147a3.57 3.57 0 0 1 3.302 2.65a2.8 2.8 0 0 1 .089.74"/><circle cx="26.279" cy="16.303" r=".75" fill="currentColor"/><circle cx="32.222" cy="22.247" r=".75" fill="currentColor"/><circle cx="29.25" cy="19.275" r=".75" fill="currentColor"/><circle cx="23.307" cy="19.275" r=".75" fill="currentColor"/><circle cx="29.25" cy="25.218" r=".75" fill="currentColor"/><circle cx="26.279" cy="22.247" r=".75" fill="currentColor"/><circle cx="20.335" cy="22.247" r=".75" fill="currentColor"/><circle cx="26.279" cy="28.19" r=".75" fill="currentColor"/><circle cx="23.307" cy="25.218" r=".75" fill="currentColor"/></svg>`,
            automation: `<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="w-6 h-6"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" color="currentColor"><path d="M19 16v-2c0-2.828 0-4.243-.879-5.121C17.243 8 15.828 8 13 8h-2c-2.828 0-4.243 0-5.121.879C5 9.757 5 11.172 5 14v2c0 2.828 0 4.243.879 5.121C6.757 22 8.172 22 11 22h2c2.828 0 4.243 0 5.121-.879C19 20.243 19 18.828 19 16m0 2c1.414 0 2.121 0 2.56-.44c.44-.439.44-1.146.44-2.56s0-2.121-.44-2.56C21.122 12 20.415 12 19 12M5 18c-1.414 0-2.121 0-2.56-.44C2 17.122 2 16.415 2 15s0-2.121.44-2.56C2.878 12 3.585 12 5 12m8.5-8.5a1.5 1.5 0 1 1-3 0a1.5 1.5 0 0 1 3 0M12 5v3m-3 5v1m6-1v1"/><path d="M10 17.5s.667.5 2 .5s2-.5 2-.5"/></g></svg>`,
            download: `<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="w-6 h-6"><path fill="currentColor" d="m12 15.577l-3.539-3.538l.708-.72L11.5 13.65V5h1v8.65l2.33-2.33l.709.719zM6.616 19q-.691 0-1.153-.462T5 17.384v-2.423h1v2.423q0 .231.192.424t.423.192h10.77q.23 0 .423-.192t.192-.424v-2.423h1v2.423q0 .691-.462 1.153T17.384 19z"/></svg>`
        };
    
        const navItems = types.map(type => {
            return `<li class="mb-2 flex justify-start items-center pl-4">
                <a href="#" class="text-gray-700 dark:text-gray-200 category-link flex items-center mr-2" 
                    data-type="${type}" target="blank">
                    ${icons[type]}
                    <span class="ml-2">${type.charAt(0).toUpperCase() + type.slice(1)}</span>
                </a>
                <div class="px-2 py-0.5 text-gray-700 dark:text-gray-200 text-xs content-background rounded-full">
                    ${appTypeCounts[type]}
                </div>
            </li>`;
        });
    
        navItems.unshift(`<li class="mb-2 flex justify-start items-center pl-4">
            <a href="#" class="text-gray-700 dark:text-gray-200 category-link flex items-center mr-2" data-type="all" target="blank">
                ${icons['home']}
                <span class="ml-2">Home</span>
            </a>
        </li>`);
        return navItems.join('');
    }
    

    /**
     * Handles category change in the app store UI.
     * @param {string} type - The selected category type.
     */
    handleCategoryChange(type) {
        let filteredApps = type === 'all' 
            ? this.appStoreManager.getHomeCategory() 
            : this.appStoreManager.filterAppsByType(type);
        if (type !== 'all') {
            filteredApps = filteredApps.sort((a, b) => a.application.name.localeCompare(b.application.name));
        }
    
        this.updateMainContent(filteredApps);
    }

    /**
     * Executes a bash script to install/uninstall an app.
     * @param {string} appId - The ID of the app to install/uninstall.
     * @param {string} action - The action to perform (install/uninstall).
     * @returns {void}
     */
    executeBashScript(appId, action) {
        const consoleContainer = this.container.querySelector('.console-output');
        const banner = this.container.querySelector('.banner-path');
        consoleContainer.classList.remove('hidden');
        banner.classList.add('hidden');
        consoleContainer.innerHTML = `
            <button type="button"
                class="box-content rounded-none border-none hover:no-underline hover:opacity-75 focus:opacity-100 focus:shadow-none focus:outline-none absolute top-1 right-1"
                id="closeConsoleButton" aria-label="Close">
                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" class="w-6 h-6 fill-red-800 font-bold dark:fill-white pr-2">
                    <path fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L17.94 6M18 18L6.06 6"></path>
                </svg>
            </button>
            <div class="text-left">
                <div class="bg-gray-300 dark:bg-gray-700 px-4 py-1 rounded-t-md inline-block">
                    <span class="font-semibold">zen</span>
                </div>
            </div>
            <div class="bg-gray-100 dark:bg-gray-800 p-1 rounded-sm border border-gray-300 dark:border-gray-700 text-left py-2 shadow-inner text-sm text-neutral-700 dark:text-neutral-200 font-mono">
                <p>Executing the script...</p>
            </div>
        `;  
        
        fetchData('/api/store/install', 'POST', { appId, action })
            .then(data => {
                const consoleOutput = consoleContainer.querySelector('.bg-gray-100');
                consoleOutput.innerHTML = ''; // Clear initial content
                if (data && data.output) {
                    data.output.forEach(line => {
                        const message = document.createElement('p');
                        message.textContent = line;
                        consoleOutput.appendChild(message);
                        consoleContainer.scrollTop = consoleContainer.scrollHeight;
                    });
                } else {
                    const message = document.createElement('p');
                    message.textContent = 'Error executing the script.';
                    consoleOutput.appendChild(message);
                }
            })
            .catch(error => {
                const consoleOutput = consoleContainer.querySelector('.bg-gray-100');
                consoleOutput.innerHTML = ''; // Clear initial content
                const message = document.createElement('p');
                message.textContent = `Error: ${error.message}`;
                consoleOutput.appendChild(message);
            });
    }

    /**
     * Hides the console view.
     */
    hideConsole() {
        const consoleContainer = this.container.querySelector('.console-output');
        const banner = this.container.querySelector('.banner-path');
        
        // Hide the console and show the banner
        consoleContainer.classList.add('hidden');
        banner.classList.remove('hidden');
    }
}

export default AppStoreUI;
