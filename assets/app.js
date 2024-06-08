import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import TomSelect from 'tom-select';
import "tom-select/dist/css/tom-select.css";
import './styles/app.css';
import './js/clipboard.js';
import './js/routing/links.js';
import { toDarkMode, toLightMode } from './js/menus/theme_switcher.js';
import { toggleIPVisibility } from './js/utils.js';

window.toDarkMode = toDarkMode;
window.toLightMode = toLightMode;
window.toggleIPVisibility = toggleIPVisibility;

import { Tab, Modal, Popover, Ripple, Tooltip, initTE } from "tw-elements";

document.addEventListener('DOMContentLoaded', () => {
    initTE({ Tab, Modal, Popover, Ripple, Tooltip });

    const TomSelectConfig = {
        plugins: ['remove_button'],
        hideSelected: true,
    };
    new TomSelect('#new_group_stores', TomSelectConfig);
});
