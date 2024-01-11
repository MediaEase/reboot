document.addEventListener('DOMContentLoaded', () => {
    const closeAllMenus = () => {
        document.querySelectorAll('[id$="_options_menu"]').forEach(menu => {
            menu.classList.add('hidden');
        });
    };
    document.addEventListener('click', (event) => {
        if (!event.target.matches('[id$="_options"], [id$="_options"] *')) {
            closeAllMenus();
        }
    });
    document.body.addEventListener('click', function (event) {
        const button = event.target.closest('[id$="_options"]');
        if (button) {
            const [appName, display] = button.id.split('_');
            const menuId = `${appName}_${display}_options_menu`;
            const menu = document.getElementById(menuId);
            closeAllMenus();
            if (menu) {
                menu.classList.toggle('hidden');
            } else {
                console.error('Menu not found:', menuId);
            }
        }
    });
});

export function generateOptionsMenu(appName, display) {
    return `
        <div class="hidden absolute right-0 mt-2 w-48 bg-white divide-y divide-gray-100 rounded-md shadow-lg z-50" id="${appName}_${display}_options_menu">
            <ul class="py-1 text-gray-700 text-center">
                <li>
                    <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-100">Open</a>
                </li>
                <li>
                    <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-100">Pin</a>
                </li>
                <li>
                    <a href="#" class="block px-4 py-2 text-sm hover:bg-red-300">Uninstall</a>
                </li>
                <div class="flex flex-col w-full relative bottom-0 app-panel-popover-footer">
                    <div class="grid grid-cols-2 border-t divide-x bg-gray-100 dark:bg-transparent pt-1">
                        <button class="rounded-md bg-amber-300 p-2 mx-2 cursor-pointer uppercase text-xs flex flex-row items-center justify-center font-semibold app-restart-link" target="_blank">
                            <svg class="w-4 h-4 fill-stone-800 hover:animate-spin" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M4.755 10.059a7.5 7.5 0 0112.548-3.364l1.903 1.903h-3.183a.75.75 0 100 1.5h4.992a.75.75 0 00.75-.75V4.356a.75.75 0 00-1.5 0v3.18l-1.9-1.9A9 9 0 003.306 9.67a.75.75 0 101.45.388zm15.408 3.352a.75.75 0 00-.919.53 7.5 7.5 0 01-12.548 3.364l-1.902-1.903h3.183a.75.75 0 000-1.5H2.984a.75.75 0 00-.75.75v4.992a.75.75 0 001.5 0v-3.18l1.9 1.9a9 9 0 0015.059-4.035.75.75 0 00-.53-.918z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                        <button id="start-stop-button" class="rounded-md p-2 mx-2 bg-green-300 cursor-pointer uppercase text-xs flex flex-row items-center justify-center font-semibold app-stop-link" target="_blank">
                            <svg class="w-4 h-4 fill-stone-800" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M4.5 5.653c0-1.426 1.529-2.33 2.779-1.643l11.54 6.348c1.295.712 1.295 2.573 0 3.285L7.28 19.991c-1.25.687-2.779-.217-2.779-1.643V5.653z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </ul>
        </div>`;
}

export function showOptionsMenu(appName) {
    const menuHtml = generateOptionsMenu(appName);
    const menuContainer = document.getElementById(appName + '_options_menu');
    menuContainer.innerHTML = menuHtml;
}

window.showOptionsMenu = showOptionsMenu;
