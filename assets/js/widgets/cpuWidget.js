import { ProgressCircle } from '../utils.js';

export function processDataForCpuWidget(widget, data) {
    const cpuData = data.cpu;
    const usageElement = widget.querySelector('.percentage');
    const modelElement = widget.querySelector('.model-text');
    const progressCircle = widget.querySelector('.cpu-progress-circle');
    ProgressCircle(progressCircle, cpuData.percentage);
    usageElement.textContent = `${cpuData.percentage.toFixed(2)}%`;
    modelElement.textContent = cpuData.model;
}
