/**
 * @file admin-dashboard.js
 * @description Scripts pour l'administration (Modale d'historique, Formulaires, etc.).
 */

class HistoryModalController {
    /**
     * Constructeur pour initialiser le contrôleur de la modale.
     * @param {Object} data - Les données d'historique de tous les utilisateurs.
     */
    constructor(data) {
        this.allData = data;
        this.currentUserId = null;
        this.modal = document.getElementById('historyModal');
        this.closeBtn = document.getElementById('closeModal');
        this.tbody = document.getElementById('modalTableBody');
        this.title = document.getElementById('modalTitle');
        
        this.filterSpot = document.getElementById('filterSpot');
        this.filterDateStart = document.getElementById('filterDateStart');
        this.filterDateEnd = document.getElementById('filterDateEnd');
        this.filterPriceMin = document.getElementById('filterPriceMin');
        this.filterPriceMax = document.getElementById('filterPriceMax');
        this.filterStatus = document.getElementById('filterStatus');
        
        if (this.modal) {
            this.bindEvents();
        }
    }

    /**
     * Attache les écouteurs d'événements.
     */
    bindEvents() {
        document.body.addEventListener('click', (e) => {
            if (e.target && e.target.classList.contains('btn-history')) {
                this.currentUserId = e.target.getAttribute('data-user-id');
                this.title.innerText = "Historique de " + e.target.getAttribute('data-user-name');
                this.resetFilters();
                this.renderTable();
                this.modal.style.display = 'flex';
            }
        });

        this.closeBtn.addEventListener('click', () => this.modal.style.display = 'none');
        this.modal.addEventListener('click', (e) => {
            if(e.target === this.modal) this.modal.style.display = 'none';
        });

        const inputs = [this.filterSpot, this.filterDateStart, this.filterDateEnd, this.filterPriceMin, this.filterPriceMax, this.filterStatus];
        inputs.forEach(input => input.addEventListener('input', () => this.renderTable()));
    }

    /**
     * Réinitialise les filtres de la modale.
     */
    resetFilters() {
        this.filterSpot.value = ''; 
        this.filterDateStart.value = ''; 
        this.filterDateEnd.value = '';
        this.filterPriceMin.value = ''; 
        this.filterPriceMax.value = '';
        this.filterStatus.value = '';
    }

    /**
     * Affiche la table de l'historique filtrée.
     */
    renderTable() {
        this.tbody.innerHTML = '';
        const userReservations = this.allData[this.currentUserId] || [];
        const filtered = userReservations.filter(res => {
            // On extrait uniquement "YYYY-MM-DD" de la date SQL
            const resDate = res.start_time.substring(0, 10);
            
            const mSpot = this.filterSpot.value === '' || res.spot_number.includes(this.filterSpot.value);
            const mDateStart = this.filterDateStart.value === '' || resDate >= this.filterDateStart.value;
            const mDateEnd = this.filterDateEnd.value === '' || resDate <= this.filterDateEnd.value;
            const mPriceMin = this.filterPriceMin.value === '' || parseFloat(res.total_price) >= parseFloat(this.filterPriceMin.value);
            const mPriceMax = this.filterPriceMax.value === '' || parseFloat(res.total_price) <= parseFloat(this.filterPriceMax.value);
            const mStatus = this.filterStatus.value === '' || res.status === this.filterStatus.value;
            
            return mSpot && mDateStart && mDateEnd && mPriceMin && mPriceMax && mStatus;
        });
        
        if (filtered.length === 0) {
            this.tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">Aucune réservation trouvée.</td></tr>';
            return;
        }
        
        filtered.forEach(res => {
            this.tbody.innerHTML += `
                <tr>
                    <td>N° ${res.spot_number}</td>
                    <td>${res.start_time}</td>
                    <td>${res.end_time}</td>
                    <td><strong>${res.total_price} €</strong></td>
                    <td>${res.status}</td>
                </tr>
            `;
        });
    }
}

/**
 * Gère l'affichage des champs selon le mode de création de place.
 */
function toggleSpotMode() {
    const mode = document.getElementById('creationMode').value;
    const singleMode = document.getElementById('singleSpotMode');
    const bulkMode = document.getElementById('bulkSpotMode');
    const inputSingle = document.getElementById('spot_number_input');
    const inputStart = document.getElementById('spot_number_start');
    const inputEnd = document.getElementById('spot_number_end');

    if (mode === 'bulk') {
        singleMode.style.display = 'none';
        bulkMode.style.display = 'block';
        inputSingle.removeAttribute('required');
        inputStart.setAttribute('required', 'required');
        inputEnd.setAttribute('required', 'required');
    } else {
        singleMode.style.display = 'block';
        bulkMode.style.display = 'none';
        inputSingle.setAttribute('required', 'required');
        inputStart.removeAttribute('required');
        inputEnd.removeAttribute('required');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Initialisation de la modale d'historique si on est dans la section utilisateurs
    if (typeof window.adminHistoryData !== 'undefined') {
        new HistoryModalController(window.adminHistoryData);
    }

    // Attache de l'événement toggleSpotMode si on est dans la section places
    const creationModeSelect = document.getElementById('creationMode');
    if (creationModeSelect) {
        creationModeSelect.addEventListener('change', toggleSpotMode);
    }
});
