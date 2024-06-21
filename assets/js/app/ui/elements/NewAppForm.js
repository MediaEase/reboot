/**
 * Handles the UI logic for creating a new application, including adding/removing service forms
 * and toggling between port fields and socket fields.
 */
class NewAppForm {
    constructor() {
        this.collectionHolder = document.querySelector('#services-collection');
        this.initialize();
        this.translator = window.translator;
    }

    initialize() {
        this.collectionHolder.dataset.index = this.collectionHolder.querySelectorAll('.service-form').length;
        document.querySelector('[data-controller="symfony--ux-dropzone--dropzone"]').classList.add('w-full');
        document.querySelector('.add-service-btn').addEventListener('click', () => {
            const currentIndex = parseInt(this.collectionHolder.dataset.index);
            if (currentIndex >= 2) {
                alert(this.translator.trans('You can only add up to two services'));
                return;
            }
            this.addServiceForm(currentIndex);
            this.collectionHolder.dataset.index = currentIndex + 1;
            this.updateServiceTitles();
        });
        document.querySelectorAll('.remove-service-btn').forEach(button => {
            button.addEventListener('click', () => {
                this.removeServiceForm(button);
            });
        });

        this.initializeFloatingLabels(document);
        document.querySelectorAll('.service-form').forEach(form => this.initializeToggleLogic(form));
    }

    addServiceForm(index) {
        const newForm = this.collectionHolder.dataset.prototype.replace(/__name__/g, index);
        const newFormWrapper = document.createElement('div');
        newFormWrapper.classList.add('service-form', 'border-r', 'border-b', 'border-gray-300', 'p-4', 'rounded-lg', 'mb-4', 'w-1/2');
        const serviceTitle = index === 0 ? this.translator.trans('Parent Service') : this.translator.trans('Child Service');
        const serviceHelper = index === 0 ? this.translator.trans('This form describes the main service linked to this application.') : this.translator.trans('This is an additional service that depends on the parent service.');
        newFormWrapper.innerHTML = `
            <h3 class="text-lg font-bold service-title text-gray-800 dark:text-gray-200">${serviceTitle}</h3>
            <p class="text-sm text-gray-500 mb-4 -mt-2">${serviceHelper}</p>
            <div class="relative mb-6" data-twe-input-wrapper-init data-twe-validate="input">
                <input type="text" id="service_${index}_name" name="service[${index}][name]" required="required" placeholder=" " class="block rounded-t-md px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                <label for="service_${index}_name" class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-2.5 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4">${this.translator.trans('Service Name')}</label>
                <p id="error-service_${index}_name" class="mt-2 text-xs text-red-600 dark:text-red-400" style="display: none;"></p>
            </div>
            <div class="relative mb-6" data-twe-input-wrapper-init data-twe-validate="input">
                <input type="text" id="service_${index}_apikey" name="service[${index}][apikey]" placeholder=" " class="block rounded-t-md px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                <label for="service_${index}_apikey" class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-2.5 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4">${this.translator.trans('API Key')} ${this.translator.trans('(optional)')}</label>
                <p class="text-xs text-left text-gray-500 mt-1">${this.translator.trans('API Key is optional.')}</p>
                <p id="error-service_${index}_apikey" class="mt-2 text-xs text-red-600 dark:text-red-400" style="display: none;"></p>
            </div>
            <div class="flex items-center justify-between mb-6">
                <label for="service_${index}_use_socket" class="block text-sm font-bold text-gray-500 dark:text-gray-400">${this.translator.trans('Use socket')}</label>
                <input type="checkbox" id="service_${index}_use_socket" class="use-socket-toggle peer relative w-[3.25rem] h-7 p-px bg-red-500 border-red-500 text-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 disabled:opacity-50 disabled:pointer-events-none checked:bg-green-400 checked:text-green-400 checked:border-green-200 focus:checked:border-green-400 before:inline-block before:size-6 before:bg-white checked:before:bg-white before:translate-x-0 checked:before:translate-x-full before:rounded-full before:transform before:ring-0 before:transition before:ease-in-out before:duration-200">
            </div>
            <div class="port-fields">
                <div class="flex space-x-2 mb-6">
                    <div class="relative w-1/2" data-twe-input-wrapper-init data-twe-validate="input">
                        <input type="number" id="service_${index}_web_port" name="service[${index}][web_port]" placeholder=" " class="block rounded-t-md px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                        <label for="service_${index}_web_port" class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-2.5 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4">${this.translator.trans('Web Port')}</label>
                        <p id="error-service_${index}_web_port" class="mt-2 text-xs text-red-600 dark:text-red-400" style="display: none;"></p>
                    </div>
                    <div class="relative w-1/2" data-twe-input-wrapper-init data-twe-validate="input">
                        <input type="number" id="service_${index}_ssl_port" name="service[${index}][ssl_port]" placeholder=" " class="block rounded-t-md px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                        <label for="service_${index}_ssl_port" class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-2.5 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4">${this.translator.trans('SSL Port')}</label>
                        <p id="error-service_${index}_ssl_port" class="mt-2 text-xs text-red-600 dark:text-red-400" style="display: none;"></p>
                    </div>
                </div>
            </div>
            <div class="socket-fields hidden">
                <div class="relative mb-6" data-twe-input-wrapper-init data-twe-validate="input">
                    <input type="text" id="service_${index}_socket" name="service[${index}][socket]" placeholder=" " class="block rounded-t-md px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                    <label for="service_${index}_socket" class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-2.5 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4">${this.translator.trans('Socket Path')}</label>
                    <p id="error-service_${index}_socket" class="mt-2 text-xs text-red-600 dark:text-red-400" style="display: none;"></p>
                </div>
            </div>
            <h4 class="text-md font-semibold mb-1 text-gray-800 dark:text-gray-200">${this.translator.trans('Paths')}</h4>
            <p class="text-xs text-gray-500 mb-4 -mt-2">${this.translator.trans('All these fields are optional.')}</p>
            <div class="flex justify-between items-center mb-4" data-twe-input-wrapper-init data-twe-validate="input">
                <label for="service_${index}_subdomain" class="block text-sm font-bold mb-2 text-left cursor-pointer text-gray-500 dark:text-gray-400">${this.translator.trans('Subdomain')}</label>
                <input type="checkbox" id="service_${index}_subdomain" name="service[${index}][subdomain]" class="peer relative w-[3.25rem] h-7 p-px bg-red-500 border-red-500 text-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 disabled:opacity-50 disabled:pointer-events-none checked:bg-green-400 checked:text-green-400 checked:border-green-200 focus:checked:border-green-400 before:inline-block before:size-6 before:bg-white checked:before:bg-white before:translate-x-0 checked:before:translate-x-full before:rounded-full before:transform before:ring-0 before:transition before:ease-in-out before:duration-200">
                <span class="peer-checked:hidden text-red-600 size-6 absolute top-0.5 start-0.5 flex justify-center items-center pointer-events-none transition-colors ease-in-out duration-200">
                </span>
                <span class="peer-checked:flex hidden text-white peer-checked:text-green-600 size-6 absolute top-0.5 end-0.5 justify-center items-center pointer-events-none transition-colors ease-in-out duration-200">
                </span>
            </div>
            <div class="relative mb-6" data-twe-input-wrapper-init data-twe-validate="input">
                <input type="text" id="service_${index}_config_path" name="service[${index}][config_path]" placeholder=" " class="block rounded-t-md px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                <label for="service_${index}_config_path" class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-2.5 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4">
                ${this.translator.trans('Config Path')} ${this.translator.trans('(optional)')}</label>
                <p id="error-service_${index}_config_path" class="mt-2 text-xs text-red-600 dark:text-red-400" style="display: none;"></p>
            </div>
            <div class="relative mb-6" data-twe-input-wrapper-init data-twe-validate="input">
                <input type="text" id="service_${index}_database_path" name="service[${index}][database_path]" placeholder=" " class="block rounded-t-md px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                <label for="service_${index}_database_path" class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-2.5 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4">
                ${this.translator.trans('Database Path')} ${this.translator.trans('(optional)')}</label>
                <p id="error-service_${index}_database_path" class="mt-2 text-xs text-red-600 dark:text-red-400" style="display: none;"></p>
            </div>
            <div class="relative mb-6" data-twe-input-wrapper-init data-twe-validate="input">
                <input type="text" id="service_${index}_caddyfile_path" name="service[${index}][caddyfile_path]" placeholder=" " class="block rounded-t-md px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                <label for="service_${index}_caddyfile_path" class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-2.5 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4">
                ${this.translator.trans('Caddyfile Path')} ${this.translator.trans('(optional)')}</label>
                <p id="error-service_${index}_caddyfile_path" class="mt-2 text-xs text-red-600 dark:text-red-400" style="display: none;"></p>
            </div>
            <div class="relative mb-6" data-twe-input-wrapper-init data-twe-validate="input">
                <input type="text" id="service_${index}_backup_path" name="service[${index}][backup_path]" placeholder=" " class="block rounded-t-md px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                <label for="service_${index}_backup_path" class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-2.5 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4">
                ${this.translator.trans('Backup Path')} ${this.translator.trans('(optional)')}</label>
                <p id="error-service_${index}_backup_path" class="mt-2 text-xs text-red-600 dark:text-red-400" style="display: none;"></p>
            </div>
            <div class="relative mb-6" data-twe-input-wrapper-init data-twe-validate="input">
                <input type="text" id="service_${index}_root_url" name="service[${index}][root_url]" placeholder=" " class="block rounded-t-md px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer">
                <label for="service_${index}_root_url" class="absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-2.5 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4">
                ${this.translator.trans('Root URL')} ${this.translator.trans('(optional)')}</label>
                <p id="error-service_${index}_root_url" class="mt-2 text-xs text-red-600 dark:text-red-400" style="display: none;"></p>
            </div>
            <div class="flex justify-end">
                <button type="button" class="text-red-500 hover:text-red-600 font-bold text-xs remove-service-btn mt-2 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg"  class="w-4 h-4 inline mr-1" width="1em" height="1em" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L17.94 6M18 18L6.06 6"/></svg>
                    ${this.translator.trans('Remove Service')}
                </button>
            </div>
        `;
        this.collectionHolder.appendChild(newFormWrapper);
        // Add event listener for the remove button
        newFormWrapper.querySelector('.remove-service-btn').addEventListener('click', () => {
            this.removeServiceForm(newFormWrapper.querySelector('.remove-service-btn'));
        });
        // Initialize floating label functionality for the new fields
        this.initializeFloatingLabels(newFormWrapper);
        // Initialize the toggle logic for port and socket fields
        this.initializeToggleLogic(newFormWrapper);
    }

    removeServiceForm(button) {
        const serviceForm = button.closest('.service-form');
        serviceForm.remove();
        this.collectionHolder.dataset.index = parseInt(this.collectionHolder.dataset.index) - 1;
        this.updateServiceTitles();
    }

    updateServiceTitles() {
        const serviceForms = this.collectionHolder.querySelectorAll('.service-form');
        serviceForms.forEach((form, index) => {
            const title = form.querySelector('.service-title');
            if (title) {
                title.textContent = index === 0 ? 'Parent Service' : 'Child Service';
            }
        });
    }

    initializeFloatingLabels(container) {
        container.querySelectorAll('.peer').forEach(input => {
            const label = input.nextElementSibling;
            if (label && input.value) {
                label.classList.add('scale-75', '-translate-y-4');
            }
            if (label) {
                input.addEventListener('input', function() {
                    if (input.value) {
                        label.classList.add('scale-75', '-translate-y-4');
                    } else {
                        label.classList.remove('scale-75', '-translate-y-4');
                    }
                });
            }
        });
    }

    initializeToggleLogic(container) {
        const toggle = container.querySelector('.use-socket-toggle');
        const portFields = container.querySelector('.port-fields');
        const socketFields = container.querySelector('.socket-fields');
        toggle.addEventListener('change', () => {
            if (toggle.checked) {
                portFields.classList.add('hidden');
                socketFields.classList.remove('hidden');
            } else {
                portFields.classList.remove('hidden');
                socketFields.classList.add('hidden');
            }
        });
    }
}

export default NewAppForm;
