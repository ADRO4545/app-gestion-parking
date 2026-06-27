/**
 * @file connexion.js
 * @description Gère la logique de connexion en deux étapes (email/mot de passe puis code de vérification).
 */

document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('actionBtn');
    const codeGroup = document.getElementById('code_group');
    const passwordGroup = document.getElementById('password_group');
    const codeInput = document.getElementById('verification_code');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const form = document.getElementById('loginForm');
    const feedback = document.getElementById('feedback');
    
    let step = 1;

    form.addEventListener('submit', (e) => {
        if (step === 1) {
            e.preventDefault(); 
            
            const email = emailInput.value;
            const password = passwordInput.value;
            
            btn.textContent = "Vérification...";
            btn.style.opacity = "0.7";
            btn.disabled = true;
            feedback.style.display = 'none'; // Cacher les anciens messages
            
            // Appel AJAX
            fetch('index.php?action=login_ajax', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'email=' + encodeURIComponent(email) + '&password=' + encodeURIComponent(password)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    step = 2;
                    passwordGroup.style.display = 'none'; // On cache le champ de mot de passe
                    codeGroup.style.display = 'block';    // On affiche le champ de code
                    
                    btn.textContent = "Valider la connexion";
                    btn.style.background = "#2ecc71";
                    btn.disabled = false;
                    btn.style.opacity = "1";
                    
                    emailInput.readOnly = true;
                    emailInput.style.background = "#f4f6f9";
                    
                    showSuccess("Identifiants corrects ! Un code à 6 chiffres vous a été envoyé.");
                    codeInput.focus();
                } else {
                    showError(data.message || "Erreur de connexion.");
                    btn.textContent = "Vérifier mes identifiants";
                    btn.disabled = false;
                    btn.style.opacity = "1";
                }
            })
            .catch(err => {
                showError("Erreur réseau. Impossible de contacter le serveur.");
                btn.textContent = "Vérifier mes identifiants";
                btn.disabled = false;
                btn.style.opacity = "1";
            });
        } else if (step === 2) {
            // Bloquer l'envoi si le code ne fait pas 6 chiffres
            if (codeInput.value.length !== 6) {
                e.preventDefault();
                showError("Veuillez entrer les 6 chiffres du code.");
            }
        }
    });

    // Forcer uniquement les chiffres dans l'input
    codeInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    /**
     * Affiche un message d'erreur à l'utilisateur.
     * @param {string} msg - Le message d'erreur à afficher.
     */
    function showError(msg) {
        feedback.textContent = msg;
        feedback.className = 'alert alert-error';
        feedback.style.display = 'block';
    }

    /**
     * Affiche un message de succès à l'utilisateur.
     * @param {string} msg - Le message de succès à afficher.
     */
    function showSuccess(msg) {
        feedback.textContent = msg;
        feedback.className = 'alert alert-success';
        feedback.style.display = 'block';
    }
});
