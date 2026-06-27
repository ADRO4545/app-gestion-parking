<h3>Gestion des Tarifs et Règles</h3>

<div style="display: flex; gap: 25px; align-items: flex-start; flex-wrap: wrap;">
    <!-- Tableau des tarifs -->
    <div style="flex: 2; min-width: 600px;">
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
                        <td><strong style="color: #2ecc71;"><?= number_format($tarif['rate_per_15min'], 2) ?> €</strong></td>
                        <td><?= (int)$tarif['priority'] ?></td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <a href="index.php?action=admin_dashboard&section=rules&edit_id=<?= $tarif['id'] ?>&name=<?= urlencode($tarif['name']) ?>&start=<?= urlencode($tarif['start_time']) ?>&end=<?= urlencode($tarif['end_time']) ?>&rate=<?= urlencode($tarif['rate_per_15min']) ?>&priority=<?= urlencode($tarif['priority']) ?>" class="btn btn-info" style="padding: 5px 10px; font-size: 0.85em;">Modifier</a>
                                
                                <form method="POST" action="index.php?action=admin_dashboard&section=rules&sub_action=delete" class="form-delete" style="display:inline;">
                                    <input type="hidden" name="tarif_id" value="<?= $tarif['id'] ?>">
                                    <button type="submit" class="btn btn-delete" style="padding: 5px 10px; font-size: 0.85em;">Supprimer</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" style="text-align:center;">Aucune règle tarifaire trouvée.</td></tr>
            <?php endif; ?>
        </table>
    </div>

    <!-- Formulaire d'ajout / modification -->
    <?php 
    $isEditingTarif = isset($_GET['edit_id']);
    $tarifAction = $isEditingTarif ? 'edit' : 'add';
    $tarifTitle = $isEditingTarif ? "Modifier la règle" : "Ajouter une règle";
    ?>
    <div style="flex: 1; min-width: 320px; background: #f8fafc; padding: 20px; border-radius: 6px; border: 1px solid #e2e8f0;">
        <h4 style="margin-top: 0; margin-bottom: 15px; color: #2c3e50; border-bottom: 2px solid #34495e; padding-bottom: 8px;"><?= $tarifTitle ?></h4>
        
        <form method="POST" action="index.php?action=admin_dashboard&section=rules&sub_action=<?= $tarifAction ?>">
            <?php if ($isEditingTarif): ?>
                <input type="hidden" name="tarif_id" value="<?= htmlspecialchars($_GET['edit_id']) ?>">
            <?php endif; ?>
            
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px;">Nom :</label>
                <input type="text" name="name" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;"
                        value="<?= htmlspecialchars($_GET['name'] ?? '') ?>" placeholder="Ex: Tarif de Nuit">
            </div>
            
            <div style="display: flex; gap: 10px; margin-bottom: 12px;">
                <div style="flex: 1;">
                    <label style="display: block; font-weight: bold; margin-bottom: 5px;">Début :</label>
                    <input type="time" name="start_time" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;"
                            value="<?= htmlspecialchars($_GET['start'] ?? '') ?>">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-weight: bold; margin-bottom: 5px;">Fin :</label>
                    <input type="time" name="end_time" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;"
                            value="<?= htmlspecialchars($_GET['end'] ?? '') ?>">
                </div>
            </div>
            
            <div style="margin-bottom: 12px;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px;">Tarif pour 15 min (€) :</label>
                <input type="number" step="0.01" name="rate_per_15min" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;"
                        value="<?= htmlspecialchars($_GET['rate'] ?? '') ?>" placeholder="Ex: 1.50">
            </div>
            
            <div style="margin-bottom: 18px;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px;">Priorité (le plus haut est pris en compte) :</label>
                <input type="number" name="priority" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;"
                        value="<?= htmlspecialchars($_GET['priority'] ?? '1') ?>">
            </div>
            
            <button type="submit" class="btn" style="background: #2ecc71; width: 100%; padding: 10px; font-weight: bold;">
                <?= $isEditingTarif ? 'Enregistrer les modifications' : 'Créer la règle' ?>
            </button>
            
            <?php if ($isEditingTarif): ?>
                <a href="index.php?action=admin_dashboard&section=rules" class="btn" style="background: #7f8c8d; display: block; text-align: center; margin-top: 8px; padding: 8px; text-decoration: none;">Annuler</a>
            <?php endif; ?>
        </form>
    </div>
</div>
