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
     * Constructs the LogsTableUI object and initializes the container for the logs table.
     *
     * @param {string} containerId - The ID of the DOM element where the logs table will be displayed.
     * @throws Will throw an error if the container element is not found.
     */
    constructor(containerId) {
        this.containerId = containerId;
        this.container = document.getElementById(containerId);
        if (!this.container) {
            console.error('Table container not found');
            throw new Error('Table container not found');
        }
    }

    /**
     * Initializes and creates the datatable with the provided logs data and user preferences.
     *
     * @param {Object[]} logsData - Array of logs data objects.
     */
    initialize(logsData) {
        const data = {
            columns: [
                { label: 'ID', field: 'id' },
                { label: 'Timestamp', field: 'timestamp' },
                { label: 'Type', field: 'type' },
                { label: 'Content', field: 'content' },
                { label: 'IP Address', field: 'ip_address' },
            ],
            rows: this.transformData(logsData)
        };
        const datatableElement = document.getElementById(this.containerId);
        if (!datatableElement) {
            console.error('Datatable element not found');
            return;
        }
        this.datatable = new Datatable(datatableElement, data, { hover: true, pagination: true, entriesOptions: [10, 20, 30, 75], fullPagination: true }, { hoverRow: 'hover:bg-gray-300 hover:text-black', column: 'pl-1 text-clip overflow-hidden text-[#212529] dark:text-white', rowItem: 'whitespace-nowrap text-clip overflow-auto px-[1.4rem] border-neutral-200 dark:border-neutral-500' });
        this.setupAdvancedSearch(this.datatable);
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
            timestamp: new Date(log.timestamp).toLocaleString(),
            type: log.type,
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
}

export default AccessLogsTableUI;
