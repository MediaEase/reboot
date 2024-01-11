// networkWidget.js
import { updateWidget } from '../utils.js';
import Chart from 'chart.js/auto';
import 'chartjs-adapter-date-fns';
import 'date-fns/locale';

let networkChartData = {
    labels: [],
    datasets: [
        {
            label: 'Download Speed (Kb/s)',
            backgroundColor: 'rgba(255, 99, 132, 0.5)',
            borderColor: 'rgb(255, 99, 132)',
            fill: true,
            data: [],
        },
        {
            label: 'Upload Speed (Kb/s)',
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
            borderColor: 'rgb(54, 162, 235)',
            fill: true,
            data: [],
        },
    ],
};

let networkChart;

export function initializeNetworkChart() {
    const ctx = document.getElementById('network_chart').getContext('2d');
    networkChart = new Chart(ctx, {
        type: 'line',
        data: networkChartData,
        options: {
            scales: {
                x: {
                    type: 'time',
                    time: {
                        unit: 'minute',
                        tooltipFormat: 'p',
                        stepSize: 1,
                    },
                    title: {
                        display: true,
                        text: 'Time',
                    },
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Speed (Kb/s)',
                    },
                },
            },
            plugins: {
                legend: {
                    labels: {
                        color: '#fff',
                    },
                },
            },
            locale: "en",
        },
    });
}
Chart.defaults.color = '#fff';

export function processDataForNetworkWidget(widget, data) {
    const networkData = data.network;
    const interfaceElement = widget.querySelector('.interface-text');

    interfaceElement.textContent = `Interface: ${networkData.interface}`;
    updateChart(networkData.downloadSpeed, networkData.uploadSpeed);
}

function updateChart(downloadSpeed, uploadSpeed) {
    const now = new Date();

    networkChartData.labels.push(now);
    networkChartData.datasets[0].data.push(downloadSpeed);
    networkChartData.datasets[1].data.push(uploadSpeed);

    if (networkChartData.labels.length > 60) {
        networkChartData.labels.shift();
        networkChartData.datasets.forEach((dataset) => dataset.data.shift());
    }

    networkChart.update();
}

export function startNetworkDataFetch(apiEndpoint, headers) {
    updateWidget('network_widget', apiEndpoint, processDataForNetworkWidget, headers);
    setInterval(() => {
        updateWidget('network_widget', apiEndpoint, processDataForNetworkWidget, headers);
    }, 5000);
}
