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
        Object.entries(appStatusData).forEach(([appName, appDetails]) => {
            const multipleServices = appDetails.length >= 2;
            const appNameSlug = appName.replace(/\s+/g, '-').toLowerCase();;
            const appCard = document.querySelector(`[data-app-panel="${appNameSlug}"]`);
            if (appCard) {
                const allServicesInactive = appDetails.every(service => service.status !== 'active');
                const anyServiceInactive = appDetails.some(service => service.status !== 'active');
                const [liquidClass, ringClass] = allServicesInactive
                    ? ['liquid-red', 'ring-red-600']
                    : (multipleServices && anyServiceInactive)
                        ? ['liquid-amber', 'ring-amber-500']
                        : ['liquid-green', 'ring-green-500'];
                const statusRing = appCard.querySelector('.app-ring');
                const statusLiquid = appCard.querySelector(`#${appNameSlug}-liquid`);
                if (statusRing) {
                    statusRing.className = `absolute inset-0 rounded-full ring-2 ${ringClass} animate-pulse app-ring`;
                }
                if (statusLiquid) {
                    statusLiquid.className = `liquid ${liquidClass}`;
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
