import { CodeBlock, slugify } from "../../utils.js";
import ButtonGenerator from "./AppButtons.js";
import { OptionsMenu } from "./OptionsMenu.js";

export function initCards(appsData, preferencesData) {
    const gridContainer = document.getElementById('grid');
    if (!gridContainer) {
        console.error('Grid container not found');
        return;
    }

    Object.entries(appsData).forEach(([appName, appDetails]) => {
        const cardElement = createCard(appName, appDetails[0], preferencesData);
        gridContainer.appendChild(cardElement);
    });
    attachEventListeners();
}

function createCard(appName, appDetail, preferencesData) {
    const card = document.createElement('div');
    card.className = 'card-container mb-16 lg:mb-0 p-2 relative group';
    card.setAttribute('data-app-panel', appName);
    const buttonGenerator = new ButtonGenerator(appName, 'card');
    const settingsButton = buttonGenerator.generateButton('settings');
    const optionsButton = buttonGenerator.generateButton('options');
    const optionsMenuInstance = new OptionsMenu();
    const hasChildServices = appDetail.childServices && appDetail.childServices.length > 0;
    const optionsMenu = optionsMenuInstance.generateOptionsMenu(appName, appDetail, preferencesData, hasChildServices);
    optionsMenuInstance.initPinLinkEventListeners();

    const logoPath = `/soft_logos/${slugify(appName)}.png`;
    const appApiKey = appDetail.apikey || 'No API Key';
    const appNameSanitized = slugify(appName);

    let serverPort = appDetail.ports[0]?.default || 'N/A';
    let webPort = hasChildServices ? appDetail.childServices[0].ports[0]?.default || 'N/A' : 'N/A';

    const allServicesInactive = appDetail.status !== 'active' && (!appDetail.childServices || appDetail.childServices.every(child => child.status !== 'active'));
    const anyServiceInactive = appDetail.status !== 'active' || (appDetail.childServices && appDetail.childServices.some(child => child.status !== 'active'));

    const [liquidClass, ringClass] = allServicesInactive
        ? ['liquid-red', 'ring-red-600']
        : (multipleServices && anyServiceInactive)
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
                            <div id="${appName}-ring" class="absolute inset-0 rounded-full ring-2 ${ringClass} animate-pulse app-ring"></div>
                            <img class="h-auto max-w-[60px] p-1 mx-auto rounded-full" src="${logoPath}" alt="${appName}">
                        </div>
                    </div>
                </div>
                <h5 class="mb-4 text-xl font-bold">
                    ${appName}
                </h5>
            </a>
            <div class="flex justify-between border-t border-gray-300 mx-8 my-2">
                <div class="flex-none my-1">
                    <span class="text-sm sm:text-base inline-flex text-left items-center text-white rounded-lg">
                        Api Key
                    </span>
                </div>
                ${CodeBlock(appNameSanitized, 'apikey', appApiKey)}
            </div>
            ${hasChildServices ? `
                <div class="flex justify-between border-t border-gray-300 mx-8 my-2">
                    <div class="flex-none my-1">
                        <span class="text-sm sm:text-base inline-flex text-left items-center text-white rounded-lg">
                            Server Port
                        </span>
                    </div>
                    ${CodeBlock(appNameSanitized, 'serverPort', serverPort)}
                </div>
                <div class="flex justify-between border-t border-gray-300 mx-8 my-2">
                    <div class="flex-none my-1">
                        <span class="text-sm sm:text-base inline-flex text-left items-center text-white rounded-lg">
                            Web Port
                        </span>
                    </div>
                    ${CodeBlock(appNameSanitized, 'webPort', webPort)}
                </div>` : `
                <div class="flex justify-between border-t border-gray-300 mx-8 my-2">
                    <div class="flex-none my-1">
                        <span class="text-sm sm:text-base inline-flex text-left items-center text-white rounded-lg">
                            Port
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
        showModalDetails(appName, appDetails);
    });

    return card;
}

async function showModalDetails(appName, appDetails) {
    const modal = document.getElementById('appModal');
    modal.innerHTML = '<ul>' + appDetails.map(detail => `<li>${detail}</li>`).join('') + '</ul>';
}

function attachEventListeners() {
    const optionsMenuInstance = new OptionsMenu();
    document.addEventListener('click', (event) => optionsMenuInstance.onDocumentClick(event));
    document.addEventListener('click', (event) => optionsMenuInstance.onButtonMenuClick(event));
}
