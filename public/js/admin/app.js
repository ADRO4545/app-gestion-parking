// public/js/admin/app.js
import { AdminController } from './AdminController.js';
import { UserFilter } from './UserFilter.js';

// On lance le JS uniquement quand la page a fini de charger
document.addEventListener('DOMContentLoaded', () => {
    new AdminController();
    new UserFilter(); // On initialise les filtres dynamiques
});