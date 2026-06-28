<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Réservations</title>
    <link rel="stylesheet" href="public/assets/css/global.css">
    <link rel="stylesheet" href="public/assets/css/my-reservations.css">
</head>
<body>
    <?php require_once __DIR__ . '/partials/header.php'; ?>
    <div class="page-wrapper">
    <div class="box">
        
        <h2>Historique de mes réservations</h2>
        
        <table>
            <tr>
                <th>Place N°</th>
                <th>Arrivée</th>
                <th>Départ</th>
                <th>Prix Payé</th>
                <th>Statut</th>
                <th>Action</th>
            </tr>
            <?php if(!empty($reservations)): ?>
                <?php foreach ($reservations as $res): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($res['spot_number']) ?></strong></td>
                        <td><?= date('d/m/Y à H:i', strtotime($res['start_time'])) ?></td>
                        <td><?= date('d/m/Y à H:i', strtotime($res['end_time'])) ?></td>
                        <td class="price"><?= number_format($res['total_price'], 2) ?> €</td>
                        <td>
                            <?php 
                                $class = 'status-completed';
                                $label = 'Terminée';
                                
                                if ($res['status'] === 'confirmed') {
                                    $class = 'status-confirmed';
                                    $label = 'Confirmée';
                                } elseif ($res['status'] === 'cancelled') {
                                    $class = 'status-cancelled';
                                    $label = 'Annulée';
                                }
                            ?>
                            <span class="badge-status <?= $class ?>"><?= $label ?></span>
                        </td>
                        <td>
                            <?php if ($res['status'] === 'confirmed'): ?>
                                <div class="flex-row-gap-5">
                                    
                                    <button class="btn btn-warning btn-sm" 
                                        data-id="<?= $res['id'] ?>" 
                                        data-start="<?= date('Y-m-d\TH:i', strtotime($res['start_time'])) ?>" 
                                        data-end="<?= date('Y-m-d\TH:i', strtotime($res['end_time'])) ?>" 
                                        onclick="openEditModal(this)">
                                        Modifier
                                    </button>
                                    
                                    <button class="btn btn-danger btn-sm"
                                        data-id="<?= $res['id'] ?>"
                                        onclick="openCancelModal(this)">
                                        Annuler
                                    </button>
                                    
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="table-empty">
                                        Vous n'avez pas encore effectué de réservation.<br>
                                        <a href="index.php?action=dashboard" class="box-footer-link">Réserver ma première place</a>
                                    </td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
    <div id="editModal" class="modal-overlay">
        <div class="modal-box">
            <span onclick="closeEditModal()" class="close-btn">&times;</span>
            <h3 class="modal-title">Modifier la réservation</h3>
            
            
            <form method="POST" action="index.php?action=update_reservation">
                <input type="hidden" name="reservation_id" id="edit_res_id">
                
                <div class="margin-bottom-15">
                    <label>Nouvelle date d'arrivée</label>
                    <input type="datetime-local" name="start_time" id="edit_start_time" required>
                </div>
                
                <div class="margin-bottom-25">
                    <label>Nouvelle date de départ</label>
                    <input type="datetime-local" name="end_time" id="edit_end_time" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Enregistrer les modifications</button>
            </form>
        </div>
    </div>
    
    <div id="cancelModal" class="modal-overlay">
        <div class="modal-box modal-box-center">
            <span onclick="closeCancelModal()" class="close-btn">&times;</span>
            <h3 class="modal-title">Annuler la réservation ?</h3>
            <p class="margin-bottom-25">Êtes-vous sûr de vouloir annuler cette réservation ? La place sera libérée. Cette action est irréversible.</p>
            
            <form method="POST" action="index.php?action=cancel_reservation">
                <input type="hidden" name="reservation_id" id="cancel_res_id">
                <div class="flex-row-gap-10">
                    <button type="button" onclick="closeCancelModal()" class="btn btn-secondary flex-1">Non, conserver</button>
                    <button type="submit" class="btn btn-danger flex-1">Oui, annuler</button>
                </div>
            </form>
        </div>
    </div>
    </div>
    </div>
    <script src="public/js/my-reservations.js" defer></script>
    <?php require_once __DIR__ . '/partials/footer.php'; ?>
</body>
</html>