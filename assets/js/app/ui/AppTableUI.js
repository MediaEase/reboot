import { Datatable } from "tw-elements";
import { slugify, CodeBlock } from "../../utils.js";
import { OptionsMenu } from "./elements/OptionsMenu.js";
import ButtonGenerator from "./elements/ButtonGenerator.js";

/**
 * Class responsible for managing the UI of the application table.
 * 
 * @property {HTMLElement} container - The DOM element where the application table will be displayed.
 * 
 * @method initialize - Initializes and creates the datatable with the provided application data and user preferences.
 * @method transformData - Transforms application data into a format suitable for rendering in the datatable.
 * @method setupAdvancedSearch - Sets up advanced search functionality for the datatable.
 * @method generateColumnData - Generates column data based on the specified column name and optional formatter.
 * @method generateStatusBadge - Generates a status badge element based on the application's status.
 * @method formatServiceNameDisplay - Formats the display of service names for the application.
 * @method toTitleCase - Converts a string to title case.
 * @method createActionButtons - Creates action buttons for each application.
 * @method setupEventListeners - Sets up event listeners for the application table.
 */
class AppTableUI {
    /**
     * Constructs the AppTableUI object and initializes the container for the application table.
     *
     * @param {string} containerId - The ID of the DOM element where the application table will be displayed.
     * @throws Will throw an error if the container element is not found.
     */
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        if (!this.container) {
            console.error('Table container not found');
            throw new Error('Table container not found');
        }
        this.datatable = null;
        this.translator = window.translator;
    }

    /**
     * Initializes and creates the datatable with the provided application data and user preferences.
     *
     * @param {Object[]} appsData - Array of application data objects.
     * @param {Object} preferencesData - User preferences data.
     */
    initialize(appsData, preferencesData) {
        const data = {
            columns: [
                { label: this.translator.trans('Name'), field: 'name' },
                { label: this.translator.trans('Version'), field: 'version' },
                { label: this.translator.trans('Status'), field: 'status' },
                { label: this.translator.trans('API Key/Socket'), field: 'apiKey' },
                { label: this.translator.trans('Ports'), field: 'ports' },
                { label: this.translator.trans('Actions'), field: 'actions', sort: false },
            ],
            rows: this.transformData(appsData, preferencesData)
        };
        const datatableElement = document.getElementById('my-app-list');
        if (!datatableElement) {
            console.error('Datatable element not found');
            return;
        }
        const datatable = new Datatable(datatableElement, data, { hover: true, pagination: true, entriesOptions: [10, 20, 30, 75], fullPagination: true }, { hoverRow: 'hover:bg-gray-300 hover:text-black', column: 'pl-1 text-clip overflow-hidden text-[#212529] dark:text-white', rowItem: 'whitespace-nowrap text-clip overflow-auto px-[1.4rem] border-neutral-200 dark:border-neutral-500' });
        this.setupAdvancedSearch(datatable);
        this.setupEventListeners();
    }

    /**
     * Transforms application data into a format suitable for rendering in the datatable.
     *
     * @param {Object[]} appData - Array of application data objects.
     * @param {Object} preferencesData - User preferences data.
     * @returns {Object[]} Transformed data suitable for rendering in the datatable.
     */
    transformData(appData, preferencesData) {
        const transformed = [];
        appData.forEach(app => {
            const appName = app.name;
            const appServices = app.services || [];
            const formattedName = this.formatServiceNameDisplay(appName, appServices);
            const actions = this.createActionButtons(appName, app, preferencesData);
    
            const version = this.generateColumnData(appServices, 'version');
            const statusBadge = this.generateColumnData(appServices, 'status', this.generateStatusBadge);
            const apiKey = this.generateColumnData(appServices, 'apiKey', CodeBlock);
            const ports = this.generateColumnData(appServices, 'ports');
    
            transformed.push({
                name: `<img src="/uploads/soft_logos/${slugify(appName)}.png" alt="${slugify(appName)} logo" style="display:inline-block; width: 30px; height: 30px; margin-right: .25rem;"> ${formattedName}`,
                version: version,
                status: statusBadge,
                apiKey: apiKey,
                ports: ports,
                actions: actions
            });
        });
    
        return transformed;
    }

    /**
     * Sets up advanced search functionality for the datatable.
     *
     * @param {Datatable} datatable - The Datatable instance.
     */
    setupAdvancedSearch(datatable) {
        const advancedSearchInput = document.getElementById('app-finder');
        const advancedSearchButton = document.getElementById('app-finder-button');
    
        if (!advancedSearchInput || !advancedSearchButton) {
            console.error('search elements not found');
            return;
        }
        const search = (value) => {
            let [phrase, columns] = value.split(" in:").map(str => str.trim());
            columns = columns ? columns.split(",").map(str => str.toLowerCase().trim()) : null;
            datatable.search(phrase, columns);
        };
        const handleSearchEvent = (e) => {
            search(advancedSearchInput.value);
        };
    
        advancedSearchButton.addEventListener("click", handleSearchEvent);
        advancedSearchInput.addEventListener("input", handleSearchEvent);
    }

    /**
     * Generates column data based on the specified column name and optional formatter.
     *
     * @param {Object[]} services - Array of service objects.
     * @param {string} columnName - The name of the column for which data is being generated.
     * @param {Function} [formatter=(x) => x] - Optional formatter function to format the data.
     * @returns {string} Formatted column data as a string.
     */
    generateColumnData(services, columnName, formatter = (x) => x) {
        const isSpecialCase = columnName === 'ports' || columnName === 'apiKey';
        const separator = isSpecialCase ? ' ' : '<br class="pt-1">';
        return services.map((service, index) => {
            let data;
            if (columnName === 'ports' && service.ports && service.ports.length > 0) {
                data = service.ports.map(port => port.default).join(', ');
            } else if (columnName === 'apiKey') {
                data = service.apikey && service.apikey !== '' ? service.apikey : this.translator.trans('N/A');
            } else {
                data = service[columnName] ? service[columnName] : this.translator.trans('N/A');
            }
            const breakLine = services.length > 1 && index === 0 ? '<br>' : '';
            const formattedData = isSpecialCase ? CodeBlock(service.name, columnName, data) : formatter(data);
    
            return breakLine + formattedData;
        }).join(separator);
    }   
    
    /**
     * Generates a status badge element based on the application's status.
     *
     * @param {string} status - The status of the application.
     * @returns {string} HTML string representing the status badge.
     */
    generateStatusBadge(status) {
        return status === 'active'
            ? `<span class="inline-flex items-center justify-center w-6 h-6 me-2 text-xs font-semibold text-green-800 rounded-full">●</span>`
            : `<span class="inline-flex items-center justify-center w-6 h-6 me-2 text-xs font-semibold text-red-800 rounded-full">●</span>`;
    }
    
    /**
     * Formats the display of service names for the application.
     *
     * @param {string} appName - The name of the application.
     * @param {Object[]} appServices - Array of service objects related to the application.
     * @returns {string} HTML string representing the formatted service names.
     */
    formatServiceNameDisplay(appName, appServices) {
        let formattedDisplay = `<a href="${appServices[0].configuration[0].root_url}">${this.toTitleCase(appName)}</a>`;
        if (appServices.length > 1) {
            appServices.forEach(service => {
                const serviceName = service.name.split('@')[0];
                formattedDisplay += `<p class="text-sm">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&#10551; ${slugify(serviceName)}</p>`;
            });
        }
    
        return formattedDisplay;
    }
    
    /**
     * Converts a string to title case.
     *
     * @param {string} str - The string to be converted to title case.
     * @returns {string} The string converted to title case.
     */
    toTitleCase(str) {
        if (typeof str !== 'string') {
            console.error('toTitleCase: Expected a string, got', str);
            return '';
        }
        return str.replace(/\b\w+/g, function (s) { return s.charAt(0).toUpperCase() + s.substr(1).toLowerCase(); });
    }
    
    /**
     * Creates action buttons for each application.
     *
     * @param {string} appName - The name of the application.
     * @param {Object} appDetail - Detailed information about the application.
     * @param {Object} preferencesData - User preferences data.
     * @returns {string} HTML string representing the action buttons.
     */
    createActionButtons(appName, appDetail, preferencesData) {
        const buttonGenerator = new ButtonGenerator(appName, 'list');
        const settingsButton = buttonGenerator.generateButton('settings');
        const optionsButton = buttonGenerator.generateButton('options');
        const optionsMenuInstance = new OptionsMenu();
        const optionsMenu = optionsMenuInstance.generateOptionsMenu(appDetail.name, appDetail, preferencesData);
    
        return `
            <div class="flex items-center my-2">
                ${settingsButton}
                ${optionsButton}
            </div>
            ${optionsMenu}
        `;
    }
    
    /**
     * Sets up event listeners for the application table.
     */
    setupEventListeners() {
        const optionsMenuInstance = new OptionsMenu();
        document.addEventListener('click', (event) => optionsMenuInstance.onDocumentClick(event));
        document.addEventListener('click', (event) => optionsMenuInstance.onButtonMenuClick(event));
    }
}

export default AppTableUI;
