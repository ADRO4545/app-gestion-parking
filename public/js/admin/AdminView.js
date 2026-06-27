export class AdminView {
    constructor() {
        this.deleteForms = document.querySelectorAll('.form-delete');
        this.autoSubmitSelects = document.querySelectorAll('.auto-submit');
    }

    // Demande confirmation avant de soumettre un formulaire de suppression
    bindDeleteConfirmation(handler) {
        this.deleteForms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!handler()) {
                    e.preventDefault(); // Annule la suppression si on clique sur "Annuler"
                }
            });
        });
    }

    // Soumet automatiquement le formulaire quand on change le nombre d'éléments par page
    bindAutoSubmit() {
        this.autoSubmitSelects.forEach(select => {
            select.addEventListener('change', (e) => {
                e.target.form.submit();
            });
        });
    }
}