// public/js/admin/UserFilter.js

export class UserFilter {
    constructor() {
        this.form = document.getElementById('filterForm');
        this.tableContainer = document.getElementById('usersTableContainer');

        // Si le formulaire et le tableau existent sur la page, on active l'écouteur
        if (this.form && this.tableContainer) {
            this.initEvents();
        }
    }

    initEvents() {
        // On écoute UNIQUEMENT la soumission du formulaire (clic sur le bouton ou touche Entrée)
        this.form.addEventListener('submit', (e) => {
            e.preventDefault(); // Empêche le rechargement complet (clignotement) de la page
            this.fetchResults();
        });
    }

    fetchResults() {
        // CORRECTION : On utilise getAttribute pour éviter le conflit avec l'input name="action"
        const formAction = this.form.getAttribute('action') || window.location.href;
        
        // On ajoute window.location.href en deuxième paramètre pour gérer les liens relatifs comme "index.php"
        const url = new URL(formAction, window.location.href);
        const params = new URLSearchParams(new FormData(this.form));
        
        url.search = params.toString();

        // Fait la requête silencieuse au serveur
        fetch(url)
            .then(response => response.text())
            .then(html => {
                // Transforme la réponse texte en vrai code HTML
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Isole uniquement le nouveau tableau dans la page reçue
                const newTableContent = doc.getElementById('usersTableContainer');
                
                if (newTableContent) {
                    // Remplace l'ancien tableau par le nouveau
                    this.tableContainer.innerHTML = newTableContent.innerHTML;
                }
                this.tableContainer.style.opacity = '1';
            })
            .catch(error => {
                console.error("Erreur lors de l'actualisation des filtres :", error);
                this.tableContainer.style.opacity = '1';
            });
    }
}