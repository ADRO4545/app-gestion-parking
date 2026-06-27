/**
 * @file my-reservations.js
 * @description Gère l'affichage des fenêtres modales d'édition et d'annulation des réservations.
 */

/**
 * Ouvre la modale de modification de réservation et pré-remplit le formulaire.
 * @param {HTMLElement} btn - Le bouton ayant déclenché l'action, contenant les attributs de données (data-id, data-start, data-end).
 */
function openEditModal(btn) {
    document.getElementById('edit_res_id').value = btn.getAttribute('data-id');
    document.getElementById('edit_start_time').value = btn.getAttribute('data-start');
    document.getElementById('edit_end_time').value = btn.getAttribute('data-end');
    
    document.getElementById('editModal').style.display = 'flex';
}

/**
 * Ferme la modale de modification de réservation.
 */
function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

/**
 * Ouvre la modale de confirmation d'annulation de réservation.
 * @param {HTMLElement} btn - Le bouton ayant déclenché l'action, contenant l'attribut data-id.
 */
function openCancelModal(btn) {
    document.getElementById('cancel_res_id').value = btn.getAttribute('data-id');
    document.getElementById('cancelModal').style.display = 'flex';
}

/**
 * Ferme la modale de confirmation d'annulation.
 */
function closeCancelModal() {
    document.getElementById('cancelModal').style.display = 'none';
}
