class AppStore {
    constructor(storeData, appsData) {
        this.apps = appsData;
        this.storeData = storeData;
        this.container = document.querySelector('#appStoreModal .modal-body');
    }

    initialize() {
        const appStoreContainer = document.createElement('div');
        appStoreContainer.className = "flex";
        this.storeData = this.storeData.filter(app => app.application.name !== 'AppStore');
        this.renderNavigation(appStoreContainer);
        this.renderMainContent(appStoreContainer);
        this.container.appendChild(appStoreContainer);
        this.setupEventListeners();
        this.showHomeCategory();
    }

    renderNavigation(parentContainer) {
        const appTypeCounts = this.storeData.reduce((acc, app) => {
            const type = app.type;
            acc[type] = (acc[type] || 0) + 1;
            return acc;
        }, {});

        const types = Object.keys(appTypeCounts).sort();

        let navItems = `<li style="display: flex; align-items: center;">
                            <a href="#" class="text-gray-900 hover:text-gray-500 block py-2.5 px-2 rounded transition duration-200" 
                                data-type="all" style="display: inline-block; margin-right: 0.5rem;">
                                Home
                            </a>
                        </li>` + types.map(type => {  
            if (typeof type === 'string') {
                return `<li style="display: flex; align-items: center;">
                            <a href="#" class="text-gray-900 hover:text-gray-500 block py-2.5 px-2 rounded transition duration-200" 
                                data-type="${type}" style="display: inline-block; margin-right: 0.5rem;">
                                ${type.charAt(0).toUpperCase() + type.slice(1)}
                            </a>
                            <div style="padding-top: 0.1em; padding-bottom: 0.1rem" class="text-xs px-3 bg-gray-200 text-gray-800 rounded-full">
                                ${appTypeCounts[type]}
                            </div>
                        </li>`;
            }
            return '';
        }).join('');

        const nav = document.createElement('nav');
        nav.className = "w-48 flex-shrink-0 border-r bg-gray-100 border-gray-300";
        nav.innerHTML = `
            <div class="px-2 pt-3">
                <h2 class="text-xl font-semibold text-gray-700">Categories</h2>
                <ul class="mt-4" id="navItems">${navItems}</ul>
            </div>
        `;

        parentContainer.appendChild(nav);
    }

    renderMainContent(parentContainer) {
        const mainContent = document.createElement('div');
        mainContent.className = "flex-grow ml-2 md:px-24 py-4";
        const bannerPath = `/soft_logos/app_store_banner.png`;
        let appCards = this.storeData.map(app => this.renderAppCard(app)).join('');
        mainContent.innerHTML = `
            <img src="${bannerPath}" alt="" class="rounded-xl max-w-5xl mx-auto mb-4">
            <div class="mb-6 px-6 py-1 border-gray-500 bg-white border-2 border-opacity-75 rounded-lg w-full space-x-6 flex items-center">
                <input type="search" id="appSearch" class="w-full border-none bg-transparent text-sm focus:outline-none" placeholder="Search an app...">
                <svg class="w-8 h-8 fill-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon">
                    <path fill-rule="evenodd" d="M10.5 3.75a6.75 6.75 0 1 0 0 13.5 6.75 6.75 0 0 0 0-13.5ZM2.25 10.5a8.25 8.25 0 1 1 14.59 5.28l4.69 4.69a.75.75 0 1 1-1.06 1.06l-4.69-4.69A8.25 8.25 0 0 1 2.25 10.5Z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-6">
                ${appCards}
            </div>
        `;
        parentContainer.appendChild(mainContent);
    }

    renderAppCard(app) {
        const logoPath = `/soft_logos/${app.application.logo}`;
        console.log(app);
        return `
            <div class="shadow-md gap-x-8 lg:gap-x-8 text-lg border-gray-500 bg-white border-2 rounded-xl hover:shadow-xl p-4">
                <div class="flex">
                    <div class="flex-shrink-0 w-24 h-24 mb-4 rounded-full mr-4 pr-4">    
                        <img class="h-auto max-w-[90px] p-1 rounded-full" src="${logoPath}" alt="${app.application.name}_logo">
                    </div>
                    <div class="flex-grow ml-4 text-left">
                        <h3 class="mb-2 font-semibold text-xl leading-tight text-gray-900">${app.application.name}</h3>
                        <p class="text-gray-500 text-md mb-2 pb-2">${app.description}</p>
                        <span class="prose text-sm mt-4 py-3 text-gray-500">Category: 
                            <a href="#" class="text-blue-500 text-sm category-link" data-type="${app.type}" target="blank">
                                ${app.type}
                            </a>
                        </span>
                    </div>
                </div>
                ${this.renderActionButton(app)}
            </div>
        `;
    }

    renderActionButton(app) {
        const isAppInstalled = this.apps.some(installedApp => installedApp.application.id === app.application.id);
        const svgPath = isAppInstalled
            ? '<path fill-rule="evenodd" d="M10.5 3.75a6 6 0 0 0-5.98 6.496A5.25 5.25 0 0 0 6.75 20.25H18a4.5 4.5 0 0 0 2.206-8.423 3.75 3.75 0 0 0-4.133-4.303A6.001 6.001 0 0 0 10.5 3.75Zm2.25 6a.75.75 0 0 0-1.5 0v4.94l-1.72-1.72a.75.75 0 0 0-1.06 1.06l3 3a.75.75 0 0 0 1.06 0l3-3a.75.75 0 1 0-1.06-1.06l-1.72 1.72V9.75Z" clip-rule="evenodd"></path>'
            : '<path fill-rule="evenodd" d="M10.5 3.75a6 6 0 0 0-5.98 6.496A5.25 5.25 0 0 0 6.75 20.25H18a4.5 4.5 0 0 0 2.206-8.423 3.75 3.75 0 0 0-4.133-4.303A6.001 6.001 0 0 0 10.5 3.75Zm2.25 6a.75.75 0 0 0-1.5 0v4.94l-1.72-1.72a.75.75 0 0 0-1.06 1.06l3 3a.75.75 0 0 0 1.06 0l3-3a.75.75 0 1 0-1.06-1.06l-1.72 1.72V9.75Z" clip-rule="evenodd"></path>';

        const dropdownArrowSvg = '<svg data-slot="icon" aria-hidden="true" fill="none" stroke-width="1.5" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m19.5 8.25-7.5 7.5-7.5-7.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>';

        const actionButtonClasses = isAppInstalled ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600 rounded-r-lg';
        const actionButton = `
            <div class="relative inline-flex items-center group">
                <button class="rounded-l-lg inline-flex items-center py-1 px-4 ${actionButtonClasses} duration-300">
                    <svg class="w-8 h-8 fill-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon">
                        ${svgPath}
                    </svg>
                    <span class="mx-4 flex items-start flex-col leading-none">
                        <span class="text-xs text-teal-100">${isAppInstalled ? 'Uninstall' : 'Install'}</span>
                        <span class="title-font text-sm text-gray-100 font-bold">${app.application.name}</span>
                    </span>
                </button>
            </div>`;

        return actionButton;
    }

    handleSearch(query) {
        const lowerCaseQuery = query.toLowerCase();
        const filteredApps = this.storeData.filter(app => 
            app.application.name.toLowerCase().includes(lowerCaseQuery)
        );
        this.updateMainContent(filteredApps);
    }

    filterAppsByType(type) {
        const filteredApps = this.storeData.filter(app => app.type === type);
        this.updateMainContent(filteredApps);
    }

    updateMainContent(filteredApps) {
        const mainContent = this.container.querySelector('.grid');
        let appCards = filteredApps.map(app => this.renderAppCard(app)).join('');
        mainContent.innerHTML = appCards;
    }

    resetFilter() {
        const shuffledApps = this.storeData.sort(() => 0.5 - Math.random());
        const randomApps = shuffledApps.slice(0, 6);
        this.updateMainContent(randomApps);
    }

    showHomeCategory() {
        const homeApps = this.storeData.slice(0, 6);
        this.updateMainContent(homeApps);
    }

    setupEventListeners() {
        // Handling clicks on category links in navigation
        const navItems = this.container.querySelector('#navItems');
        navItems.addEventListener('click', (event) => {
            const target = event.target;
            if (target.tagName === 'A') {
                event.preventDefault();
                const type = target.dataset.type;
                if (type === 'all') {
                    this.resetFilter();
                } else {
                    this.filterAppsByType(type);
                }
            }
        });

        // Handling clicks on category links in app cards
        this.container.addEventListener('click', (event) => {
            if (event.target.classList.contains('category-link')) {
                event.preventDefault();
                const type = event.target.dataset.type;
                this.filterAppsByType(type);
            }
        });

        // Handling input in the search field
        const searchInput = this.container.querySelector('#appSearch');
        searchInput.addEventListener('input', (event) => {
            this.handleSearch(event.target.value);
        });

        // handle the submenu toggle
        this.container.addEventListener('click', (event) => {
            if (event.target.classList.contains('rounded-r-lg')) {
                event.preventDefault();
                event.target.nextElementSibling.classList.toggle('hidden');
            }
        });
    }

    
}

export default AppStore;
