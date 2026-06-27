<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Compte</title>
    <link rel="stylesheet" href="public/assets/css/global.css">
    <link rel="stylesheet" href="public/assets/css/account.css">
</head>
<body>
    <div class="box">
        <?php require_once __DIR__ . '/partials/header.php'; ?>

        <h2>Gestion de mon profil</h2>

        <?php if (!empty($successMsg)): ?>
            <div class="alert-success"><?= htmlspecialchars($successMsg) ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?action=update_account">
            <div class="form-group">
                <label>Nom complet</label>
                <input type="text" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label>Adresse Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label>Téléphone</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
            </div>

            <button type="submit" class="btn-send">Mettre à jour mes informations</button>
        </form>
    </div>
    <?php require_once __DIR__ . '/partials/footer.php'; ?>
</body>
</html>