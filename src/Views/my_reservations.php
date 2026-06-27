<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Réservations</title>
    <link rel="stylesheet" href="public/assets/css/global.css">
    <link rel="stylesheet" href="public/assets/css/my-reservations.css">
</head>
<body>
    <div class="box">
        <?php require_once __DIR__ . '/partials/header.php'; ?>
        
        <h2 style="border-bottom: 2px solid #eee; padding-bottom: 10px;">Historique de mes réservations</h2>
        
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
                                <button style="background: #f39c12; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-weight: bold;" 
                                    data-id="<?= $res['id'] ?>" 
                                    data-start="<?= date('Y-m-d\TH:i', strtotime($res['start_time'])) ?>" 
                                    data-end="<?= date('Y-m-d\TH:i', strtotime($res['end_time'])) ?>" 
                                    onclick="openEditModal(this)">
                                    Modifier
                                </button>
                                <button style="background: #e74c3c; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-weight: bold; margin-left: 5px;"
                                    data-id="<?= $res['id'] ?>"
                                    onclick="openCancelModal(this)">
                                    Annuler
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding:30px; color:#7f8c8d;">
                        Vous n'avez pas encore effectué de réservation.<br>
                        <a href="index.php?action=dashboard" style="color:#3498db; text-decoration:none; font-weight:bold; display:inline-block; margin-top:10px;">Réserver ma première place</a>
                    </td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
    <div id="editModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); align-items: center; justify-content: center; z-index: 1000;">
        <div style="background: white; padding: 30px; border-radius: 8px; width: 100%; max-width: 400px; position: relative;">
            <span onclick="closeEditModal()" style="position: absolute; top: 15px; right: 20px; font-size: 1.5em; cursor: pointer; color: #e74c3c; font-weight: bold;">&times;</span>
            <h3 style="margin-top: 0; color: #2c3e50;">Modifier la réservation</h3>
            
            
            <form method="POST" action="index.php?action=update_reservation">
                <input type="hidden" name="reservation_id" id="edit_res_id">
                
                <div style="margin-bottom: 15px;">
                    <label style="display:block; font-weight: bold; margin-bottom:5px;">Nouvelle date d'arrivée</label>
                    <input type="datetime-local" name="start_time" id="edit_start_time" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; box-sizing: border-box;">
                </div>
                
                <div style="margin-bottom: 25px;">
                    <label style="display:block; font-weight: bold; margin-bottom:5px;">Nouvelle date de départ</label>
                    <input type="datetime-local" name="end_time" id="edit_end_time" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; box-sizing: border-box;">
                </div>
                
                <button type="submit" style="background: #3498db; color: white; border: none; padding: 12px; width: 100%; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 1em;">Enregistrer les modifications</button>
            </form>
        </div>
    </div>
    
    <div id="cancelModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); align-items: center; justify-content: center; z-index: 1000;">
        <div style="background: white; padding: 30px; border-radius: 8px; width: 100%; max-width: 400px; position: relative; text-align: center;">
            <span onclick="closeCancelModal()" style="position: absolute; top: 15px; right: 20px; font-size: 1.5em; cursor: pointer; color: #e74c3c; font-weight: bold;">&times;</span>
            <h3 style="margin-top: 0; color: #2c3e50;">Annuler la réservation ?</h3>
            <p style="margin-bottom: 25px;">Êtes-vous sûr de vouloir annuler cette réservation ? La place sera libérée. Cette action est irréversible.</p>
            
            <form method="POST" action="index.php?action=cancel_reservation">
                <input type="hidden" name="reservation_id" id="cancel_res_id">
                <div style="display: flex; justify-content: space-between; gap: 10px;">
                    <button type="button" onclick="closeCancelModal()" style="background: #bdc3c7; color: white; border: none; padding: 12px; width: 50%; border-radius: 4px; cursor: pointer; font-weight: bold;">Non, conserver</button>
                    <button type="submit" style="background: #e74c3c; color: white; border: none; padding: 12px; width: 50%; border-radius: 4px; cursor: pointer; font-weight: bold;">Oui, annuler</button>
                </div>
            </form>
        </div>
    </div>

    <script src="public/js/my-reservations.js" defer></script>
    <?php require_once __DIR__ . '/partials/footer.php'; ?>
</body>
</html>