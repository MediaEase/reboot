import { slugify, CodeBlock } from "../../utils.js";
import ButtonGenerator from "./elements/ButtonGenerator.js";
import { OptionsMenu } from "./elements/OptionsMenu.js";

/**
 * AppCardUI is responsible for creating and managing the UI elements of application cards.
 * 
 * @property {HTMLElement} container - The container where the cards will be appended.
 * @property {Object} optionsMenu - The options menu for each application.
 * 
 * @method initialize - Initializes the application cards using provided data.
 * @method createCard - Creates a card element for a given application.
 * @method generatePorts - Generates server and web port numbers for a given application detail.
 * @method showModalDetails - Displays a modal with detailed information about an application.
 * @method setupEventListeners - Sets up event listeners for the card elements.
 */
class AppCardUI {
    /**
     * Constructs the AppCardUI object.
     * @param {string} containerId - The ID of the container where the cards will be appended.
     */
    constructor(containerId = 'grid') {
        this.container = document.getElementById(containerId);
        if (!this.container) {
            console.error('Grid container not found');
            throw new Error('Grid container not found');
        }
        this.translator = window.translator;
    }

    /**
     * Initializes the application cards using provided data.
     * @param {Object} appsData - Data about the applications to be displayed.
     * @param {Object} preferencesData - User's preferences data.
     */
    initialize(appsData, preferencesData) {
        Object.entries(appsData).forEach(([appName, appDetail]) => {
            const cardElement = this.createCard(appName, appDetail, preferencesData);
            this.container.appendChild(cardElement);
        });
        this.setupEventListeners();
    }

    /**
     * Creates a card element for a given application.
     * @param {string} appName - The name of the application.
     * @param {Object} appDetail - Detailed data about the application.
     * @param {Object} preferencesData - User's preferences data.
     * @returns {HTMLElement} - The card element.
     */
    createCard(appName, appDetail, preferencesData) {
        const card = document.createElement('div');
        card.className = 'card-container mb-16 lg:mb-0 p-2 relative group';
        card.setAttribute('data-app-panel', appDetail.name);
        const buttonGenerator = new ButtonGenerator(appDetail.name, 'card');
        const settingsButton = buttonGenerator.generateButton('settings');
        const optionsButton = buttonGenerator.generateButton('options');
        const optionsMenuInstance = new OptionsMenu();
        const optionsMenu = optionsMenuInstance.generateOptionsMenu(appDetail.name, appDetail, preferencesData);
        const appNameSanitized = slugify(appDetail.name);
        const hasChildServices = appDetail.services.length > 1;
        const logoPath = `/uploads/soft_logos/${slugify(appDetail.name)}.png`;
        const appApiKey = appDetail.services[0]?.apikey || this.translator.trans('No API Key');
        const [serverPort, webPort] = this.generatePorts(appDetail);
    
        const allServicesInactive = appDetail.services.every(service => service.status !== 'active');
        const anyServiceInactive = appDetail.services.some(service => service.status !== 'active');
    
        const [liquidClass, ringClass] = allServicesInactive
            ? ['liquid-red', 'ring-red-600']
            : anyServiceInactive
                ? ['liquid-amber', 'ring-amber-500']
                : ['liquid-green', 'ring-green-500'];

        card.innerHTML = `
            <div class="block h-full card-blur shadow-md rounded-xl hover:shadow-xl hover:scale-105 transition duration-300 ease-in-out p-6" data-app-panel="${appNameSanitized}">
                <div class="flex items-center justify-between">
                    ${settingsButton}
                    ${optionsButton}
                </div>
                ${optionsMenu}
                <a href="/apps/${appNameSanitized}">
                    <div class="mt-6 text-center text-white relative">
                        <div class="inline-block overflow-hidden scale-105 translate-x-4 skew-y-3 md:transform-none p-1">
                            <div class="relative w-[65px]">
                                <div id="${appDetail.name}-ring" class="absolute inset-0 rounded-full ring-2 ${ringClass} animate-pulse app-ring"></div>
                                <img class="h-auto max-w-[60px] p-1 mx-auto rounded-full" src="${logoPath}" alt="${appDetail.name}">
                            </div>
                        </div>
                    </div>
                    <h5 class="mb-4 text-xl font-bold">
                        ${appDetail.name}
                    </h5>
                </a>
                <div class="flex justify-between border-t border-gray-300 mx-8 my-2">
                    <div class="flex-none my-1">
                        <span class="text-sm sm:text-base inline-flex text-left items-center text-white rounded-lg">
                            ${this.translator.trans('API Key')}
                        </span>
                    </div>
                    ${CodeBlock(appNameSanitized, 'apikey', appApiKey)}
                </div>
                ${hasChildServices ? `
                    <div class="flex justify-between border-t border-gray-300 mx-8 my-2">
                        <div class="flex-none my-1">
                            <span class="text-sm sm:text-base inline-flex text-left items-center text-white rounded-lg">
                                ${this.translator.trans('Server Port')}
                            </span>
                        </div>
                        ${CodeBlock(appNameSanitized, 'serverPort', serverPort)}
                    </div>
                    <div class="flex justify-between border-t border-gray-300 mx-8 my-2">
                        <div class="flex-none my-1">
                            <span class="text-sm sm:text-base inline-flex text-left items-center text-white rounded-lg">
                                ${this.translator.trans('Web Port')}
                            </span>
                        </div>
                        ${CodeBlock(appNameSanitized, 'webPort', webPort)}
                    </div>` : `
                    <div class="flex justify-between border-t border-gray-300 mx-8 my-2">
                        <div class="flex-none my-1">
                            <span class="text-sm sm:text-base inline-flex text-left items-center text-white rounded-lg">
                                ${this.translator.trans('Port')}
                            </span>
                        </div>
                        ${CodeBlock(appNameSanitized, 'port', serverPort)}
                    </div>`
            }
                <div id="${appNameSanitized}-liquid" class="liquid ${liquidClass}"></div>
            </div>
        `;

        card.querySelector('.settings-button').addEventListener('click', (event) => {
            event.stopPropagation();
            this.showModalDetails(appName, appDetail);
        });

    return card;
    }

    /**
     * Generates server and web port numbers for a given application detail.
     * Iterates through each service of the application to determine the server and web ports.
     * 
     * @param {Object} appDetail - Detailed data about the application, including its services.
     * @returns {[string, string]} - An array where the first element is the server port and the second element is the web port. 
     *                               Returns 'N/A' for each port if not applicable.
     */
    generatePorts(appDetail) {
        let serverPort = this.translator.trans('N/A');
        let webPort = this.translator.trans('N/A');
        appDetail.services.forEach(service => {
            if (service.name.includes('-server')) {
                serverPort = service.ports[0]?.default || serverPort;
            }
            if (service.name.includes('-web')) {
                webPort = service.ports[0]?.default || webPort;
            } else {
                serverPort = service.ports[0]?.default || serverPort;
            }
        });
        return [serverPort, webPort];
    }

    /**
     * Displays a modal with detailed information about an application.
     * @param {string} appName - The name of the application.
     * @param {Object} appDetails - Detailed data about the application.
     */
    showModalDetails(appName, appDetails) {
        const modal = document.getElementById('appModal');
        modal.innerHTML = '<ul>' + appDetails.map(detail => `<li>${this.translator.trans(detail)}</li>`).join('') + '</ul>';
    }

    /**
     * Sets up event listeners for the card elements.
     */
    setupEventListeners() {
        const optionsMenuInstance = new OptionsMenu();
        document.addEventListener('click', (event) => optionsMenuInstance.onDocumentClick(event));
        document.addEventListener('click', (event) => optionsMenuInstance.onButtonMenuClick(event));
    }
}

export default AppCardUI;
