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
                if (widget.name === 'network_widget') {
                    this.initializeNetworkWidget(widget);
                } else if (widget.processData) {
                    this.initializeRegularWidget(widget);
                }
            }
        });
    }

    isUserWidgetEnabled(widget) {
        return this.userPreferences.widgets.some(uniqueName =>
            uniqueName.replace(/\d+$/, '') === widget.type
        );
    }

    initializeNetworkWidget(widget) {
        initializeNetworkChart();
        startNetworkDataFetch(widget.url, headers);
    }

    initializeRegularWidget(widget) {
        setInterval(() => updateWidget(widget.name, widget.url, widget.processData, headers), 5000);
    }
}

export default WidgetManager;
