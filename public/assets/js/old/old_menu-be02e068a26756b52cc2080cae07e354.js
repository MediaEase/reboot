
document.addEventListener('DOMContentLoaded', () => {
    // Attacher les événements pour ouvrir les modales
    const modalToggles = document.querySelectorAll('[data-toggle="modal"]');
    modalToggles.forEach(toggle => {
        toggle.addEventListener('click', function () {
            const modalId = this.dataset.target.replace('#', '');
            openModal(modalId);
        });
    });

    // Attacher un événement pour fermer les modales en cliquant sur le fond (modal backdrop)
    const modalBackdrop = document.getElementById('modalBackdrop');
    modalBackdrop.addEventListener('click', () => {
        // Fermer toutes les modales qui ne sont pas cachées
        document.querySelectorAll('.modal:not(.hidden)').forEach(modal => {
            closeModal(modal.getAttribute('id'));
        });
    });

    // Fermer les modales lorsque l'utilisateur appuie sur la touche 'Échap'
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            document.querySelectorAll('.modal:not(.hidden)').forEach(modal => {
                closeModal(modal.getAttribute('id'));
            });
        }
    });

    // Fermer initialement toutes les modales
    closeAllModals();
});

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    const modalBackdrop = document.getElementById('modalBackdrop');
    if (modal && modalBackdrop) {
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        modalBackdrop.classList.remove('hidden');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    const modalBackdrop = document.getElementById('modalBackdrop');
    if (modal && modalBackdrop) {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        modalBackdrop.classList.add('hidden');
    }
}

// Fonction pour fermer toutes les modales
function closeAllModals() {
    const modals = document.querySelectorAll('.modal');
    const modalBackdrop = document.getElementById('modalBackdrop');
    modals.forEach(modal => {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
    });
    modalBackdrop.classList.add('hidden');
}
