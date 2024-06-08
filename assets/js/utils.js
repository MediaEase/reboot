function colorize(usage) {
    const lowThreshold = 10;
    const highThreshold = 90;
    let color;

    if (usage < lowThreshold) {
        color = 'green';
    } else if (usage > highThreshold) {
        color = 'red';
    } else {
        const greenToRed = (usage - lowThreshold) / (highThreshold - lowThreshold);
        const red = Math.floor(255 * greenToRed);
        const green = 255 - red;
        color = `rgb(${red}, ${green}, 0)`;
    }

    return color;
}

export function ProgressCircle(circle, percentage) {
    const radius = circle.r.baseVal.value;
    const circumference = 2 * Math.PI * radius;
    circle.style.strokeDasharray = `${circumference} ${circumference}`;
    const offset = circumference - (percentage / 100) * circumference;
    circle.style.strokeDashoffset = offset;
    circle.style.stroke = colorize(percentage);
}

export function updateWidget(widgetName, apiEndpoint, dataProcessor) {
    const widget = document.querySelector(`[data-widget-panel="${widgetName}"]`);
    if (!widget) {
        console.error(`Widget with ID '${widgetName}' not found`);
        return;
    }
    const spinner = widget.querySelector('.spinner');
    const content = widget.querySelector('.widget_content');

    fetchData(apiEndpoint, 'GET')
        .then(data => {
            if (data) {
                dataProcessor(widget, data);
                content.style.display = '';
            } else {
                console.error(`No data received from ${apiEndpoint}`);
            }
            spinner.style.display = 'none';
        })
        .catch(error => {
            console.error(`Error fetching metrics from ${apiEndpoint}:`, error);
            spinner.style.display = 'none';
        });
}

export async function fetchData(url, method = 'GET', body = null) {
    const options = {
        method: method,
        headers: headers
    };

    if (body && (method === 'PUT' || method === 'POST')) {
        options.body = JSON.stringify(body);
        options.headers['Content-Type'] = 'application/hal+json';
    }

    if (method === 'PATCH') {
        options.body = JSON.stringify(body);
        options.headers['Content-Type'] = 'application/merge-patch+json';
    }

    try {
        const response = await fetch(url, options);
        if (response.status === 401) {
            window.location.href = '/logout';
            return null;
        }
        return await response.json();
    } catch (error) {
        console.error('Error:', error);
        return null;
    }
}

export function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}

export const headers = { 'Authorization': 'Bearer ' + getCookie('thegate') };

export function CodeBlock(id, scope, data) {
    if (scope === 'port') {
        id = `${id}-port`;
    } else if (scope === 'apikey') {
        id = `${id}-apikey`;
    }
    const displayData = data.length > 10 ? `${data.substring(0, 10)}...` : data;

    return `
        <div class="flex-none my-1">
            <code id="${id}" class="text-xs xs:text-base inline-flex text-left items-center space-x-4 bg-gray-800 text-white rounded-lg py-1 pl-6" data-clipboard-text="${data}">
                <span class="text-yellow-500 text-xs text-clip">
                    ${displayData}
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

export function slugify(str) {
    if (typeof str !== 'string') {
        console.error('slugify: Expected a string, got', str);
        return '';
    }
    return str.replace(/\s+/g, '-').toLowerCase();
}

/**
 * Fetches the content of a log file from the server.
 * 
 * @param {string} filePath - The path to the log file.
 * @returns {Promise<string>} A promise that resolves with the log file content as a string.
 */
export async function fetchLogContent(filePath) {
    const response = await fetchData('/api/logs/fetch', 'POST', { filePath });
    console.log(response);
    if (!response) {
        throw new Error('Failed to fetch log content');
    }

    return response.logs;
}

export function toggleIPVisibility() {
    const ipDisplay = document.getElementById('ip-display');
    const ipAddress = ipDisplay.getAttribute('data-ip');
    const eyeIcon = document.getElementById('eye-icon');
    const isHidden = ipDisplay.textContent === '**********';

    if (isHidden) {
        ipDisplay.classList.remove('text-center');
        ipDisplay.textContent = ipAddress;
        eyeIcon.innerHTML = `
            <path d="M12 4.5C7.30558 4.5 3.13546 7.21027 1.5 12C3.13546 16.7897 7.30558 19.5 12 19.5C16.6944 19.5 20.8645 16.7897 22.5 12C20.8645 7.21027 16.6944 4.5 12 4.5ZM12 17.25C8.82436 17.25 6.125 14.5506 6.125 11.375C6.125 8.19944 8.82436 5.5 12 5.5C15.1756 5.5 17.875 8.19944 17.875 11.375C17.875 14.5506 15.1756 17.25 12 17.25ZM12 7.375C10.3579 7.375 9.125 8.60794 9.125 10.25C9.125 11.8921 10.3579 13.125 12 13.125C13.6421 13.125 14.875 11.8921 14.875 10.25C14.875 8.60794 13.6421 7.375 12 7.375Z" />`; // Eye icon for visible IP
    } else {
        ipDisplay.classList.add('text-center');
        ipDisplay.textContent = '**********';
        eyeIcon.innerHTML = `
            <path d="M12 4.5C7.30558 4.5 3.13546 7.21027 1.5 12C3.13546 16.7897 7.30558 19.5 12 19.5C16.6944 19.5 20.8645 16.7897 22.5 12C20.8645 7.21027 16.6944 4.5 12 4.5ZM12 17.25C8.82436 17.25 6.125 14.5506 6.125 11.375C6.125 8.19944 8.82436 5.5 12 5.5C15.1756 5.5 17.875 8.19944 17.875 11.375C17.875 14.5506 15.1756 17.25 12 17.25ZM12 7.375C10.3579 7.375 9.125 8.60794 9.125 10.25C9.125 11.8921 10.3579 13.125 12 13.125C13.6421 13.125 14.875 11.8921 14.875 10.25C14.875 8.60794 13.6421 7.375 12 7.375Z" />`; // Eye icon for hidden IP
    }
}
