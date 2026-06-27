/**
 * @file book.js
 * @description Gère la logique de réservation, le calcul du prix et la validation de la carte bleue.
 */

document.addEventListener('DOMContentLoaded', function() {
    // -- Logique des dates de réservation --
    const startDateInput = document.getElementById('start_date');
    const startTimeInput = document.getElementById('start_time');
    const endDateInput = document.getElementById('end_date');
    const endTimeInput = document.getElementById('end_time');
    
    const durationFeedback = document.getElementById('duration-feedback');
    const durationText = document.getElementById('duration-text');
    const priceText = document.getElementById('price-text');
    const appliedTarifText = document.getElementById('applied-tarif-text');
    const submitBtn = document.getElementById('submitBtn');

    // On récupère les tarifs depuis la variable globale injectée
    const tarifs = window.tarifs || [];

    /**
     * Trouve le tarif applicable pour une heure donnée.
     * @param {Array} tarifs - Tableau des tarifs disponibles.
     * @param {string} currentTimeStr - Heure actuelle (HH:MM:SS).
     * @returns {Object|null} - Le tarif correspondant, ou null.
     */
    function matchTarifJS(tarifs, currentTimeStr) {
        for (let i = 0; i < tarifs.length; i++) {
            let t = tarifs[i];
            let start = t.start_time;
            let end = t.end_time;
            if (start <= end) {
                if (currentTimeStr >= start && currentTimeStr < end) return t;
            } else {
                if (currentTimeStr >= start || currentTimeStr < end) return t;
            }
        }
        return null;
    }

    /**
     * @returns {string|null} - Combine date et heure de début en YYYY-MM-DDTHH:MM
     */
    function getCombinedStart() {
        if (startDateInput.value && startTimeInput.value) return startDateInput.value + 'T' + startTimeInput.value;
        return null;
    }

    /**
     * @returns {string|null} - Combine date et heure de fin en YYYY-MM-DDTHH:MM
     */
    function getCombinedEnd() {
        if (endDateInput.value && endTimeInput.value) return endDateInput.value + 'T' + endTimeInput.value;
        return null;
    }

    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    startDateInput.min = now.toISOString().split('T')[0];

    function handleStartChange() {
        const startVal = getCombinedStart();
        if (startVal) {
            endDateInput.disabled = false;
            endTimeInput.disabled = false;
            endDateInput.min = startDateInput.value;
            calculateDurationAndPrice();
        } else {
            endDateInput.disabled = true;
            endTimeInput.disabled = true;
            submitBtn.disabled = true;
            durationFeedback.style.display = 'none';
        }
    }

    startDateInput.addEventListener('change', handleStartChange);
    startTimeInput.addEventListener('change', handleStartChange);
    endDateInput.addEventListener('change', calculateDurationAndPrice);
    endTimeInput.addEventListener('change', calculateDurationAndPrice);

    /**
     * Calcule la durée et le prix total de la réservation et met à jour l'UI.
     */
    function calculateDurationAndPrice() {
        const startVal = getCombinedStart();
        const endVal = getCombinedEnd();

        if (startVal && endVal) {
            const start = new Date(startVal);
            const end = new Date(endVal);

            if (end > start) {
                const diffInMins = Math.floor((end - start) / 60000);
                const hours = Math.floor(diffInMins / 60);
                const minutes = diffInMins % 60;
                durationText.textContent = (hours > 0 ? hours + 'h ' : '') + (minutes > 0 ? minutes + 'm' : (hours === 0 ? "Moins d'une minute" : ''));

                let totalPrice = 0.0;
                let currentPointer = new Date(start.getTime());

                let appliedRates = new Set();

                while (currentPointer < end) {
                    let h = currentPointer.getHours().toString().padStart(2, '0');
                    let m = currentPointer.getMinutes().toString().padStart(2, '0');
                    let timeStr = `${h}:${m}:00`;

                    let applicableTarif = matchTarifJS(tarifs, timeStr);
                    if (applicableTarif) {
                        totalPrice += parseFloat(applicableTarif.rate_per_15min);
                        appliedRates.add(parseFloat(applicableTarif.rate_per_15min).toFixed(2) + ' € / 15min');
                    }
                    currentPointer.setMinutes(currentPointer.getMinutes() + 15);
                }

                priceText.textContent = totalPrice.toFixed(2) + ' €';

                appliedTarifText.textContent = Array.from(appliedRates).join(', ') || 'Aucun tarif';
                durationFeedback.style.display = 'block';
                submitBtn.disabled = false;
            } else {
                durationFeedback.style.display = 'none';
                submitBtn.disabled = true;
            }
        }
    }

    if (getCombinedStart() && getCombinedEnd()) calculateDurationAndPrice();

    // -- Logique de formatage de la carte de paiement --
    const ccName = document.getElementById('cc_name');
    const ccNumber = document.getElementById('cc_number');
    const ccExpiry = document.getElementById('cc_expiry');
    const ccCvv = document.getElementById('cc_cvv');

    ccNumber.addEventListener('input', function(e) {
        let v = e.target.value.replace(/\D/g, ''); 
        let formatted = v.match(/.{1,4}/g)?.join(' ') || ''; 
        e.target.value = formatted;
    });

    ccExpiry.addEventListener('input', function(e) {
        let v = e.target.value.replace(/\D/g, '');
        if (v.length > 2) {
            v = v.substring(0, 2) + '/' + v.substring(2, 4);
        }
        e.target.value = v;
    });

    ccCvv.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/\D/g, '');
    });


    // -- Logique de Validation & Popup --
    const bookingForm = document.getElementById('bookingForm');
    const paymentModal = document.getElementById('paymentModal');
    
    /**
     * Affiche ou cache une erreur sur un champ du formulaire.
     */
    function showError(inputElement, errorElementId, isError) {
        const errEl = document.getElementById(errorElementId);
        if (isError) {
            inputElement.classList.add('error-input');
            errEl.style.display = 'block';
        } else {
            inputElement.classList.remove('error-input');
            errEl.style.display = 'none';
        }
    }

    bookingForm.addEventListener('submit', function(e) {
        e.preventDefault(); 
        let isValid = true;

        if (ccName.value.trim().length < 3) {
            showError(ccName, 'err_cc_name', true);
            isValid = false;
        } else {
            showError(ccName, 'err_cc_name', false);
        }

        if (ccNumber.value.length !== 19) {
            showError(ccNumber, 'err_cc_number', true);
            isValid = false;
        } else {
            showError(ccNumber, 'err_cc_number', false);
        }

        const expRegex = /^(0[1-9]|1[0-2])\/\d{2}$/;
        let expiryValid = false;
        if (expRegex.test(ccExpiry.value)) {
            const parts = ccExpiry.value.split('/');
            const month = parseInt(parts[0], 10);
            const year = parseInt("20" + parts[1], 10);
            
            const currentDate = new Date();
            const currentMonth = currentDate.getMonth() + 1;
            const currentYear = currentDate.getFullYear();

            if (year > currentYear || (year === currentYear && month >= currentMonth)) {
                expiryValid = true;
            }
        }
        if (!expiryValid) {
            showError(ccExpiry, 'err_cc_expiry', true);
            isValid = false;
        } else {
            showError(ccExpiry, 'err_cc_expiry', false);
        }

        if (ccCvv.value.length !== 3) {
            showError(ccCvv, 'err_cc_cvv', true);
            isValid = false;
        } else {
            showError(ccCvv, 'err_cc_cvv', false);
        }

        if (isValid) {
            paymentModal.style.display = 'flex';
        }
    });

    document.getElementById('cancelPaymentBtn').addEventListener('click', function() {
        paymentModal.style.display = 'none';
    });

    document.getElementById('confirmPaymentBtn').addEventListener('click', function() {
        bookingForm.submit();
    });
});
