import { Datatable } from "tw-elements";
import { generateOptionsMenu } from "./menus";

let datatableInstance = null;

export function initTable(fetchedData) {
    console.log(fetchedData);
    const data = {
        columns: [
            { label: 'Name', field: 'name' },
            { label: 'Version', field: 'version' },
            { label: 'Status', field: 'status' },
            { label: 'API Key/Socket', field: 'apiKey' },
            { label: 'Ports', field: 'ports' },
            { label: 'Actions', field: 'actions', sort: false },
        ],
        rows: transformData(fetchedData)
    };
    const customDatatable = document.getElementById("app-list-table");
    datatableInstance = new Datatable(customDatatable, data, { hover: true, pagination: true, entriesOptions: [10, 20, 30, 75], fullPagination: true }, { hoverRow: 'hover:bg-gray-300 hover:text-black' });
    const input = document.getElementById('app-finder');
    if (input) {
        document.getElementById("app-finder-button").addEventListener("click", () => {
            search(input.value, datatableInstance);
        });
        input.addEventListener("keydown", (e) => {
            if (e.keyCode === 13) {
                search(e.target.value, datatableInstance);
            }
        });
    } else {
        console.error("Advanced search input not found");
    }

}

function search(value, datatableInstance) {
    if (typeof datatableInstance.search === 'function') {
        let phrase = value.trim();
        let columns = ['name'];
        datatableInstance.search(phrase, columns);
    } else {
        console.error("Search method not found on datatable instance");
    }
}

export function updateTable(appsData) {
    if (!datatableInstance) {
        initTable(appsData);
        return;
    }
    const data = transformData(appsData);
    console.log(data);
    const rows = document.querySelectorAll('[data-te-field="status"]');
    rows.forEach((row, index) => {
        row.innerHTML = data[index].status;
    });
}

function generateStatusHTML(app) {
    const servicesEntries = Object.entries(app.paths.services);
    return servicesEntries
        .map((service, index) => {
            let statusBadge = service.status === 'active'
                ? `<span class="mt-1 inline-flex items-center justify-center w-6 h-6 me-2 text-xs font-semibold text-green-800 rounded-full">●</span>`
                : `<span class="my-1 inline-flex items-center justify-center w-6 h-6 me-2 text-xs font-semibold text-red-800 rounded-full">●</span>`;
            return `${index === 0 && servicesEntries.length > 1 ? '<br>' : ''}${statusBadge}`;
        })
        .join('<br>');
}

function transformData(appsData) {
    return Object.values(appsData).map(app => {
        const formattedName = toTitleCase(app.name);
        const servicesEntries = Object.entries(app.paths.services);
        const hasMultipleServices = servicesEntries.length > 1;

        let serviceNameDisplay = formattedName;
        if (hasMultipleServices) {
            serviceNameDisplay = `<span class="mb-1">${formattedName}</span><br>` +
                servicesEntries.map(([serviceName]) =>
                    `&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&#10551; ${toTitleCase(serviceName)}`)
                    .join('<br>');
        }

        let servicesStatus = generateStatusHTML(app);
        let ports = servicesEntries
            .map(([serviceName, service], index) => {
                let portType = hasMultipleServices
                    ? (serviceName.toLowerCase().includes('web') ? 'web' : 'server')
                    : '';
                return `${index === 0 && hasMultipleServices ? '<br>' : ''}` +
                    generateCodeBlock(serviceName, 'port', service.port);
            })
            .join('');

        const logoPathWithUnderscore = `${app.name.replace(/ /g, '-')}`;
        const logoPath = `/soft_logos/${logoPathWithUnderscore}.png`;
        let apiKey = generateCodeBlock(formattedName, app.apiKey);
        return {
            name: `<img src="${logoPath}" alt="${formattedName}" style="display:inline-block; width: 30px; height: 30px;"> ${serviceNameDisplay}`,
            version: app.currentVersion,
            status: servicesStatus,
            apiKey: apiKey,
            ports: ports,
            actions: `
            <div class="flex items-center">
                <a class="settings-button cursor-pointer text-sm font-medium text-gray-50"
                    data-te-ripple-init
                    data-te-ripple-color="light"
                    data-app-name=${formattedName}
                    data-te-action="settings"
                    id="${formattedName}_settings">
                    <svg class="w-6 h-6 fill-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                        <path d="M456.7 242.27l-26.08-4.2a8 8 0 01-6.6-6.82c-.5-3.2-1-6.41-1.7-9.51a8.08 8.08 0 013.9-8.62l23.09-12.82a8.05 8.05 0 003.9-9.92l-4-11a7.94 7.94 0 00-9.4-5l-25.89 5a8 8 0 01-8.59-4.11q-2.25-4.2-4.8-8.41a8.16 8.16 0 01.7-9.52l17.29-19.94a8 8 0 00.3-10.62l-7.49-9a7.88 7.88 0 00-10.5-1.51l-22.69 13.63a8 8 0 01-9.39-.9c-2.4-2.11-4.9-4.21-7.4-6.22a8 8 0 01-2.5-9.11l9.4-24.75A8 8 0 00365 78.77l-10.2-5.91a8 8 0 00-10.39 2.21l-16.64 20.84a7.15 7.15 0 01-8.5 2.5s-5.6-2.3-9.8-3.71A8 8 0 01304 87l.4-26.45a8.07 8.07 0 00-6.6-8.42l-11.59-2a8.07 8.07 0 00-9.1 5.61l-8.6 25.05a8 8 0 01-7.79 5.41h-9.8a8.07 8.07 0 01-7.79-5.41l-8.6-25.05a8.07 8.07 0 00-9.1-5.61l-11.59 2a8.07 8.07 0 00-6.6 8.42l.4 26.45a8 8 0 01-5.49 7.71c-2.3.9-7.3 2.81-9.7 3.71-2.8 1-6.1.2-8.8-2.91l-16.51-20.34A8 8 0 00156.75 73l-10.2 5.91a7.94 7.94 0 00-3.3 10.09l9.4 24.75a8.06 8.06 0 01-2.5 9.11c-2.5 2-5 4.11-7.4 6.22a8 8 0 01-9.39.9L111 116.14a8 8 0 00-10.5 1.51l-7.49 9a8 8 0 00.3 10.62l17.29 19.94a8 8 0 01.7 9.52q-2.55 4-4.8 8.41a8.11 8.11 0 01-8.59 4.11l-25.89-5a8 8 0 00-9.4 5l-4 11a8.05 8.05 0 003.9 9.92L85.58 213a7.94 7.94 0 013.9 8.62c-.6 3.2-1.2 6.31-1.7 9.51a8.08 8.08 0 01-6.6 6.82l-26.08 4.2a8.09 8.09 0 00-7.1 7.92v11.72a7.86 7.86 0 007.1 7.92l26.08 4.2a8 8 0 016.6 6.82c.5 3.2 1 6.41 1.7 9.51a8.08 8.08 0 01-3.9 8.62L62.49 311.7a8.05 8.05 0 00-3.9 9.92l4 11a7.94 7.94 0 009.4 5l25.89-5a8 8 0 018.59 4.11q2.25 4.2 4.8 8.41a8.16 8.16 0 01-.7 9.52l-17.29 19.96a8 8 0 00-.3 10.62l7.49 9a7.88 7.88 0 0010.5 1.51l22.69-13.63a8 8 0 019.39.9c2.4 2.11 4.9 4.21 7.4 6.22a8 8 0 012.5 9.11l-9.4 24.75a8 8 0 003.3 10.12l10.2 5.91a8 8 0 0010.39-2.21l16.79-20.64c2.1-2.6 5.5-3.7 8.2-2.6 3.4 1.4 5.7 2.2 9.9 3.61a8 8 0 015.49 7.71l-.4 26.45a8.07 8.07 0 006.6 8.42l11.59 2a8.07 8.07 0 009.1-5.61l8.6-25a8 8 0 017.79-5.41h9.8a8.07 8.07 0 017.79 5.41l8.6 25a8.07 8.07 0 009.1 5.61l11.59-2a8.07 8.07 0 006.6-8.42l-.4-26.45a8 8 0 015.49-7.71c4.2-1.41 7-2.51 9.6-3.51s5.8-1 8.3 2.1l17 20.94A8 8 0 00355 439l10.2-5.91a7.93 7.93 0 003.3-10.12l-9.4-24.75a8.08 8.08 0 012.5-9.12c2.5-2 5-4.1 7.4-6.21a8 8 0 019.39-.9L401 395.66a8 8 0 0010.5-1.51l7.49-9a8 8 0 00-.3-10.62l-17.29-19.94a8 8 0 01-.7-9.52q2.55-4.05 4.8-8.41a8.11 8.11 0 018.59-4.11l25.89 5a8 8 0 009.4-5l4-11a8.05 8.05 0 00-3.9-9.92l-23.09-12.82a7.94 7.94 0 01-3.9-8.62c.6-3.2 1.2-6.31 1.7-9.51a8.08 8.08 0 016.6-6.82l26.08-4.2a8.09 8.09 0 007.1-7.92V250a8.25 8.25 0 00-7.27-7.73zM256 112a143.82 143.82 0 01139.38 108.12A16 16 0 01379.85 240H274.61a16 16 0 01-13.91-8.09l-52.1-91.71a16 16 0 019.85-23.39A146.94 146.94 0 01256 112zM112 256a144 144 0 0143.65-103.41 16 16 0 0125.17 3.47L233.06 248a16 16 0 010 15.87l-52.67 91.7a16 16 0 01-25.18 3.36A143.94 143.94 0 01112 256zm144 144a146.9 146.9 0 01-38.19-4.95 16 16 0 01-9.76-23.44l52.58-91.55a16 16 0 0113.88-8H379.9a16 16 0 0115.52 19.88A143.84 143.84 0 01256 400z"></path>
                    </svg>
                </a>
                <a
                    class="options-button cursor-pointer font-medium text-blue-500 transition-all duration-300 group-hover:text-blue-500/80"
                    data-te-ripple-init
                    data-te-ripple-color="light"
                    data-app-name=${formattedName}
                    data-te-action="options"
                    id="${formattedName}_options">
                    <svg class="w6- h-6 fill-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10.5 6a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zm0 6a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zm0 6a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0z" clip-rule="evenodd"></path>
                    </svg>
                </a>
            </div>
            ` + generateOptionsMenu(formattedName),
        };
    });
}

function toTitleCase(str) {
    return str.replace(/\b\w+/g, function (s) { return s.charAt(0).toUpperCase() + s.substr(1).toLowerCase(); });
}

function isPort(value) {
    return /^\d{4,5}$/.test(value);
}

function generateCodeBlock(appName, data) {
    let id;
    if (isPort(data)) {
        id = `${appName}-port`;
    } else {
        id = `${appName}-apiKey`;
    }
    return `
        <div class="flex-none my-1">
            <code id="${id}" class="text-xs xs:text-base inline-flex text-left items-center space-x-4 bg-gray-800 text-white rounded-lg py-1 pl-6">
                <span class="text-yellow-500 text-xs text-clip">
                    ${data}
                </span>
                <button onclick="copyToClipboard('${id}')" class="copy-button">
                    <svg class="shrink-0 h-4 w-4 mr-2 first-letter:transition fill-white group-hover:fill-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="" aria-hidden="true">
                        <path d="M7.5 3.375c0-1.036.84-1.875 1.875-1.875h.375a3.75 3.75 0 013.75 3.75v1.875C13.5 8.161 14.34 9 15.375 9h1.875A3.75 3.75 0 0121 12.75v3.375C21 17.16 20.16 18 19.125 18h-9.75A1.875 1.875 0 017.5 16.125V3.375z"></path>
                        <path d="M15 5.25a5.23 5.23 0 00-1.279-3.434 9.768 9.768 0 016.963 6.963A5.23 5.23 0 0017.25 7.5h-1.875A.375.375 0 0115 7.125V5.25zM4.875 6H6v10.125A3.375 3.375 0 009.375 19.5H16.5v1.125c0 1.035-.84 1.875-1.875 1.875h-9.75A1.875 1.875 0 013 20.625V7.875C3 6.839 3.84 6 4.875 6z"></path>
                    </svg>
                </button>
            </code>
        </div>`;
}
