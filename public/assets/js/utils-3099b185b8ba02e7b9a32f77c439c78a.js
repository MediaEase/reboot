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

export function updateWidget(widgetId, apiEndpoint, dataProcessor) {
    const widget = document.getElementById(widgetId);
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

    try {
        const response = await fetch(url, options);
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
    return `
        <div class="flex-none my-1">
            <code id="${id}" class="text-xs xs:text-base inline-flex text-left items-center space-x-4 bg-gray-800 text-white rounded-lg py-1 pl-6">
                <span class="text-yellow-500 text-xs text-clip">
                    ${data}
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
