<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Sécurisée</title>
    <link rel="stylesheet" href="public/assets/css/global.css">
    <link rel="stylesheet" href="public/assets/css/connexion.css">
</head>
<body>
    <?php require_once __DIR__ . '/partials/header.php'; ?>
    <div class="page-wrapper">
    <div class="box">
        <h2>Se connecter</h2>
        
        <?php if (!empty($erreur)) echo "<div class='alert alert-error'>$erreur</div>"; ?>
        <div id="feedback" class="alert"></div>
        
        <form id="loginForm" method="POST" action="index.php?action=login">
            <label>Email :</label>
            <input type="email" name="email" id="email" required>
            
            <div id="password_group">
                <label>Mot de passe :</label>
                <input type="password" name="password" id="password" required>
            </div>
            
            <div id="code_group">
                <label>Code de vérification (reçu par email) :</label>
                <input type="text" name="code" id="verification_code" maxlength="6" autocomplete="off" placeholder="000000">
            </div>
            
            <button type="submit" id="actionBtn" class="btn-send">Vérifier mes identifiants</button>
        </form>
        <p class="box-footer-link"><a href="index.php?action=register">Créer un compte</a></p>
    </div>
    </div>
    <script src="public/js/connexion.js" defer></script>
    <?php require_once __DIR__ . '/partials/footer.php'; ?>
</body>
</html>