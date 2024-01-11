import { fetchData } from '../utils.js';
export class OptionsMenu {
    constructor() {
        this.handlePinClick = this.handlePinClick.bind(this);
        this.initializeEventListeners();
        this.setupEventListeners();
    }

    initializeEventListeners() {
        document.addEventListener('DOMContentLoaded', () => {
            this.setupEventListeners();
        });
        if (document.readyState === 'complete') {
            this.setupEventListeners();
        }
    }

    setupEventListeners() {
        document.addEventListener('click', this.onDocumentClick.bind(this));
        document.body.addEventListener('click', (event) => {
            if (event.target.matches('[id$="_options"], [id$="_options"] *')) {
                this.onButtonMenuClick(event);
            } else if (!event.target.closest('[id$="_options_menu"]')) {
                this.closeAllMenus();
            }
        });
        document.querySelectorAll('.pin-link').forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                event.stopPropagation();
                this.onPinLinkClick(event);
            }
                .bind(this));
        });
    }

    initPinLinkEventListeners() {
        document.querySelectorAll('.pin-link').forEach(button => {
            button.removeEventListener('click', this.handlePinClick);
            button.addEventListener('click', this.handlePinClick.bind(this));
        });  
    }

    handlePinClick(event) {
        event.preventDefault();
        event.stopPropagation();
        this.onPinLinkClick(event);
    }

    closeAllMenus() {
        document.querySelectorAll('[id$="_options_menu"]').forEach(menu => {
            menu.classList.add('hidden');
        });
    }

    onDocumentClick(event) {
        if (!event.target.matches('[id$="_options"], [id$="_options"] *') && !event.target.closest('[id$="_options_menu"]')) {
            this.closeAllMenus();
        }
    }

    onButtonMenuClick(event) {
        const button = event.target.closest('[id$="_options"]');
        if (!button) return;

        const [appName] = button.id.split('_');
        const menuId = `#${appName}_options_menu`;
        const container = button.closest('[id$="grid"]') || button.closest('[id$="list"]');
        const menu = container ? container.querySelector(menuId) : null;

        this.closeAllMenus();
        menu ? menu.classList.toggle('hidden') : console.error('Menu not found in the specific view container');
    }

    isAppPinned(serviceId, preferences) {
        return preferences.pinnedApps.some(pinnedApp => pinnedApp.id === serviceId);
    }

    generateOptionsMenu(appName, appDetails, preferences, multipleServices) {
        let serviceControls = '';
        const mainServiceName = appDetails.name.split('@')[0];
        serviceControls += this.generateServiceControlHTML(mainServiceName, appDetails.status === 'active', multipleServices);
        if (Array.isArray(appDetails.childServices)) {
            appDetails.childServices.forEach(childService => {
                const childServiceName = childService.name.split('@')[0];
                serviceControls += this.generateServiceControlHTML(childServiceName, childService.status === 'active', multipleServices);
            });
        }
        const menuId = appName.replace(/\s+/g, '-').toLowerCase();
        const pinAction = this.generatePinLink(appDetails, preferences);

        return `
            <div class="hidden absolute right-0 mt-2 w-48 bg-white divide-y divide-gray-100 rounded-md shadow-lg z-50" id="${menuId}_options_menu" data-app-name="${menuId}">
                <ul class="py-1 text-gray-700 text-center">
                    <li><a href="${appDetails.configuration[0].root_url}" class="block px-4 py-2 text-sm hover:bg-gray-100">Open</a></li>
                    <li>${pinAction}</li>
                    <li><a href="#" class="block px-4 py-2 text-sm hover:bg-red-300">Uninstall</a></li>
                    <div class="flex flex-col w-full relative bottom-0 app-panel-popover-footer">
                        ${serviceControls}
                    </div>
                </ul>
            </div>
        `;
    }

    generateServiceControlHTML(serviceName, isServiceActive, multipleServices) {
        const restartButtonHtml = `
        <button class="rounded-md bg-amber-300 p-2 mx-2 cursor-pointer uppercase text-xs flex flex-row items-center justify-center font-semibold app-restart-link" data-service-name="${serviceName}">
            <svg class="w-4 h-4 fill-stone-800 hover:animate-spin" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M4.755 10.059a7.5 7.5 0 0112.548-3.364l1.903 1.903h-3.183a.75.75 0 100 1.5h4.992a.75.75 0 00.75-.75V4.356a.75.75 0 00-1.5 0v3.18l-1.9-1.9A9 9 0 003.306 9.67a.75.75 0 101.45.388zm15.408 3.352a.75.75 0 00-.919.53 7.5 7.5 0 01-12.548 3.364l-1.902-1.903h3.183a.75.75 0 000-1.5H2.984a.75.75 0 00-.75.75v4.992a.75.75 0 001.5 0v-3.18l1.9 1.9a9 9 0 0015.059-4.035.75.75 0 00-.53-.918z" clip-rule="evenodd"></path>
            </svg>
        </button>`;

        const controlButtonHtml = isServiceActive ?
            `<button id="${serviceName}_stop-button" class="rounded-md p-2 mx-2 bg-red-600 cursor-pointer uppercase text-xs flex flex-row items-center justify-center font-semibold app-stop-link" data-service-name="${serviceName}">
            <svg class="w-4 h-4 fill-stone-800" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M5.25 7.5A2.25 2.25 0 017.5 5.25h9a2.25 2.25 0 012.25 2.25v9a2.25 2.25 0 01-2.25 2.25h-9a2.25 2.25 0 01-2.25-2.25v-9z" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </button>` :
            `<button id="${serviceName}_start-button" class="rounded-md p-2 mx-2 bg-green-300 cursor-pointer uppercase text-xs flex flex-row items-center justify-center font-semibold app-start-link" data-service-name="${serviceName}">
            <svg class="w-4 h-4 fill-stone-800" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M4.5 5.653c0-1.426 1.529-2.33 2.779-1.643l11.54 6.348c1.295.712 1.295 2.573 0 3.285L7.28 19.991c-1.25.687-2.779-.217-2.779-1.643V5.653z" clip-rule="evenodd"></path>
            </svg>
        </button>`;
        const separator = multipleServices ? `<span class="mx-auto text-xs border-t text-gray-500 dark:text-gray-400">${serviceName}</span>` : '';

        return `${separator} <div class="grid grid-cols-2 divide-x bg-gray-100 dark:bg-transparent pt-1"> ${restartButtonHtml} ${controlButtonHtml} </div>`;
    }

    generatePinLink(appDetails, preferences) {
        const isPinned = this.isAppPinned(appDetails.id, preferences);
        const pinAction = isPinned ? 'Unpin' : 'Pin';
        return `
        <button class="pin-link block w-full px-4 py-2 text-sm hover:bg-gray-100" data-service-id="${appDetails.id}">
            ${pinAction}
        </button>
    `;
    }

    showOptionsMenu(appName, appDetails) {
        const menuHtml = this.generateOptionsMenu(appName, appDetails);
        const menuContainer = document.getElementById(appName + '_options_menu');
        menuContainer.innerHTML = menuHtml;
    }

    onPinLinkClick(event) {
        const serviceId = parseInt(event.target.getAttribute('data-service-id'), 10);
        const data = {
            service: serviceId
        };
        fetchData(`/api/me/preferences/pin`, 'PATCH', data)
            .then(response => {
                if (response) {
                    const pinLink = event.target.closest('.pin-link');
                    pinLink.innerHTML = response.isPinned ? 'Unpin' : 'Pin';
                }
            }).catch(error => {
                console.error('Error pinning/unpinning the app:', error);
            });

            window.location.reload();
    }
}
