<h3>Gestion des comptes</h3>

<form method="GET" action="index.php" class="filter-box" id="filterForm" style="flex-wrap: wrap;">
    <input type="hidden" name="action" value="admin_dashboard">
    <input type="hidden" name="section" value="users">
    
    <select name="sort_name">
        <option value="">Tri : Défaut</option>
        <option value="asc" <?= (isset($_GET['sort_name']) && $_GET['sort_name'] == 'asc') ? 'selected' : '' ?>>Nom (A-Z)</option>
        <option value="desc" <?= (isset($_GET['sort_name']) && $_GET['sort_name'] == 'desc') ? 'selected' : '' ?>>Nom (Z-A)</option>
    </select>

    <input type="text" name="email_search" id="emailSearch" placeholder="Rechercher un email..." value="<?= htmlspecialchars($_GET['email_search'] ?? '') ?>">

    <select name="role_id">
        <option value="">Tous les rôles</option>
        <option value="1" <?= (isset($_GET['role_id']) && $_GET['role_id'] == '1') ? 'selected' : '' ?>>Client</option>
        <option value="2" <?= (isset($_GET['role_id']) && $_GET['role_id'] == '2') ? 'selected' : '' ?>>Admin</option>
    </select>

    <select name="user_status">
        <option value="">Tous les statuts</option>
        <option value="active" <?= (isset($_GET['user_status']) && $_GET['user_status'] == 'active') ? 'selected' : '' ?>>Actif</option>
        <option value="banned" <?= (isset($_GET['user_status']) && $_GET['user_status'] == 'banned') ? 'selected' : '' ?>>Désactivé</option>
    </select>

    <div style="display: flex; align-items: center; gap: 5px; background: white; padding: 5px 10px; border: 1px solid #ccc; border-radius: 4px;">
        <label style="font-size: 0.85em; color: #555;">Inscrit entre :</label>
        <input type="date" name="date_start" value="<?= htmlspecialchars($_GET['date_start'] ?? '') ?>">
        <label style="font-size: 0.85em; color: #555;">et</label>
        <input type="date" name="date_end" value="<?= htmlspecialchars($_GET['date_end'] ?? '') ?>">
    </div>

    <select name="has_reserved">
        <option value="">A déjà réservé ?</option>
        <option value="yes" <?= (isset($_GET['has_reserved']) && $_GET['has_reserved'] == 'yes') ? 'selected' : '' ?>>Oui</option>
        <option value="no" <?= (isset($_GET['has_reserved']) && $_GET['has_reserved'] == 'no') ? 'selected' : '' ?>>Non</option>
    </select>

    <button type="submit" class="btn btn-info" id="submitFilterBtn">Appliquer les filtres</button>
    <a href="index.php?action=admin_dashboard&section=users" style="color:#e74c3c; text-decoration:none; font-size:0.9em;">Réinitialiser</a>
</form>

<div id="usersTableContainer">
    <table>
        <tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Création</th><th>Statut</th><th>Actions</th></tr>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u['name']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['role_name'] ?? 'Client') ?></td>
                <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                <td><strong style="color: <?= $u['status'] === 'active' ? '#2ecc71' : '#e74c3c' ?>;"><?= ucfirst($u['status']) ?></strong></td>
                <td>
                    <div style="display:flex; gap:5px;">
                        <form method="POST" action="index.php?action=admin_dashboard&section=users&sub_action=<?= $u['status'] === 'active' ? 'deactivate' : 'activate' ?>">
                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                            <button type="submit" class="btn <?= $u['status'] === 'active' ? 'btn-deactivate' : 'btn-activate' ?>">
                                <?= $u['status'] === 'active' ? 'Désactiver' : 'Activer' ?>
                            </button>
                        </form>
                        
                        <form method="POST" action="index.php?action=admin_dashboard&section=users&sub_action=delete" class="form-delete">
                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                            <button type="submit" class="btn btn-delete">Supprimer</button>
                        </form>

                        <button class="btn btn-info btn-history" data-user-id="<?= $u['id'] ?>" data-user-name="<?= htmlspecialchars($u['name']) ?>">Historique de réservation</button>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<div id="historyModal" class="modal-overlay">
    <div class="modal-content">
        <span class="close-modal" id="closeModal">&times;</span>
        <h3 id="modalTitle">Historique de l'utilisateur</h3>
        
        <div class="modal-filters" style="align-items: center;">
            <input type="text" id="filterSpot" placeholder="N° Place (ex: A1)">
            
            <div style="display: flex; align-items: center; gap: 5px; background: white; padding: 5px; border: 1px solid #ccc; border-radius: 4px;">
                <label style="font-size: 0.85em; color: #555;">Réservé du :</label>
                <input type="date" id="filterDateStart">
                <label style="font-size: 0.85em; color: #555;">au :</label>
                <input type="date" id="filterDateEnd">
            </div>

            <input type="number" id="filterPriceMin" placeholder="Prix Min (€)" style="width: 100px;">
            <input type="number" id="filterPriceMax" placeholder="Prix Max (€)" style="width: 100px;">
            <select id="filterStatus">
                <option value="">Tous les statuts</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>

        <table id="modalTable">
            <thead>
                <tr><th>Place</th><th>Début</th><th>Fin</th><th>Prix</th><th>Statut</th></tr>
            </thead>
            <tbody id="modalTableBody">
            </tbody>
        </table>
    </div>
</div>

<script>
    window.adminHistoryData = <?= $allHistoriesJson ?? '{}' ?>;
</script>
