<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" href="public/assets/css/global.css">
    <link rel="stylesheet" href="public/assets/css/inscription.css">
</head>
<body>
    <?php require_once __DIR__ . '/partials/header.php'; ?>
    <div class="page-wrapper">
    <div class="box">
        <h2>Créer un compte</h2>
        
        <?php if (!empty($erreur)) echo "<div class='alert alert-error'>$erreur</div>"; ?>
        <div id="feedback" class="alert"></div>
        
        <form id="registerForm" method="POST" action="index.php?action=register">
            <label>Prénom :</label>
            <input type="text" name="prenom" required>

            <label>Nom :</label>
            <input type="text" name="nom" required>
            
            <label>Email :</label>
            <input type="email" name="email" id="email" required>

            <label>Téléphone :</label>
            <input type="text" name="phone">
            
            <label>Mot de passe :</label>
            <input type="password" name="password" required>
            
            <div id="code_group">
                <label>Code de vérification (reçu par email) :</label>
                <input type="text" name="code" id="verification_code" maxlength="6" autocomplete="off" placeholder="000000">
            </div>
            
            <button type="submit" id="actionBtn" class="btn-send">Valider</button>
        </form>
        <p class="box-footer-link"><a href="index.php?action=login">Déjà inscrit ? Connexion</a></p>
    </div>
    </div>
    <script src="public/js/inscription.js" defer></script>
    <?php require_once __DIR__ . '/partials/footer.php'; ?>
</body>
</html>