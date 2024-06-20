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
import NewAppForm from './js/app/ui/elements/NewAppForm';

window.toDarkMode = toDarkMode;
window.toLightMode = toLightMode;
window.toggleIPVisibility = toggleIPVisibility;

import { Tab, Modal, Popover, Ripple, Tooltip, initTE, Validation } from "tw-elements";

document.addEventListener('DOMContentLoaded', () => {
    initTE({ Tab, Modal, Popover, Ripple, Tooltip, Validation });

    const TomSelectConfig = {
        plugins: ['remove_button'],
        hideSelected: true,
    };
    if (document.getElementById('new_group_stores')) {
        new TomSelect('#new_group_stores', TomSelectConfig);
    }

    const newAppForm = document.getElementById('create-user-form');
    if (newAppForm) {
        new NewAppForm();
    }
});
