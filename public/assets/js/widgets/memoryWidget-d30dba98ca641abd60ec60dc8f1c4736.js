import { ProgressCircle } from '../utils.js';

export function processDataForRamWidget(widget, data) {
    const ramData = data.ram;
    const usageElement = widget.querySelector('.percentage');
    const totalElement = widget.querySelector('.total-text');
    const freeElement = widget.querySelector('.free-text');
    const progressCircle = widget.querySelector('.ram-progress-circle');
    ProgressCircle(progressCircle, ramData.percentage);
    usageElement.textContent = `${ramData.percentage.toFixed(2)}%`;
    totalElement.textContent = `Total: ${Number(ramData.total).toFixed(2)} MB`;
    freeElement.textContent = `Available: ${(ramData.available).toFixed(2)} MB`;
}
