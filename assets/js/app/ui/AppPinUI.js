class AppPinsUI {
    constructor(containerId = 'pinned-apps') {
        this.container = document.getElementById(containerId);
        if (!this.container) {
            console.error('Pinned Apps container not found');
            throw new Error('Pinned Apps container not found');
        }
    }

    initPins(preferencesData) {
        const pinnedAppsData = preferencesData.pinnedApps;
        if (pinnedAppsData && pinnedAppsData.length > 0) {
            this.renderPinnedApps(pinnedAppsData);
        }
    }

    renderPinnedApps(pinnedAppsData) {
        this.container.innerHTML = '';

        pinnedAppsData.forEach(app => {
            const appLink = document.createElement('a');
            appLink.href = app.configuration[0].root_url.toLowerCase();
            appLink.classList.add('flex', 'items-center', 'justify-center', 'w-12', 'h-12', 'mt-2', 'rounded', 'hover:bg-gray-700', 'hover:text-gray-300');

            const appImage = document.createElement('img');
            appImage.src = `/soft_logos/${app.name}.png`;
            appImage.alt = `${app.name} logo`;
            appImage.classList.add('w-8', 'h-8', 'rounded-full');

            appLink.appendChild(appImage);
            this.container.appendChild(appLink);
        });
    }

    updatePins(pins) {
        this.renderPinnedApps(pins);
    }
}

export default AppPinsUI;
