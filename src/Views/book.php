<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réserver la place <?= htmlspecialchars($spot['spot_number']) ?></title>
    <link rel="stylesheet" href="public/assets/css/global.css">
    <link rel="stylesheet" href="public/assets/css/book.css">
</head>
<body>
    <?php require_once __DIR__ . '/partials/header.php'; ?>
    <div class="page-wrapper">
    <div class="booking-container">
        <h2 class="header-title">Réservation - Place <?= htmlspecialchars($spot['spot_number']) ?></h2>

        <?php
        $getStartStr = $_GET['start_time'] ?? '';
        $getEndStr = $_GET['end_time'] ?? '';
        
        $getStartDate = $getStartStr ? date('Y-m-d', strtotime($getStartStr)) : '';
        $getStartTime = $getStartStr ? date('H:i', strtotime($getStartStr)) : '';
        $getEndDate = $getEndStr ? date('Y-m-d', strtotime($getEndStr)) : '';
        $getEndTime = $getEndStr ? date('H:i', strtotime($getEndStr)) : '';
        ?>

        <form id="bookingForm" method="POST" action="index.php?action=book&id=<?= $spot['id'] ?>">
            
            <div class="form-group">
                <label>Date et heure d'arrivée</label>
                <div class="flex-row-gap-10">
                    <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($getStartDate ?? '') ?>" required>
                    <input type="time" id="start_time" name="start_time" value="<?= htmlspecialchars($getStartTime ?? '') ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label>Date et heure de départ</label>
                <div class="flex-row-gap-10">
                    <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($getEndDate ?? '') ?>" required <?= isset($_GET['end_time']) ? '' : 'disabled' ?>>
                    <input type="time" id="end_time" name="end_time" value="<?= htmlspecialchars($getEndTime ?? '') ?>" required <?= isset($_GET['end_time']) ? '' : 'disabled' ?>>
                </div>
            </div>
            <div id="duration-feedback" class="feedback-box">
                
                <strong class="strong-dark">Tarif :</strong> <span id="price-text" class="price-accent">--</span><br>
                <strong class="strong-dark">Temps de la réservation :</strong> <span id="duration-text">--</span><br>
                <strong class="strong-dark">Base tarifaire appliquée :</strong> <span id="applied-tarif-text" class="font-weight-500">--</span>
            </div>

            <h3 class="payment-section-title">Informations de paiement</h3>
            
            <div class="form-group">
                <label>Nom sur la carte</label>
                <input type="text" id="cc_name" placeholder="Ex: Jean Dupont" required autocomplete="cc-name">
                <div id="err_cc_name" class="error-msg">Veuillez entrer un nom valide (min 3 lettres).</div>
            </div>
            
            <div class="form-group">
                <label>Numéro de carte bleue</label>
                <input type="text" id="cc_number" placeholder="0000 0000 0000 0000" maxlength="19" required autocomplete="cc-number">
                <div id="err_cc_number" class="error-msg">Le numéro de carte doit comporter 16 chiffres.</div>
            </div>
            
            <div class="flex-row-gap-15">
                <div class="form-group flex-1">
                    <label>Date d'expiration</label>
                    <input type="text" id="cc_expiry" placeholder="MM/AA" maxlength="5" required autocomplete="cc-exp">
                    <div id="err_cc_expiry" class="error-msg">Date invalide ou carte expirée.</div>
                </div>
                <div class="form-group flex-1">
                    <label>CVV</label>
                    <input type="password" id="cc_cvv" placeholder="123" maxlength="3" required autocomplete="cc-csc">
                    <div id="err_cc_cvv" class="error-msg">Le CVV doit comporter 3 chiffres.</div>
                </div>
            </div>

            <button type="submit" id="submitBtn" class="btn-submit" disabled>Payer de manière sécurisée</button>
        </form>
    </div>

    <!-- Modale de confirmation de paiement -->
    <div id="paymentModal" class="modal-overlay">
        <div class="modal-box">
            <h3 class="modal-title">Confirmer le paiement</h3>
            <p>Êtes-vous sûr de vouloir valider le paiement et réserver cette place ?</p>
            <div class="modal-action-row">
                <button type="button" id="cancelPaymentBtn" class="btn-modal btn-cancel">Non, annuler</button>
                <button type="button" id="confirmPaymentBtn" class="btn-modal btn-confirm">Oui, valider</button>
            </div>
        </div>
    </div>
    </div>
    <script>
        window.tarifs = <?= json_encode($tarifs ?? []) ?>;
    </script>
    <script src="public/js/book.js" defer></script>
    <?php require_once __DIR__ . '/partials/footer.php'; ?>
</body>
</html>