// table.js
import { Datatable } from "tw-elements";
import { CodeBlock, slugify } from "../../utils.js";
import { OptionsMenu } from "./OptionsMenu.js";
import ButtonGenerator from "./AppButtons.js";

export function initTable(appsData, preferencesData) {
    const data = {
        columns: [
            { label: 'Name', field: 'name' },
            { label: 'Version', field: 'version' },
            { label: 'Status', field: 'status' },
            { label: 'API Key/Socket', field: 'apiKey' },
            { label: 'Ports', field: 'ports' },
            { label: 'Actions', field: 'actions', sort: false },
        ],
        rows: transformData(appsData, preferencesData)
    };
    const datatableElement = document.getElementById('my-app-list');
    if (!datatableElement) {
        console.error('Datatable element not found');
        return;
    }
    const datatable = new Datatable(datatableElement, data, { hover: true, pagination: true, entriesOptions: [10, 20, 30, 75], fullPagination: true }, { hoverRow: 'hover:bg-gray-300 hover:text-black', column: 'pl-1 text-clip overflow-hidden text-[#212529] dark:text-white', rowItem: 'whitespace-nowrap text-clip overflow-auto px-[1.4rem] border-neutral-200 dark:border-neutral-500' });
    setupAdvancedSearch(datatable);
    attachEventListeners();
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
    const handleSearchEvent = (e) => {
        search(advancedSearchInput.value);
    };

    advancedSearchButton.addEventListener("click", handleSearchEvent);
    advancedSearchInput.addEventListener("input", handleSearchEvent);
}

function transformData(appData, preferencesData) {
    const transformed = [];
    for (const [appName, services] of Object.entries(appData)) {
        services.forEach(service => {
            const hasMultipleServices = services.length > 1;
            const serviceNames = formatServiceNameDisplay(appName, service, hasMultipleServices);
            const combinedServices = [service].concat(service.childServices || []);

            transformed.push({
                name: `<img src="/soft_logos/${slugify(appName)}.png" alt="${slugify(appName)} logo" style="display:inline-block; width: 30px; height: 30px;"> ${serviceNames}`,
                version: generateColumnData(combinedServices, 'version'),
                status: generateColumnData(combinedServices, 'status', generateStatusBadge),
                apiKey: generateColumnData(combinedServices, 'apikey', CodeBlock),
                ports: generateColumnData(combinedServices, 'ports', CodeBlock),
                actions: createActionButtons(appName, service, preferencesData)
            });
        });
    }
    return transformed;
}


function generateStatusBadge(status) {
    return status === 'active'
        ? `<span class="inline-flex items-center justify-center w-6 h-6 me-2 text-xs font-semibold text-green-800 rounded-full">●</span>`
        : `<span class="inline-flex items-center justify-center w-6 h-6 me-2 text-xs font-semibold text-red-800 rounded-full">●</span>`;
}

function formatServiceNameDisplay(appName, appDetails) {
    let formattedDisplay = `<a href="${appDetails.configuration[0].root_url}">${toTitleCase(appName)}</a>`;
    const hasChildServices = appDetails.childServices && appDetails.childServices.length > 0;
    if (hasChildServices) {
        const mainServiceName = appDetails.name.split('@')[0];
        formattedDisplay += `<p class="text-sm">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&#10551; ${slugify(mainServiceName)}</p>`;
        appDetails.childServices.forEach(childService => {
            const childServiceName = childService.name.split('@')[0];
            formattedDisplay += `<p class="text-sm">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&#10551; ${slugify(childServiceName)}</p>`;
        });
    }

    return formattedDisplay;
}


function generateColumnData(services, columnName, formatter = (x) => x) {
    const isSpecialCase = columnName === 'ports' || columnName === 'apikey';
    const separator = isSpecialCase ? '' : '<br class="pt-1">';
    return services.map((service, index) => {
        let data = service[columnName] ? service[columnName] : 'N/A';
        const shouldAddBreak = index === 0 && services.length > 1;
        if (columnName === 'ports' && service.ports && service.ports[0]) {
            data = service.ports[0].default;
        }

        const formattedData = isSpecialCase ? CodeBlock(service.name, columnName, data) : formatter(data);
        return `${shouldAddBreak ? '<br class="pb-1">' : ''}` + formattedData;
    }).join(separator);
}

function toTitleCase(str) {
    if (typeof str !== 'string') {
        console.error('toTitleCase: Expected a string, got', str);
        return '';
    }
    return str.replace(/\b\w+/g, function (s) { return s.charAt(0).toUpperCase() + s.substr(1).toLowerCase(); });
}

function createActionButtons(appName, data, preferencesData) {
    const buttonGenerator = new ButtonGenerator(appName, 'list');
    const settingsButton = buttonGenerator.generateButton('settings');
    const optionsButton = buttonGenerator.generateButton('options');
    const optionsMenuInstance = new OptionsMenu();
    const hasChildServices = data.childServices && data.childServices.length > 0;
    const optionsMenu = optionsMenuInstance.generateOptionsMenu(appName, data, preferencesData, hasChildServices);

    return `
        <div class="flex items-center my-2">
            ${settingsButton}
            ${optionsButton}
        </div>
        ${optionsMenu}
    `;
}

function attachEventListeners() {
    const optionsMenuInstance = new OptionsMenu();
    document.addEventListener('click', (event) => optionsMenuInstance.onDocumentClick(event));
    document.addEventListener('click', (event) => optionsMenuInstance.onButtonMenuClick(event));
}
