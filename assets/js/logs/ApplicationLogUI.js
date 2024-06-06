import { fetchData } from '../utils.js';

/**
 * Class responsible for managing the UI of the application logs.
 * 
 * @property {HTMLElement} container - The DOM element where the logs will be displayed.
 * 
 * @method initialize - Initializes and creates the log display with the provided file content and user actions.
 * @method refreshLogs - Refreshes the log content from the input path.
 * @method handleFetchLogs - Handles fetching logs from the input path.
 * @method handleDownload - Handles the download of the fetched log content.
 * @method setupAutoRefresh - Sets up the auto-refresh functionality.
 */
class ApplicationLogUI {
    /**
     * Constructs the ApplicationLogUI object and initializes the container for the logs.
     *
     * @param {string} containerId - The ID of the DOM element where the logs will be displayed.
     * @throws Will throw an error if the container element is not found.
     */
    constructor(containerId) {
        this.containerId = containerId;
        this.container = document.getElementById(containerId);
        if (!this.container) {
            console.error('Log container not found');
            throw new Error('Log container not found');
        }

        this.logFilePathInput = document.getElementById('log-file-path');
        this.logsContainer = this.container.querySelector('#log-content');
        this.fetchLogsButton = document.getElementById('fetch-logs-button');
        this.downloadButton = document.getElementById('download-button');
        this.refreshButton = document.getElementById('refresh-button');
        this.autoRefreshCheckbox = document.getElementById('auto-refresh');
        this.logLevelSelect = document.getElementById('log_level_defaultLogLevel');
        this.refreshIntervalSelect = document.getElementById('log_level_logRefreshDelay');
        this.autoRefreshInterval = null;

        this.logLevelColors = {
            'NOTICE': 'cyan',
            'INFO': 'cyan',
            'DEBUG': 'green',
            'WARN': 'yellow',
            'WARNING': 'yellow',
            'ERROR': 'red',
            'ERR': 'red',
            'SUCCESS': 'green',
        };

        this.initialize();
    }

    /**
     * Initializes and sets up event listeners for user interactions.
     */
    initialize() {
        this.fetchLogsButton.addEventListener('click', () => this.handleFetchLogs());
        this.downloadButton.addEventListener('click', () => this.handleDownload());
        this.refreshButton.addEventListener('click', () => this.refreshLogs());
        this.autoRefreshCheckbox.addEventListener('change', () => this.setupAutoRefresh());
        this.logLevelSelect.addEventListener('change', () => this.handleFetchLogs());
    }

    processLogs(logs, logLevel) {
        const filteredLogs = (Array.isArray(logs) ? logs : logs.split('\n'))
            .filter(log => logLevel === 'all' || log.includes(logLevel.toUpperCase()))
            .filter(log => !log.startsWith('Sudoers entry'));

        filteredLogs.forEach(log => {
            const p = document.createElement('p');
            const logFragment = document.createDocumentFragment();
            let colored = false;

            Object.keys(this.logLevelColors).forEach(level => {
                if (log.includes(level) && !colored) {
                    const parts = log.split(level);
                    const span = document.createElement('span');
                    span.textContent = level;
                    span.style.color = this.logLevelColors[level];
                    logFragment.append(parts[0], span, parts[1]);
                    colored = true;
                }
            });

            if (!colored) {
                logFragment.append(log);
            }

            p.appendChild(logFragment);
            this.logsContainer.appendChild(p);
        });
    }

    /**
     * Determines the color for a log entry based on its level.
     *
     * @param {string} log - The log entry.
     * @returns {string} The color for the log entry.
     */
    getLogColor(log) {
        for (const level in this.logLevelColors) {
            if (log.includes(level)) {
                return this.logLevelColors[level];
            }
        }
        return 'white';
    }

    /**
     * Handles fetching logs from the input path.
     */
    async handleFetchLogs() {
        const filePath = this.logFilePathInput.value;
        if (filePath) {
            const response = await fetchData('/api/logs/fetch', 'POST', { filePath });
            if (response) {
                console.log('Logs Response:', response);
                const logs = response.logs;
                const logLevel = this.logLevelSelect.value;
                this.logsContainer.innerHTML = '';
                this.processLogs(logs, logLevel);
                this.container.classList.remove('hidden');
            } else {
                console.error('Failed to fetch logs');
            }
        }
    }
    

    /**
     * Handles the download of the fetched log content.
     */
    handleDownload() {
        const filePath = this.logFilePathInput.value;
        if (filePath) {
            const logs = this.logsContainer.textContent;
            const blob = new Blob([logs], { type: 'text/plain' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filePath.split('/').pop();
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
    }

    /**
     * Refreshes the log content from the input path.
     */
    async refreshLogs() {
        await this.handleFetchLogs();
    }

    /**
     * Sets up the auto-refresh functionality.
     */
    setupAutoRefresh() {
        const interval = parseInt(this.refreshIntervalSelect.value, 10) * 1000;
        if (this.autoRefreshCheckbox.checked) {
            this.autoRefreshInterval = setInterval(() => this.refreshLogs(), interval);
        } else {
            clearInterval(this.autoRefreshInterval);
        }
    }
}

export default ApplicationLogUI;
