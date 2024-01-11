// table.js
import { Datatable } from "tw-elements";
import { CodeBlock } from "../utils.js";
import { generateOptionsMenu, showOptionsMenu } from './appMenus.js';
import ButtonGenerator from "./appButtons.js";

export function initTable(appsData) {
    const data = {
        columns: [
            { label: 'Name', field: 'name' },
            { label: 'Version', field: 'version' },
            { label: 'Status', field: 'status' },
            { label: 'API Key/Socket', field: 'apiKey' },
            { label: 'Ports', field: 'ports' },
            { label: 'Actions', field: 'actions', sort: false },
        ],
        rows: transformData(appsData)
    };
    const datatableElement = document.getElementById('my-app-list');
    if (!datatableElement) {
        console.error('Datatable element not found');
        return;
    }
    const datatable = new Datatable(datatableElement, data, { hover: true, pagination: true, entriesOptions: [10, 20, 30, 75], fullPagination: true }, { hoverRow: 'hover:bg-gray-300 hover:text-black' });
    setupAdvancedSearch(datatable);
    addOptionsEventListeners;
}

function addOptionsEventListeners() {
    document.querySelectorAll('.options-button').forEach(button => {
        button.addEventListener('click', function () {
            const appName = this.getAttribute('data-app-name');
            showOptionsMenu(appName);
        });
    });
}

function setupAdvancedSearch(datatable) {
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
    advancedSearchButton.addEventListener("click", () => {
        search(advancedSearchInput.value);
    });
    advancedSearchInput.addEventListener("keydown", (e) => {
        if (e.keyCode === 13) {
            search(e.target.value);
        }
    });
}

function transformData(appData) {
    const transformed = [];

    for (const [appName, appDetails] of Object.entries(appData.apps)) {
        let services = [];
        if (Array.isArray(appDetails.services)) {
            services = appDetails.services.map(serviceName => {
                const serviceDetails = appDetails.paths.services[serviceName];
                return {
                    name: serviceName,
                    status: serviceDetails ? serviceDetails.status : 'unknown',
                    port: serviceDetails ? serviceDetails.port : 'N/A'
                };
            });
        }
        transformed.push({
            name: `<img src="/soft_logos/${slugify(appName)}.png" alt="${slugify(appName)} logo" style="display:inline-block; width: 30px; height: 30px;"> ${formatServiceNameDisplay(services, appName)}`,
            version: appDetails.currentVersion,
            status: generateStatus(services),
            apiKey: CodeBlock(appName, "apikey", appDetails.apiKey),
            services: services,
            ports: generatePorts(services),
            actions: createActionButtons(appName, 'list') + generateOptionsMenu(appName, 'list')
        });
    }
    return transformed;
}

export function generateStatus(services) {
    return services
        .map((service, index) => {
            let serviceName = service.name.split('@')[0];
            let statusBadge = service.status === 'active'
                ? `<span id="${serviceName}-status" class="mt-1 inline-flex items-center justify-center w-6 h-6 me-2 text-xs font-semibold text-green-800 rounded-full">●</span>`
                : `<span id="${serviceName}-status" class="my-1 inline-flex items-center justify-center w-6 h-6 me-2 text-xs font-semibold text-red-800 rounded-full">●</span>`;
            return `${index === 0 && services.length > 1 ? '<br>' : ''}${statusBadge}`;
        })
        .join('<br>');
}

function formatServiceNameDisplay(services, appName) {
    const formattedName = toTitleCase(appName);
    const hasMultipleServices = services.length > 1;
    if (hasMultipleServices) {
        return `<span class="mb-1">${formattedName}</span><br>` +
            services.map(service =>
                `&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&#10551; ${toTitleCase(service.name)}`)
                .join('<br>');
    }
    return formattedName;
}

function generatePorts(services) {
    const hasMultipleServices = services.length > 1;
    return services.map((service, index) => {
        return `${index === 0 && hasMultipleServices ? '<br>' : ''}` +
            CodeBlock(service.name, 'port', service.port);
    }).join('');
}

function toTitleCase(str) {
    if (typeof str !== 'string') {
        console.error('toTitleCase: Expected a string, got', str);
        return '';
    }
    return str.replace(/\b\w+/g, function (s) { return s.charAt(0).toUpperCase() + s.substr(1).toLowerCase(); });
}

function slugify(str) {
    if (typeof str !== 'string') {
        console.error('slugify: Expected a string, got', str);
        return '';
    }
    return str.replace(/\s+/g, '-').toLowerCase();
}

function createActionButtons(appName, display) {
    const buttonGenerator = new ButtonGenerator(appName, display);
    return `
        <div class="flex items-center">
        ${buttonGenerator.generateButton('settings')}
        ${buttonGenerator.generateButton('options')}
        </div>
    `;
}
