
import { Datatable } from "tw-elements";
import { slugify, CodeBlock } from "../../utils.js";
import { OptionsMenu } from "./OptionsMenu.js";
import ButtonGenerator from "./elements/ButtonGenerator.js";

class AppTableUI {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        if (!this.container) {
            console.error('Table container not found');
            throw new Error('Table container not found');
        }
        this.datatable = null;
    }

    initTable(appsData, preferencesData) {
        const data = {
            columns: [
                { label: 'Name', field: 'name' },
                { label: 'Version', field: 'version' },
                { label: 'Status', field: 'status' },
                { label: 'API Key/Socket', field: 'apiKey' },
                { label: 'Ports', field: 'ports' },
                { label: 'Actions', field: 'actions', sort: false },
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
        this.attachEventListeners();
    }

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
                name: `<img src="/soft_logos/${slugify(appName)}.png" alt="${slugify(appName)} logo" style="display:inline-block; width: 30px; height: 30px;"> ${formattedName}`,
                version: version,
                status: statusBadge,
                apiKey: apiKey,
                ports: ports,
                actions: actions
            });
        });
    
        return transformed;
    }

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

    generateColumnData(services, columnName, formatter = (x) => x) {
        const isSpecialCase = columnName === 'ports' || columnName === 'apiKey';
        const separator = isSpecialCase ? ' ' : '<br class="pt-1">';
        return services.map((service, index) => {
            let data;
            if (columnName === 'ports' && service.ports && service.ports.length > 0) {
                data = service.ports.map(port => port.default).join(', ');
            } else if (columnName === 'apiKey') {
                data = service.apikey && service.apikey !== '' ? service.apikey : 'N/A';
            } else {
                data = service[columnName] ? service[columnName] : 'N/A';
            }
            const breakLine = services.length > 1 && index === 0 ? '<br>' : '';
            const formattedData = isSpecialCase ? CodeBlock(service.name, columnName, data) : formatter(data);
    
            return breakLine + formattedData;
        }).join(separator);
    }   
    
    generateStatusBadge(status) {
        return status === 'active'
            ? `<span class="inline-flex items-center justify-center w-6 h-6 me-2 text-xs font-semibold text-green-800 rounded-full">●</span>`
            : `<span class="inline-flex items-center justify-center w-6 h-6 me-2 text-xs font-semibold text-red-800 rounded-full">●</span>`;
    }
    
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
    
    toTitleCase(str) {
        if (typeof str !== 'string') {
            console.error('toTitleCase: Expected a string, got', str);
            return '';
        }
        return str.replace(/\b\w+/g, function (s) { return s.charAt(0).toUpperCase() + s.substr(1).toLowerCase(); });
    }
    
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
    
    attachEventListeners() {
        const optionsMenuInstance = new OptionsMenu();
        document.addEventListener('click', (event) => optionsMenuInstance.onDocumentClick(event));
        document.addEventListener('click', (event) => optionsMenuInstance.onButtonMenuClick(event));
    }
}

export default AppTableUI;
