import { fetchData } from '../../utils.js';

/**
 * Represents an options menu for an app, handling its interactions and UI updates.
 */
export class OptionsMenu {
    constructor() {
        this.eventListenersInitialized = false; // Indicates if event listeners are already set up to avoid redundancy.
        this.initializeEventListeners(); // Initial call to set up event listeners.
    }

    /**
     * Sets up event listeners if they haven't been initialized. This method ensures
     * that event listeners are added only once to prevent duplicate handling of events.
     */
    initializeEventListeners() {
        if (!this.eventListenersInitialized) {
            // Setup listeners when DOM is fully loaded.
            document.addEventListener('DOMContentLoaded', this.setupEventListeners.bind(this));

            // If DOM is already loaded, setup listeners immediately.
            if (document.readyState === 'complete') {
                this.setupEventListeners();
            }

            // Mark event listeners as initialized.
            this.eventListenersInitialized = true;
        }
    }

    /**
     * Establishes global and specific event listeners related to the options menu,
     * including general document clicks and more specific interactions within the menu.
     */
    setupEventListeners() {
        // Listen for any clicks in the document to handle global interactions.
        document.addEventListener('click', this.onDocumentClick.bind(this));

        // Handle clicks within the document body, focusing on menu interactions.
        document.body.addEventListener('click', (event) => {
            // Handle menu button clicks and close menus when clicking outside.
            if (event.target.matches('[id$="_options"], [id$="_options"] *')) {
                this.onButtonMenuClick(event);
            } else if (!event.target.closest('[id$="_options_menu"]')) {
                this.closeAllMenus();
            }
        });

        // Attach listeners to dynamically generated 'pin-link' elements.
        this.initPinLinkEventListeners();
    }

    /**
     * Reinitializes event listeners for 'pin-link' elements. This is necessary to
     * ensure proper event handling for dynamically added or removed elements.
     */
    initPinLinkEventListeners() {
        // Remove existing listeners and reattach them to ensure only one listener per element.
        const pinLinks = document.querySelectorAll('.pin-link');
        pinLinks.forEach(button => {
            button.removeEventListener('click', this.handlePinClick);
            button.addEventListener('click', this.handlePinClick.bind(this));
        });
    }

    /**
     * Handles the click event on pin links, preventing default behavior and stopping
     * event propagation. This method ensures singular handling of the pin action.
     * @param {Event} event - The event object associated with the click.
     */
    handlePinClick(event) {
        event.preventDefault();
        event.stopImmediatePropagation();

        // Extract service ID and handle pinning/unpinning actions.
        const serviceId = parseInt(event.target.getAttribute('data-service-id'), 10);
        fetchData(`/api/me/preferences/pin`, 'PATCH', { service: serviceId })
            .then(response => {
                if (response) {
                    window.location.reload(); // Reload the page to reflect the updated pin status.
                }
            }).catch(error => {
                console.error('Error pinning/unpinning the app:', error);
            });
    }

    /**
     * Closes all open menus to maintain a clean and intuitive interface.
     * Ensures that only one menu is open at a time for better user experience.
     */
    closeAllMenus() {
        document.querySelectorAll('[id$="_options_menu"]').forEach(menu => {
            menu.classList.add('hidden'); // Hide each menu.
        });
    }

    /**
     * Handles clicks on the document to manage menu visibility.
     * Closes any open menus if a click occurs outside of an options menu.
     * @param {Event} event - The event object associated with the click.
     */
    onDocumentClick(event) {
        if (!event.target.matches('[id$="_options"], [id$="_options"] *') && !event.target.closest('[id$="_options_menu"]')) {
            this.closeAllMenus();
        }
    }

    /**
     * Handles the click event on the options menu button. This function manages
     * the toggling of menu visibility based on user interaction.
     * @param {Event} event - The event object associated with the menu button click.
     */
    onButtonMenuClick(event) {
        const button = event.target.closest('[id$="_options"]');
        if (!button) return; // Exit if no relevant button was clicked.

        // Extract the app name and use it to identify the corresponding menu.
        const [appName] = button.id.split('_');
        const menuId = `#${appName}_options_menu`;
        const container = button.closest('[id$="grid"]') || button.closest('[id$="list"]');
        const menu = container ? container.querySelector(menuId) : null;

        // Toggle the visibility of the identified menu.
        this.closeAllMenus(); // Close all menus before opening the new one.
        if (menu) {
            menu.classList.toggle('hidden'); // Toggle visibility of the target menu.
        } else {
            console.error('Menu not found in the specific view container');
        }
    }

    /**
     * Checks if a specific app is pinned based on the user's preferences.
     * 
     * @param {number} serviceId - The unique identifier for the service being checked.
     * @param {object} preferences - The user's preferences object, which should include a 'pinnedApps' array.
     * @returns {boolean} - Returns true if the app is pinned (i.e., found in the user's pinnedApps array); otherwise, returns false.
     * 
     * This function iterates through the 'pinnedApps' array within the provided preferences object.
     * It checks if any of the apps in this array match the provided serviceId. If a match is found,
     * it indicates that the app is currently pinned by the user.
     */
    isAppPinned(serviceId, preferences) {
        // Check if the preferences object has a 'pinnedApps' array.
        return preferences.pinnedApps.some(pinnedApp => pinnedApp.id === serviceId);
    }

    /**
     * Generates the HTML for the options menu of an application.
     * 
     * @param {string} appName - The name of the application.
     * @param {object} appDetails - Object containing details about the application. 
     *                              Expected to have 'name', 'status', 'childServices', 
     *                              and 'configuration' properties.
     * @param {object} preferences - User preferences, used for determining pin status.
     * @param {boolean} multipleServices - Indicates if the app has multiple services, 
     *                                     affecting the layout and content of the menu.
     * @returns {string} HTML string for the options menu.
     * 
     * The function generates a menu with options to open, pin/unpin, and uninstall the application.
     * It also dynamically generates control buttons for the main service and any child services,
     * based on their active or inactive status. The generated HTML is used to build a responsive
     * and interactive user interface for managing applications.
     */
    generateOptionsMenu(appName, appDetails, preferences, multipleServices) {
        // Initialize empty string for service controls
        let serviceControls = '';
        
        // Extract and use the main service name from appDetails
        const mainServiceName = appDetails.name.split('@')[0];
        serviceControls += this.generateServiceControlHTML(mainServiceName, appDetails.status === 'active', multipleServices);
        
        // Loop through child services if any and append their controls
        if (Array.isArray(appDetails.childServices)) {
            appDetails.childServices.forEach(childService => {
                const childServiceName = childService.name.split('@')[0];
                serviceControls += this.generateServiceControlHTML(childServiceName, childService.status === 'active', multipleServices);
            });
        }

        // Generate a unique menu ID based on the application name
        const menuId = appName.replace(/\s+/g, '-').toLowerCase();
        
        // Generate the pin/unpin action link
        const pinAction = this.generatePinLink(appDetails, preferences);

        // Construct and return the full HTML for the options menu
        return `
            <div class="hidden absolute right-0 mt-2 w-48 bg-white divide-y divide-gray-100 rounded-md shadow-lg z-50" id="${menuId}_options_menu" data-app-name="${menuId}">
                <ul class="py-1 text-gray-700 text-center">
                    <li><a href="${appDetails.services[0].configuration.root_url}" class="block px-4 py-2 text-sm hover:bg-gray-100">Open</a></li>
                    <li>${pinAction}</li>
                    <li><a href="#" class="block px-4 py-2 text-sm hover:bg-red-300">Uninstall</a></li>
                    <div class="flex flex-col w-full relative bottom-0 app-panel-popover-footer">
                        ${serviceControls}
                    </div>
                </ul>
            </div>
        `;
    }

    /**
     * Generates HTML for service control buttons, including start, stop, and restart.
     * @param {string} serviceName - The name of the service.
     * @param {boolean} isServiceActive - Indicates if the service is currently active.
     * @param {boolean} multipleServices - Flag to indicate if there are multiple services, 
     *                                      affecting the layout of the controls.
     * @returns {string} HTML string for service control buttons.
     * 
     * This function generates HTML for the restart button, which is always shown, and the
     * stop or start button, depending on whether the service is active. The generated HTML
     * is used to build a responsive and interactive user interface for managing applications.
     */
    generateServiceControlHTML(serviceName, isServiceActive, multipleServices) {
        // HTML for the restart button, always shown.
        const restartButtonHtml = `
        <button class="rounded-md bg-amber-300 p-2 mx-2 cursor-pointer uppercase text-xs flex flex-row items-center justify-center font-semibold app-restart-link" data-service-name="${serviceName}">
            <svg class="w-4 h-4 fill-stone-800 hover:animate-spin" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M4.755 10.059a7.5 7.5 0 0112.548-3.364l1.903 1.903h-3.183a.75.75 0 100 1.5h4.992a.75.75 0 00.75-.75V4.356a.75.75 0 00-1.5 0v3.18l-1.9-1.9A9 9 0 003.306 9.67a.75.75 0 101.45.388zm15.408 3.352a.75.75 0 00-.919.53 7.5 7.5 0 01-12.548 3.364l-1.902-1.903h3.183a.75.75 0 000-1.5H2.984a.75.75 0 00-.75.75v4.992a.75.75 0 001.5 0v-3.18l1.9 1.9a9 9 0 0015.059-4.035.75.75 0 00-.53-.918z" clip-rule="evenodd"></path>
            </svg>
        </button>`;

        // HTML for the stop or start button, depending on whether the service is active.
        const controlButtonHtml = isServiceActive ?
            // Stop button for active services.
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
        // Separator for services, shown only if there are multiple services.
        const separator = multipleServices ? `<span class="mx-auto text-xs border-t text-gray-500 dark:text-gray-400">${serviceName}</span>` : '';

        // Combining the HTML parts into one string.
        return `${separator} <div class="grid grid-cols-2 divide-x bg-gray-100 dark:bg-transparent pt-1"> 
                ${restartButtonHtml} ${controlButtonHtml} </div>`;
    }

    /**
     * Generates the HTML for the pin/unpin link of an app.
     * @param {object} appDetails - The details of the app.
     * @param {object} preferences - User preferences, including pinned apps.
     * @returns {string} HTML string for the pin/unpin link.
     * 
     * This function generates HTML for the pin/unpin link, based on the pin status of the app.
     * The generated HTML is used to build a responsive and interactive user interface for managing applications.
     * The pin/unpin link is used to pin or unpin an app, based on the user's preferences.
     * The link is displayed as 'Pin' if the app is not currently pinned, and 'Unpin' if it is pinned.
     * Clicking the link triggers a PATCH request to the preferences API to pin or unpin the app.
     * The link is updated based on the response from the API.
     */
    generatePinLink(appDetails, preferences) {
        // Check if the app is currently pinned.
        const isPinned = this.isAppPinned(appDetails.id, preferences);
        // Determine the action text based on the pin status.
        const pinAction = isPinned ? 'Unpin' : 'Pin';

        // Return HTML string for the pin/unpin button.
        return `
            <button class="pin-link block w-full px-4 py-2 text-sm hover:bg-gray-100" 
                    data-service-id="${appDetails.id}">
                ${pinAction}
            </button>`;
        }

    /**
     * Populates the options menu for an app with the relevant HTML content.
     * @param {string} appName - The name of the app.
     * @param {object} appDetails - Details of the app, used to generate menu content.
     * 
     * This function generates the HTML for the options menu of an app and sets it as the content of the menu.
     * It is used to build a responsive and interactive user interface for managing applications.
     * The menu is populated with options to open, pin/unpin, and uninstall the application.
     * It also dynamically generates control buttons for the main service and any child services,
     * based on their active or inactive status.
     */
    showOptionsMenu(appName, appDetails) {
        // Generate the menu HTML based on app details.
        const menuHtml = this.generateOptionsMenu(appName, appDetails);
        // Locate the container for the app's options menu.
        const menuContainer = document.getElementById(appName + '_options_menu');
        // Set the generated HTML as the content of the menu container.
        menuContainer.innerHTML = menuHtml;
    }
}
