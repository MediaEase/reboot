import { updateWidget, headers } from '../utils.js';
import { initializeNetworkChart, startNetworkDataFetch } from './networkWidget.js';

class WidgetManager {
    constructor(widgets, userPreferences) {
        this.widgets = widgets;
        this.userPreferences = userPreferences;
    }

    async initialize() {
        this.widgets.forEach(widget => {
            if (this.isUserWidgetEnabled(widget)) {
                if (widget.name.startsWith('net')) {
                    this.initializeNetworkWidget(widget);
                } else {
                    this.initializeRegularWidget(widget);
                }
            }
        });
    }

    isUserWidgetEnabled(widget) {
        return this.userPreferences.selectedWidgets.includes(widget.name);
    }

    initializeNetworkWidget(widget) {
        initializeNetworkChart();
        startNetworkDataFetch(widget.url, headers);
    }

    initializeRegularWidget(widget) {
        setInterval(() => updateWidget(widget.type, widget.url, widget.processData), 5000);
    }
}

export default WidgetManager;
