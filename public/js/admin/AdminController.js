import { AdminView } from './AdminView.js';

export class AdminController {
    constructor() {
        this.view = new AdminView();
        this.init();
    }

    init() {
        // On définit le message de confirmation
        this.view.bindDeleteConfirmation(() => {
            return confirm("⚠️ Attention : Êtes-vous sûr de vouloir supprimer cet élément définitivement ?");
        });

        // On active la soumission automatique (pour la pagination)
        this.view.bindAutoSubmit();
    }
}