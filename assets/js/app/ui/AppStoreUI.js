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
 */
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
        appStoreContainer.className = "flex";
        const modalTitle = document.querySelector('#appStoreModalLabel');
        modalTitle.innerHTML = `
        <div class="flex justify-center items-center">
            <div class="py-1 rounded-md w-4/12 space-x-6 flex items-lef">
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
            <h2 class="text-md font-medium text-gray-900 dark:text-neutral-200">Categories</h2>
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
        mainContent.className = "flex-grow ml-2 md:px-24 pt-4 mb-8";
        const bannerPath = `/soft_logos/app_store_banner.png`;
        let appCards = this.storeData.map(app => this.renderAppCard(app)).join('');
        mainContent.innerHTML = `
        <img src="${bannerPath}" alt="" class="rounded-xl max-w-5xl mx-auto mb-4" data-controller="installApp" data-install-app-target="banner">
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
        const logoPath = `/soft_logos/${app.application.logo}`;
        const cardClasses = `shadow-md gap-x-8 lg:gap-x-8 text-lg content-background dark:content-background light:border-2 border-gray-500 border-opacity-75 rounded-xl hover:shadow-xl p-4`;
        return `
            <div class="${cardClasses}">
                <div class="flex">
                    <div class="flex-shrink-0 w-24 h-24 mb-4 rounded-full mr-4 pr-4">    
                        <img class="h-auto max-w-[90px] p-1 rounded-full" src="${logoPath}" alt="${app.application.name}_logo">
                    </div>
                    <div class="flex-grow ml-4 text-left">
                        <h3 class="mb-2 font-semibold text-xl leading-tight text-gray-900 dark:text-neutral-200">${app.application.name}</h3>
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
        const isAppInstalled = this.apps.some(installedApp => installedApp.application.id === app.application.id);
        const isButtonDisabled = this.userGroup !== "full" && this.userGroup !== app.type;
        const disabledClass = isButtonDisabled ? 'blur-[1px] cursor-not-allowed' : '';
        const actionButtonClasses = isAppInstalled ? 'bg-red-500 hover:bg-red-600 pl-4' : 'px-4 bg-green-500 hover:bg-green-600 rounded-r-lg';
        const svgPath = isAppInstalled
            ? '<path fill-rule="evenodd" d="M10.5 3.75a6 6 0 0 0-5.98 6.496A5.25 5.25 0 0 0 6.75 20.25H18a4.5 4.5 0 0 0 2.206-8.423 3.75 3.75 0 0 0-4.133-4.303A6.001 6.001 0 0 0 10.5 3.75Zm2.25 6a.75.75 0 0 0-1.5 0v4.94l-1.72-1.72a.75.75 0 0 0-1.06 1.06l3 3a.75.75 0 0 0 1.06 0l3-3a.75.75 0 1 0-1.06-1.06l-1.72 1.72V9.75Z" clip-rule="evenodd"></path>'
            : '<path fill-rule="evenodd" d="M10.5 3.75a6 6 0 0 0-5.98 6.496A5.25 5.25 0 0 0 6.75 20.25H18a4.5 4.5 0 0 0 2.206-8.423 3.75 3.75 0 0 0-4.133-4.303A6.001 6.001 0 0 0 10.5 3.75Zm2.25 6a.75.75 0 0 0-1.5 0v4.94l-1.72-1.72a.75.75 0 0 0-1.06 1.06l3 3a.75.75 0 0 0 1.06 0l3-3a.75.75 0 1 0-1.06-1.06l-1.72 1.72V9.75Z" clip-rule="evenodd"></path>';

        const popoverArrowSvg = '<path d="m19.5 8.25-7.5 7.5-7.5-7.5" stroke-linecap="round" stroke-linejoin="round"></path>';
        const popoverClasses = isAppInstalled ? 'bg-red-500 hover:bg-red-600 popover-arrow-button' : 'hidden';
        const links = isButtonDisabled ? ['Remove'] : ['Backup', 'Reinstall', 'Remove', 'Reset', 'Update'];
        const listItems = links.map(link => `<li><a>${link}</a></li>`).join('');
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
            <a class="${actionButtonClasses} ${disabledClass} rounded-l-lg inline-flex items-center cursor-pointer py-1"
                ${isButtonDisabled ? 'disabled' : ''}
                data-action="${isButtonDisabled ? '' : 'click->storeactions#handleAction'}"
                data-storeactions-url-value="/api/store"
                data-app-name="${app.application.altname}"
                data-action-type="${action}">
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
            if (event.target.matches('#navItems a, .category-link')) {
                event.preventDefault();
                this.handleCategoryChange(event.target.dataset.type);
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
        const navItems = types.map(type => {
        return `<li class="mb-2 flex justify-start items-center pl-4">
            <a href="#" class="text-gray-700 dark:text-gray-200 text-sm category-link" 
                data-type="${type}" style="display: inline-block; margin-right: 0.5rem;">
                ${type.charAt(0).toUpperCase() + type.slice(1)}
            </a>
            <div class="px-2 py-0.5 text-gray-700 dark:text-gray-200 text-xs content-background rounded-full">
                ${appTypeCounts[type]}
            </div>
        </li>`;
        });
        navItems.unshift(`<li class="mb-2 flex justify-start items-center pl-4"><a href="#" class="text-gray-700 dark:text-gray-200 text-sm category-link" data-type="all" target="blank">Home</a></li>`);
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
        this.updateMainContent(filteredApps);
    }
}

export default AppStoreUI;
