import { Datatable } from "tw-elements";

/**
 * Class responsible for managing the UI of the group table.
 */
class SettingGroupTableUI {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        if (!this.container) {
            console.error('Table container not found');
            throw new Error('Table container not found');
        }
        this.datatable = null;
        this.translator = window.translator;
    }

    initialize(groupsData) {
        const data = {
            columns: [
                { label: this.translator.trans('Name'), field: 'name' },
                { label: this.translator.trans('Apps'), field: 'apps' },
                { label: this.translator.trans('Actions'), field: 'actions', sort: false },
            ],
            rows: this.transformData(groupsData)
        };

        const datatableElement = document.getElementById('group-list');
        if (!datatableElement) {
            console.error('Datatable element not found');
            return;
        }

        this.datatable = new Datatable(datatableElement, data, {
            hover: true, pagination: true, entriesOptions: [10, 20, 30], fullPagination: true
        }, {
            hoverRow: 'hover:bg-gray-300 hover:text-black', 
            column: 'pl-1 text-clip overflow-hidden text-[#212529] dark:text-white', 
            rowItem: 'whitespace-nowrap text-clip overflow-auto px-[1.4rem] border-neutral-200 dark:border-neutral-500'
        });
    }

    transformData(groupsData) {
        return groupsData.map(group => ({
            name: group.name,
            description: group.description,
            apps: group.apps.join(', '),
            actions: this.createActionButtons(group.id)
        }));
    }

    createActionButtons(groupId) {
        return `<a href='/access-groups/${groupId}/edit'>${this.translator.trans('Edit')}</a> | <a href='#' onclick='deleteGroup(${groupId})'>${this.translator.trans('Delete')}</a>`;
    }
}

export default SettingGroupTableUI;
