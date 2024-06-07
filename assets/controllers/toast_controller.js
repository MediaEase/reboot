import { Controller } from '@hotwired/stimulus';
import {
    Toast,
    initTE,
} from "tw-elements";

initTE({ Toast });

export default class extends Controller {
    connect() {
        const toastElements = document.querySelectorAll('[data-te-toast-init]');
        toastElements.forEach(element => {
            const toast = new Toast(element);
            toast.show();
            setTimeout(() => {
                toast.hide();
            }, 8000);
        });
    }
}
