<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Réservation</title>
    <link rel="stylesheet" href="public/assets/css/global.css">
    <link rel="stylesheet" href="public/assets/css/dashboard.css">
</head>
<body>
    <div class="box">
        <?php require_once __DIR__ . '/partials/header.php'; ?>
        
        <h2>Bienvenue, <?= htmlspecialchars($_SESSION['user_name']) ?> !</h2>
        
        <form method="GET" action="index.php" class="search-box">
            <input type="hidden" name="action" value="dashboard">
            <div>
                <label>Date d'arrivée :</label>
                <input type="datetime-local" name="start_time" value="<?= htmlspecialchars($searchStart ?? '') ?>" required>
            </div>
            <div>
                <label>Date de départ :</label>
                <input type="datetime-local" name="end_time" value="<?= htmlspecialchars($searchEnd ?? '') ?>" required>
            </div>
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px;">
                <input type="checkbox" name="is_handicap" id="is_handicap" value="1" <?= isset($_GET['is_handicap']) ? 'checked' : '' ?> style="width: auto;">
                <label for="is_handicap" style="margin: 0; cursor: pointer;">Place handicapée</label>
            </div>
            <button type="submit" class="btn-search">🔍 Rechercher</button>
        </form>

        <?php if (!empty($erreur)): ?>
            <p style="color:red; font-weight:bold;"><?= $erreur ?></p>
        <?php endif; ?>

        <?php if ($searched): ?>
            <h3>🚗 Places disponibles pour vos dates sélectionnées :</h3>
        <?php else: ?>
            <h3>🚗 Places disponibles actuellement (Live à l'instant T) :</h3>
        <?php endif; ?>

        <?php if (empty($places)): ?>
            <p style="color: #e74c3c; font-weight: bold; margin-top: 20px;">Aucune place disponible pour cette période. Essayez d'autres horaires.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Numéro</th>
                        <th>Type</th>
                        <th>Statut / Prochaine réservation</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($places as $place): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($place['spot_number']) ?></strong></td>
                            <td><span style="text-transform: capitalize;"><?= htmlspecialchars($place['type_name']) ?></span></td>
                            <td>
                                <?php if (empty($place['next_reservation_start'])): ?>
                                    <span class="badge-info" style="background: #e8f8f5; color: #2ecc71;">🟢 Libre (Aucune réservation future)</span>
                                <?php else: ?>
                                    <span class="badge-info" style="background: #fef9e7; color: #d35400;">🟡 Libre jusqu'au <?= date('d/m/Y à H:i', strtotime($place['next_reservation_start'])) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="index.php?action=book&id=<?= $place['id'] ?>&start_time=<?= urlencode($searchStart) ?>&end_time=<?= urlencode($searchEnd) ?>" class="btn-reserve">Réserver</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <?php require_once __DIR__ . '/partials/footer.php'; ?>
</body>
</html>