/**
 * This class will update : the app table
 *                          the app cards
 * What should be updated : 
 * - the app table should change the status of the app (from green dot to red dot)
 * - the app cards should change the status of the app (from green cricle to red circle). Also the liquid should change color from liquid-green to liquid-red
 * - app cards and app table have contextual menus. The contextual menus should be updated as well (icon play should be replaced by icon stop and vice versa)
 */
// status.js
class AppStatusUpdater {
    constructor(appStatusData) {
        this.appTable = document.querySelector('#my-app-list');
        this.appCards = document.querySelectorAll('.app-card');
        this.contextualMenus = document.querySelectorAll('.contextual-menu');
        this.appStatusData = appStatusData;
    }

    updateStatus(appStatusData) {
        this.updateAppTable(appStatusData);
        this.updateAppCards(appStatusData);
        this.updateContextualMenus(appStatusData);
    }

    updateAppTable(appStatusData) {
        appStatusData.apps.forEach(app => {
            app.services.forEach(service => {
                let serviceName = service.name.split('@')[0];
                const statusElement = document.getElementById(`${serviceName}-status`);
                if (statusElement) {
                    let statusBadge = service.status === 'active'
                        ? `<span id="${serviceName}-status" class="mt-1 inline-flex items-center justify-center w-6 h-6 me-2 text-xs font-semibold text-green-800 rounded-full">●</span>`
                        : `<span id="${serviceName}-status" class="my-1 inline-flex items-center justify-center w-6 h-6 me-2 text-xs font-semibold text-red-800 rounded-full">●</span>`;
                    statusElement.outerHTML = statusBadge;
                }
            });
        });
    }

    updateAppCards(appStatusData) {
        appStatusData.apps.forEach(app => {
            const appName = app.name.toLowerCase().replace(' ', '');
            const appCard = document.querySelector(`[data-app-panel="${appName}"]`);
            if (appCard) {
                const allServicesActive = app.services.every(service => service.status === 'active');
                const statusRing = appCard.querySelector('.app-ring');
                if (statusRing) {
                    statusRing.className = allServicesActive ? 'absolute inset-0 rounded-full ring-2 ring-green-500 animate-pulse app-ring' : 'absolute inset-0 rounded-full ring-2 ring-red-500 animate-pulse app-ring';
                }
                const statusLiquid = appCard.querySelector(`#${appName}-liquid`);
                if (statusLiquid) {
                    statusLiquid.className = allServicesActive ? 'liquid liquid-green' : 'liquid liquid-red';
                }
            }
        });
    }


    updateContextualMenus(appStatusData) {
        // Logique pour mettre à jour les menus contextuels
        // Par exemple, changer l'icône de lecture en arrêt et vice versa
        // console.log(appStatusData);
    }
}

export default AppStatusUpdater;
