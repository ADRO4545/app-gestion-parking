<h3>Gestion des Tarifs et Règles</h3>

<div class="admin-section-layout">
    <!-- Tableau des tarifs -->
    <div class="admin-section-main">
        <table>
            <tr>
                <th>Nom de la règle</th>
                <th>Heure de début</th>
                <th>Heure de fin</th>
                <th>Tarif (15 min)</th>
                <th>Priorité</th>
                <th>Actions</th>
            </tr>
            <?php if (!empty($tarifs)): ?>
                <?php foreach ($tarifs as $tarif): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($tarif['name']) ?></strong></td>
                        <td><?= htmlspecialchars(substr($tarif['start_time'], 0, 5)) ?></td>
                        <td><?= htmlspecialchars(substr($tarif['end_time'], 0, 5)) ?></td>
                        <td><strong class="amount-positive"><?= number_format($tarif['rate_per_15min'], 2) ?> €</strong></td>
                        <td><?= (int)$tarif['priority'] ?></td>
                        <td>
                            <div class="table-actions">
                                <a href="index.php?action=admin_dashboard&section=rules&edit_id=<?= $tarif['id'] ?>&name=<?= urlencode($tarif['name']) ?>&start=<?= urlencode($tarif['start_time']) ?>&end=<?= urlencode($tarif['end_time']) ?>&rate=<?= urlencode($tarif['rate_per_15min']) ?>&priority=<?= urlencode($tarif['priority']) ?>" class="btn btn-info btn-sm">Modifier</a>
                                
                                <form method="POST" action="index.php?action=admin_dashboard&section=rules&sub_action=delete" class="form-delete form-inline">
                                    <input type="hidden" name="tarif_id" value="<?= $tarif['id'] ?>">
                                    <button type="submit" class="btn btn-delete btn-sm">Supprimer</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr class="table-empty"><td colspan="6">Aucune règle tarifaire trouvée.</td></tr>
            <?php endif; ?>
        </table>
    </div>

    <!-- Formulaire d'ajout / modification -->
    <?php 
    $isEditingTarif = isset($_GET['edit_id']);
    $tarifAction = $isEditingTarif ? 'edit' : 'add';
    $tarifTitle = $isEditingTarif ? "Modifier la règle" : "Ajouter une règle";
    ?>
    <div class="admin-section-sidebar">
        <h4><?= $tarifTitle ?></h4>
        
        <form method="POST" action="index.php?action=admin_dashboard&section=rules&sub_action=<?= $tarifAction ?>">
            <?php if ($isEditingTarif): ?>
                <input type="hidden" name="tarif_id" value="<?= htmlspecialchars($_GET['edit_id']) ?>">
            <?php endif; ?>
            
            <div class="admin-form-group">
                <label>Nom :</label>
                <input type="text" name="name" required
                        value="<?= htmlspecialchars($_GET['name'] ?? '') ?>" placeholder="Ex: Tarif de Nuit">
            </div>
            
            <div class="admin-form-row">
                <div class="admin-form-group">
                    <label>Début :</label>
                    <input type="time" name="start_time" required
                            value="<?= htmlspecialchars($_GET['start'] ?? '') ?>">
                </div>
                <div class="admin-form-group">
                    <label>Fin :</label>
                    <input type="time" name="end_time" required
                            value="<?= htmlspecialchars($_GET['end'] ?? '') ?>">
                </div>
            </div>
            
            <div class="admin-form-group">
                <label>Tarif pour 15 min (€) :</label>
                <input type="number" step="0.01" name="rate_per_15min" required
                        value="<?= htmlspecialchars($_GET['rate'] ?? '') ?>" placeholder="Ex: 1.50">
            </div>
            
            <div class="admin-form-group">
                <label>Priorité (le plus haut est pris en compte) :</label>
                <input type="number" name="priority" required
                        value="<?= htmlspecialchars($_GET['priority'] ?? '1') ?>">
            </div>
            
            <button type="submit" class="btn btn-submit-full">
                <?= $isEditingTarif ? 'Enregistrer les modifications' : 'Créer la règle' ?>
            </button>
            
            <?php if ($isEditingTarif): ?>
                <a href="index.php?action=admin_dashboard&section=rules" class="btn btn-cancel-full">Annuler</a>
            <?php endif; ?>
        </form>
    </div>
</div>
