import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

import { Tab, Modal, Popover, Ripple, Tooltip, initTE } from "tw-elements";
import './styles/app.css';
import './js/clipboard.js';
import './js/routing/links.js';
import { toDarkMode, toLightMode } from './js/menus/theme_switcher.js';
import ApplicationLogUI from './js/logs/ApplicationLogUI.js';

window.toDarkMode = toDarkMode;
window.toLightMode = toLightMode;

document.addEventListener('DOMContentLoaded', () => {
    initTE({ Tab, Modal, Popover, Ripple, Tooltip });
});

document.addEventListener('DOMContentLoaded', () => {
    new ApplicationLogUI('logs-container');
});
