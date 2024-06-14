document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("create-user-form");
    if (form) {
        form.addEventListener("submit", async function (event) {
            event.preventDefault();
            const formData = new FormData(form);
            const action = form.getAttribute("action") || window.location.href;
            const csrfToken = form.querySelector('input[name="create_user[_token]"]').value;
            try {
                const response = await fetch(action, {
                    method: "POST",
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                if (!response.ok) {
                    const errorText = await response.text();
                    const responseData = JSON.parse(errorText);
                    clearErrors();
                    renderErrors(responseData.errors);
                    throw new Error("Network response was not ok");
                }
                const responseData = await response.json();
                clearErrors();
                if (responseData.errors) {
                    renderErrors(responseData.errors);
                } else {
                    window.location.href = responseData.redirectUrl;
                }
            } catch (error) {
                console.error("Form submission error:", error);
            }
        });
    }

    function clearErrors() {
        const errorDivs = document.querySelectorAll(".form-error");
        errorDivs.forEach((div) => {
            div.style.display = "none";
            div.innerHTML = "";
        });
        const inputFields = document.querySelectorAll(".peer");
        inputFields.forEach((field) => {
            field.classList.remove('border-red-600', 'dark:border-red-500', 'focus:border-red-600', 'dark:focus:border-red-500');
        });
        const labels = document.querySelectorAll("label");
        labels.forEach((label) => {
            label.classList.remove('text-red-600', 'dark:text-red-500');
        });
    }
    function renderErrors(errors) {
        for (const [field, message] of Object.entries(errors)) {
            const errorDiv = document.getElementById(`error-create_user_${field}`);
            if (errorDiv) {
                errorDiv.innerHTML = `${message}`;
                errorDiv.style.display = "block";
            }
            if (field === 'first') {
                const passwordErrorDiv = document.getElementById('error-create_user_plainPassword');
                if (passwordErrorDiv) {
                    passwordErrorDiv.innerHTML = `${message}`;
                    passwordErrorDiv.style.display = "block";
                }
                const passwordFields = document.querySelectorAll('#create_user_plainPassword_first, #create_user_plainPassword_second');
                passwordFields.forEach((field) => {
                    field.classList.add('border-red-600', 'dark:border-red-500', 'focus:border-red-600', 'dark:focus:border-red-500');
                });
                const passwordLabels = document.querySelectorAll('label[for="create_user_plainPassword_first"], label[for="create_user_plainPassword_second"]');
                passwordLabels.forEach((label) => {
                    label.classList.add('text-red-600', 'dark:text-red-500');
                });
            } else {
                const inputField = document.getElementById(`create_user_${field}`);
                if (inputField) {
                    inputField.classList.add('border-red-600', 'dark:border-red-500', 'focus:border-red-600', 'dark:focus:border-red-500');
                }
                const label = inputField ? inputField.parentNode.querySelector('label') : null;
                if (label) {
                    label.classList.add('text-red-600', 'dark:text-red-500');
                }
            }
        }
    }
});
