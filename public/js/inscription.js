/**
 * @file inscription.js
 * @description Gère la logique d'inscription avec vérification d'email par code.
 */

document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('actionBtn');
    const codeGroup = document.getElementById('code_group');
    const codeInput = document.getElementById('verification_code');
    const emailInput = document.getElementById('email');
    const form = document.getElementById('registerForm');
    const feedback = document.getElementById('feedback');
    
    let step = 1;

    form.addEventListener('submit', (e) => {
        if (step === 1) {
            e.preventDefault();
            
            const email = emailInput.value;
            btn.textContent = "Envoi en cours...";
            btn.style.opacity = "0.7";
            btn.disabled = true;
            
            fetch('index.php?action=send_code_ajax', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'email=' + encodeURIComponent(email)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    step = 2;
                    codeGroup.style.display = 'block';
                    btn.textContent = "Valider";
                    btn.className = "btn-validate";
                    btn.disabled = false;
                    btn.style.opacity = "1";
                    
                    emailInput.readOnly = true;
                    emailInput.style.background = "#f4f6f9";
                    
                    showSuccess("Un code à 6 chiffres a été envoyé à votre email.");
                    codeInput.focus();
                } else {
                    showError(data.message || "Erreur lors de l'envoi du code.");
                    btn.textContent = "Envoyer";
                    btn.disabled = false;
                    btn.style.opacity = "1";
                }
            })
            .catch(err => {
                showError("Erreur réseau. Impossible de contacter le serveur.");
                btn.textContent = "Envoyer";
                btn.disabled = false;
                btn.style.opacity = "1";
            });
        } else if (step === 2) {
            if (codeInput.value.length !== 6) {
                e.preventDefault();
                showError("Veuillez entrer les 6 chiffres du code.");
            }
        }
    });

    codeInput.addEventListener('input', function() {
        if (step === 2) {
            this.value = this.value.replace(/[^0-9]/g, '');
            
            if (this.value.length === 6) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        }
    });

    /**
     * Affiche un message d'erreur.
     * @param {string} msg - Message à afficher.
     */
    function showError(msg) {
        feedback.textContent = msg;
        feedback.className = 'alert alert-error';
        feedback.style.display = 'block';
    }

    /**
     * Affiche un message de succès.
     * @param {string} msg - Message à afficher.
     */
    function showSuccess(msg) {
        feedback.textContent = msg;
        feedback.className = 'alert alert-success';
        feedback.style.display = 'block';
    }
});
