import { Datatable } from "tw-elements";

/**
 * Class responsible for managing the UI of the logs table.
 * 
 * @property {HTMLElement} container - The DOM element where the logs table will be displayed.
 * 
 * @method initialize - Initializes and creates the datatable with the provided logs data and user preferences.
 * @method transformData - Transforms logs data into a format suitable for rendering in the datatable.
 * @method setupAdvancedSearch - Sets up advanced search functionality for the datatable.
 */
class AccessLogsTableUI {
    /**
     * Constructs the AccessLogsTableUI object and initializes the container for the logs table.
     *
     * @param {string} containerId - The ID of the DOM element where the logs table will be displayed.
     * @throws Will throw an error if the container element is not found.
     */
    constructor(containerId) {
        this.containerId = containerId;
        this.container = document.getElementById(containerId);
        this.translator = window.translator;
        if (!this.container) {
            console.error('Table container not found');
            throw new Error('Table container not found');
        }
        this.datatable = null;
    }

    /**
     * Initializes and creates the datatable with the provided logs data.
     *
     * @param {Object} logsData - Object containing logs data, pagination information, and other metadata.
     */
    initialize(logsData) {
        const logsArray = Array.isArray(logsData.logs) ? logsData.logs : Object.values(logsData.logs);
        
        if (!logsArray || !Array.isArray(logsArray)) {
            console.error('Expected logsData to contain an array of logs, got:', logsData);
            return;
        }

        const data = {
            columns: [
                { label: this.translator.trans('Type'), field: 'type', maxWidth: '5%' },
                { label: this.translator.trans('Content'), field: 'content', maxWidth: '70%' },
                { label: this.translator.trans('Timestamp'), field: 'createdAt', maxWidth: '10%' },
                { label: this.translator.trans('IP Address'), field: 'ip_address', maxWidth: '10%' },
            ],
            rows: this.transformData(logsArray)
        };

        const datatableElement = document.getElementById(this.containerId);
        if (!datatableElement) {
            console.error('Datatable element not found');
            return;
        }

        const datatable = new Datatable(datatableElement, data, {
            hover: true,
            pagination: true,
            entriesOptions: [10, 20, 30, 75],
            fullPagination: true
        }, {
            hoverRow: 'hover:bg-gray-300 hover:text-black',
            column: 'pl-1 text-clip overflow-hidden text-[#212529] dark:text-white',
            rowItem: 'whitespace-nowrap text-clip overflow-auto px-[1.4rem] border-neutral-200 dark:border-neutral-500'
        });

        this.setupAdvancedSearch(datatable);
    }

    /**
     * Transforms logs data into a format suitable for rendering in the datatable.
     *
     * @param {Object[]} logsData - Array of logs data objects.
     * @returns {Object[]} Transformed data suitable for rendering in the datatable.
     */
    transformData(logsData) {
        return logsData.map(log => ({
            id: log.id,
            createdAt: new Date(log.createdAt).toLocaleString(),
            type: log.logType,
            content: log.content,
            ip_address: log.ip_address,
        }));
    }

    /**
     * Sets up advanced search functionality for the datatable.
     *
     * @param {Datatable} datatable - The Datatable instance.
     */
    setupAdvancedSearch(datatable) {
        const advancedSearchInput = document.getElementById('log-finder');
        const advancedSearchButton = document.getElementById('log-finder-button');
    
        if (!advancedSearchInput || !advancedSearchButton) {
            console.error('search elements not found: input:', advancedSearchInput, 'button:', advancedSearchButton);
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
}

export default AccessLogsTableUI;
