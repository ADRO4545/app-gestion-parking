<link rel="stylesheet" href="public/assets/css/header.css">

<header class="main-header">
    <div class="header-logo-group">
        <div class="header-logo">Parking</div>
        
        <!-- Badge indiquant le rôle -->
        <?php if (!isset($_SESSION['user_id'])): ?>
            <span class="header-badge header-badge-visitor">Non connecté</span>
        <?php elseif (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 2): ?>
            <span class="header-badge header-badge-admin">Admin</span>
        <?php else: ?>
            <span class="header-badge header-badge-client">Client</span>
        <?php endif; ?>
    </div>

    <nav class="header-nav">
        <!-- Liens pour les Visiteurs (Non connectés) -->
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="index.php?action=login" class="link-connexion">Connexion</a>
            <a href="index.php?action=register" class="link-register">S'inscrire</a>
        
        <!-- Liens pour les Administrateurs (role_id = 2) -->
        <?php elseif (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 2): ?>
            <span class="header-user-info">👤 <?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></span>
            <a href="index.php?action=admin_dashboard">Dashboard Admin</a>
            <a href="index.php?action=account">Mon Compte</a>
            <a href="index.php?action=logout" class="link-logout">Déconnexion</a>
        
        <!-- Liens pour les Clients (role_id = 1) -->
        <?php else: ?>
            <span class="header-user-info">👤 <?= htmlspecialchars($_SESSION['user_name'] ?? 'Client') ?></span>
            <a href="index.php?action=dashboard">Réserver une place</a>
            <a href="index.php?action=my_reservations">Mes Réservations</a>
            <a href="index.php?action=account">Mon Compte</a>
            <a href="index.php?action=logout" class="link-logout">Déconnexion</a>
        <?php endif; ?>
    </nav>
</header>