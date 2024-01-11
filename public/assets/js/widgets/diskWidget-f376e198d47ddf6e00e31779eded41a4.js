import { ProgressCircle } from '../utils.js';

export function processDataForDiskWidget(widget, data) {
    const diskData = data.disk;
    const usageElement = widget.querySelector('.usage-text');
    const mountElement = widget.querySelector('.mount-text');
    const nameElement = widget.querySelector('.name-text');
    const totalElement = widget.querySelector('.total-text');
    const freeElement = widget.querySelector('.free-text');
    const usedElement = widget.querySelector('.used-text');
    const progressCircle = widget.querySelector('.disk-progress-circle');
    ProgressCircle(progressCircle, diskData.percentage);
    usageElement.textContent = `${diskData.percentage}%`;
    totalElement.textContent = `Total: ${diskData.total}`;
    usedElement.textContent = `Used: ${diskData.used}`;
    freeElement.textContent = `Free: ${diskData.free}`;
    mountElement.textContent = `Mount: ${diskData.mount}`;
    nameElement.textContent = `${diskData.name}`;
}
