<h3>Gestion des places de parking</h3>

<div style="display: flex; gap: 25px; align-items: flex-start; flex-wrap: wrap;">
    
    <div style="flex: 2; min-width: 600px;">
        <form method="GET" action="index.php" class="filter-box" style="gap: 10px;">
            <input type="hidden" name="action" value="admin_dashboard">
            <input type="hidden" name="section" value="spots">
            
            <input type="text" name="spot_search" placeholder="Rechercher un n°..." 
                value="<?= htmlspecialchars($_GET['spot_search'] ?? '') ?>" 
                style="padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; max-width: 150px;">

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
            <a href="index.php?action=admin_dashboard&section=spots" style="color:#e74c3c; text-decoration:none; font-size:0.9em;">Réinitialiser</a>
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
                        <div style="display: flex; gap: 5px;">
                            <a href="index.php?action=admin_dashboard&section=spots&edit_id=<?= $spot['id'] ?>&spot_number=<?= urlencode($spot['spot_number']) ?>&type_id_form=<?= $spot['type_id'] ?>&status_form=<?= $spot['status'] ?>" class="btn btn-info" style="padding: 5px 10px; font-size: 0.85em;">Modifier</a>
                            
                            <form method="POST" action="index.php?action=admin_dashboard&section=spots&sub_action=delete" class="form-delete" style="display:inline;">
                                <input type="hidden" name="spot_id" value="<?= $spot['id'] ?>">
                                <button type="submit" class="btn btn-delete" style="padding: 5px 10px; font-size: 0.85em;">Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if(empty($spots)): ?>
                <tr><td colspan="4" style="text-align:center;">Aucune place enregistrée.</td></tr>
            <?php endif; ?>
        </table>
    </div>

    <?php 
    $isEditing = isset($_GET['edit_id']);
    $formAction = $isEditing ? 'edit' : 'add';
    $formTitle = $isEditing ? "⚙️ Modifier la place" : "➕ Ajouter une place";
    ?>
    <div style="flex: 1; min-width: 320px; background: #f8fafc; padding: 20px; border-radius: 6px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
        <h4 style="margin-top: 0; margin-bottom: 15px; color: #2c3e50; border-bottom: 2px solid #34495e; padding-bottom: 8px; font-size: 1.1em;"><?= $formTitle ?></h4>
        
        <form method="POST" action="index.php?action=admin_dashboard&section=spots&sub_action=<?= $formAction ?>">
            <?php if ($isEditing): ?>
                <input type="hidden" name="spot_id" value="<?= htmlspecialchars($_GET['edit_id']) ?>">
                <div style="margin-bottom: 12px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 5px; font-size: 0.9em; color: #4a5568;">Numéro de la place :</label>
                    <input type="number" name="spot_number" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;"
                            value="<?= htmlspecialchars($_GET['spot_number'] ?? '') ?>" placeholder="Ex: 102" min="1">
                </div>
            <?php else: ?>
                <div style="margin-bottom: 12px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 5px; font-size: 0.9em; color: #4a5568;">Mode d'ajout :</label>
                    <select name="creation_mode" id="creationMode" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                        <option value="single">Une seule place</option>
                        <option value="bulk">Création en lot (ex: de 1 à 154)</option>
                    </select>
                </div>

                <div id="singleSpotMode" style="margin-bottom: 12px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 5px; font-size: 0.9em; color: #4a5568;">Numéro de la place :</label>
                    <input type="number" name="spot_number" id="spot_number_input" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;" placeholder="Ex: 42" min="1" required>
                </div>

                <div id="bulkSpotMode" style="display: none; margin-bottom: 12px;">
                    <div style="display: flex; gap: 10px;">
                        <div style="flex: 1;">
                            <label style="display: block; font-weight: bold; margin-bottom: 5px; font-size: 0.9em; color: #4a5568;">De :</label>
                            <input type="number" name="spot_number_start" id="spot_number_start" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;" placeholder="Ex: 1" min="1">
                        </div>
                        <div style="flex: 1;">
                            <label style="display: block; font-weight: bold; margin-bottom: 5px; font-size: 0.9em; color: #4a5568;">À :</label>
                            <input type="number" name="spot_number_end" id="spot_number_end" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;" placeholder="Ex: 154" min="1">
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div style="margin-bottom: 12px;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px; font-size: 0.9em; color: #4a5568;">Type de place :</label>
                <select name="type_id" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; background: white;">
                    <?php foreach ($spotTypes as $type): ?>
                        <option value="<?= $type['id'] ?>" <?= (isset($_GET['type_id_form']) && $_GET['type_id_form'] == $type['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars(ucfirst($type['name'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom: 18px;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px; font-size: 0.9em; color: #4a5568;">Statut initial :</label>
                <select name="status" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; background: white;">
                    <option value="free" <?= (isset($_GET['status_form']) && $_GET['status_form'] === 'free') ? 'selected' : '' ?>>Libre</option>
                    <option value="maintenance" <?= (isset($_GET['status_form']) && $_GET['status_form'] === 'maintenance') ? 'selected' : '' ?>>En maintenance</option>
                </select>
            </div>

            <button type="submit" class="btn" style="background: #2ecc71; width: 100%; padding: 10px; font-weight: bold; font-size: 0.95em;">
                <?= $isEditing ? 'Enregistrer les modifications' : 'Créer la place' ?>
            </button>
            
            <?php if ($isEditing): ?>
                <a href="index.php?action=admin_dashboard&section=spots" class="btn" style="background: #7f8c8d; display: block; text-align: center; margin-top: 8px; padding: 8px; font-weight: bold; text-decoration: none;">Annuler</a>
            <?php endif; ?>
        </form>
    </div>
</div>
