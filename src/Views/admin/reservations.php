<h3>Gestion globale des réservations</h3>

<form method="GET" action="index.php" class="filter-box">
    <input type="hidden" name="action" value="admin_dashboard">
    <input type="hidden" name="section" value="reservations">
    
    <input type="text" name="res_spot" placeholder="N° Place" value="<?= htmlspecialchars($_GET['res_spot'] ?? '') ?>">
    <input type="text" name="res_email" placeholder="Email Utilisateur" value="<?= htmlspecialchars($_GET['res_email'] ?? '') ?>">
    
    <select name="res_status">
        <option value="">Tous les statuts</option>
        <option value="pending" <?= (isset($_GET['res_status']) && $_GET['res_status'] == 'pending') ? 'selected' : '' ?>>En attente</option>
        <option value="confirmed" <?= (isset($_GET['res_status']) && $_GET['res_status'] == 'confirmed') ? 'selected' : '' ?>>Confirmée</option>
        <option value="cancelled" <?= (isset($_GET['res_status']) && $_GET['res_status'] == 'cancelled') ? 'selected' : '' ?>>Annulée</option>
    </select>

    <div class="filter-date-range">
        <label>Du :</label>
        <input type="date" name="res_date_start" value="<?= htmlspecialchars($_GET['res_date_start'] ?? '') ?>">
        <label>Au :</label>
        <input type="date" name="res_date_end" value="<?= htmlspecialchars($_GET['res_date_end'] ?? '') ?>">
    </div>

    <div class="filter-limit">
        <label>Afficher par :</label>
        <select name="limit" class="auto-submit">
            <option value="10" <?= (isset($limit) && $limit == 10) ? 'selected' : '' ?>>10</option>
            <option value="30" <?= (isset($limit) && $limit == 30) ? 'selected' : '' ?>>30</option>
            <option value="50" <?= (isset($limit) && $limit == 50) ? 'selected' : '' ?>>50</option>
            <option value="100" <?= (isset($limit) && $limit == 100) ? 'selected' : '' ?>>100</option>
        </select>
    </div>

    <button type="submit" class="btn btn-info">Filtrer</button>
    <a href="index.php?action=admin_dashboard&section=reservations" class="link-reset">Réinitialiser</a>
</form>

<table>
    <tr>
        <th>N° Place</th>
        <th>Utilisateur (Email)</th>
        <th>Début</th>
        <th>Fin</th>
        <th>Prix Total</th>
        <th>Statut</th>
    </tr>
    <?php if(!empty($reservations)): ?>
        <?php foreach ($reservations as $r): ?>
            <tr>
                <td><strong><?= htmlspecialchars($r['spot_number']) ?></strong></td>
                <td><?= htmlspecialchars($r['user_email']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($r['start_time'])) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($r['end_time'])) ?></td>
                <td><strong><?= number_format($r['total_price'], 2) ?> €</strong></td>
                <td>
                    <?php 
                        $statusClass = 'status-' . $r['status'];
                    ?>
                    <strong class="<?= $statusClass ?>"><?= ucfirst($r['status']) ?></strong>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr class="table-empty"><td colspan="6">Aucune réservation trouvée pour ces critères.</td></tr>
    <?php endif; ?>
</table>

<?php if (isset($totalPage) && $totalPage > 1): ?>
    <div class="pagination">
        <?php 
        $queryParams = $_GET;
        unset($queryParams['p']); 
        $baseUrl = 'index.php?' . http_build_query($queryParams) . '&p=';
        ?>
        
        <?php if ($currentPage > 1): ?>
            <a href="<?= $baseUrl . ($currentPage - 1) ?>">&laquo; Précédent</a>
        <?php endif; ?>
        
        <?php for ($i = 1; $i <= $totalPage; $i++): ?>
            <a href="<?= $baseUrl . $i ?>" class="<?= $i == $currentPage ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        
        <?php if ($currentPage < $totalPage): ?>
            <a href="<?= $baseUrl . ($currentPage + 1) ?>">Suivant &raquo;</a>
        <?php endif; ?>
    </div>
<?php endif; ?>
