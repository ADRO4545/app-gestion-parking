<h3>Gestion des places de parking</h3>

<div class="admin-section-layout">
    
    <div class="admin-section-main">
        <form method="GET" action="index.php" class="filter-box" style="gap: 10px;">
            <input type="hidden" name="action" value="admin_dashboard">
            <input type="hidden" name="section" value="spots">
            
            <input type="text" name="spot_search" placeholder="Rechercher un n°..." 
                value="<?= htmlspecialchars($_GET['spot_search'] ?? '') ?>" 
                class="spot-search-input">

            <select name="type_id">
                <option value="">Tous les types</option>
                <?php foreach ($spotTypes as $type): ?>
                    <option value="<?= $type['id'] ?>" <?= (isset($_GET['type_id']) && $_GET['type_id'] == $type['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars(ucfirst($type['name'])) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="status">
                <option value="">Tous les statuts</option>
                <option value="free" <?= (isset($_GET['status']) && $_GET['status'] === 'free') ? 'selected' : '' ?>>Libre</option>
                <option value="occupied" <?= (isset($_GET['status']) && $_GET['status'] === 'occupied') ? 'selected' : '' ?>>Occupée</option>
                <option value="maintenance" <?= (isset($_GET['status']) && $_GET['status'] === 'maintenance') ? 'selected' : '' ?>>En maintenance</option>
            </select>

            <button type="submit" class="btn btn-info">Filtrer</button>
            <a href="index.php?action=admin_dashboard&section=spots" class="link-reset">Réinitialiser</a>
        </form>

        <table>
            <tr>
                <th>N° Place</th>
                <th>Type de place</th>
                <th>Statut actuel</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($spots as $spot): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($spot['spot_number']) ?></strong></td>
                    <td><?= htmlspecialchars(ucfirst($spot['type_name'])) ?></td>
                    <td>
                        <?php 
                        $displayStatus = $spot['computed_status'] ?? $spot['status'];
                        $color = $displayStatus === 'free' ? '#2ecc71' : ($displayStatus === 'occupied' ? '#e74c3c' : '#f39c12');
                        $label = $displayStatus === 'free' ? 'Libre' : ($displayStatus === 'occupied' ? 'Réservée / Occupée' : 'Maintenance');
                        ?>
                        <strong style="color: <?= $color ?>;"><?= $label ?></strong>
                    </td>
                    <td>
                        <div class="table-actions">
                            <a href="index.php?action=admin_dashboard&section=spots&edit_id=<?= $spot['id'] ?>&spot_number=<?= urlencode($spot['spot_number']) ?>&type_id_form=<?= $spot['type_id'] ?>&status_form=<?= $spot['status'] ?>" class="btn btn-info btn-sm">Modifier</a>
                            
                            <form method="POST" action="index.php?action=admin_dashboard&section=spots&sub_action=delete" class="form-delete form-inline">
                                <input type="hidden" name="spot_id" value="<?= $spot['id'] ?>">
                                <button type="submit" class="btn btn-delete btn-sm">Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if(empty($spots)): ?>
                <tr class="table-empty"><td colspan="4">Aucune place enregistrée.</td></tr>
            <?php endif; ?>
        </table>
    </div>

    <?php 
    $isEditing = isset($_GET['edit_id']);
    $formAction = $isEditing ? 'edit' : 'add';
    $formTitle = $isEditing ? "Modifier la place" : "Ajouter une place";
    ?>
    <div class="admin-section-sidebar">
        <h4><?= $formTitle ?></h4>
        
        <form method="POST" action="index.php?action=admin_dashboard&section=spots&sub_action=<?= $formAction ?>">
            <?php if ($isEditing): ?>
                <input type="hidden" name="spot_id" value="<?= htmlspecialchars($_GET['edit_id']) ?>">
                <div class="admin-form-group">
                    <label>Numéro de la place :</label>
                    <input type="number" name="spot_number" required
                            value="<?= htmlspecialchars($_GET['spot_number'] ?? '') ?>" placeholder="Ex: 102" min="1">
                </div>
            <?php else: ?>
                <div class="admin-form-group">
                    <label>Mode d'ajout :</label>
                    <select name="creation_mode" id="creationMode">
                        <option value="single">Une seule place</option>
                        <option value="bulk">Création en lot (ex: de 1 à 154)</option>
                    </select>
                </div>

                <div id="singleSpotMode">
                    <div class="admin-form-group">
                        <label>Numéro de la place :</label>
                        <input type="number" name="spot_number" id="spot_number_input" placeholder="Ex: 42" min="1" required>
                    </div>
                </div>

                <div id="bulkSpotMode">
                    <div class="bulk-row">
                        <div class="admin-form-group">
                            <label>De :</label>
                            <input type="number" name="spot_number_start" id="spot_number_start" placeholder="Ex: 1" min="1">
                        </div>
                        <div class="admin-form-group">
                            <label>À :</label>
                            <input type="number" name="spot_number_end" id="spot_number_end" placeholder="Ex: 154" min="1">
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="admin-form-group">
                <label>Type de place :</label>
                <select name="type_id" required>
                    <?php foreach ($spotTypes as $type): ?>
                        <option value="<?= $type['id'] ?>" <?= (isset($_GET['type_id_form']) && $_GET['type_id_form'] == $type['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars(ucfirst($type['name'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="admin-form-group">
                <label>Statut initial :</label>
                <select name="status" required>
                    <option value="free" <?= (isset($_GET['status_form']) && $_GET['status_form'] === 'free') ? 'selected' : '' ?>>Libre</option>
                    <option value="maintenance" <?= (isset($_GET['status_form']) && $_GET['status_form'] === 'maintenance') ? 'selected' : '' ?>>En maintenance</option>
                </select>
            </div>

            <button type="submit" class="btn btn-submit-full">
                <?= $isEditing ? 'Enregistrer les modifications' : 'Créer la place' ?>
            </button>
            
            <?php if ($isEditing): ?>
                <a href="index.php?action=admin_dashboard&section=spots" class="btn btn-cancel-full">Annuler</a>
            <?php endif; ?>
        </form>
    </div>
</div>
