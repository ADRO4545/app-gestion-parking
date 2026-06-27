<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration</title>
    <link rel="stylesheet" href="public/assets/css/global.css">
    <link rel="stylesheet" href="public/assets/css/admin-dashboard.css">
</head>
<body>

<div class="container">
    <?php require_once __DIR__ . '/partials/header.php'; ?>

    <div class="tabs">
        <a href="index.php?action=admin_dashboard&section=users" class="tab-link <?= $section === 'users' ? 'active' : '' ?>">Utilisateurs</a>
        <a href="index.php?action=admin_dashboard&section=spots" class="tab-link <?= $section === 'spots' ? 'active' : '' ?>">Places</a>
        <a href="index.php?action=admin_dashboard&section=reservations" class="tab-link <?= $section === 'reservations' ? 'active' : '' ?>">Réservations</a>
        <a href="index.php?action=admin_dashboard&section=reports" class="tab-link <?= $section === 'reports' ? 'active' : '' ?>">Rapports</a>
        <a href="index.php?action=admin_dashboard&section=rules" class="tab-link <?= $section === 'rules' ? 'active' : '' ?>">Règles</a>
    </div>

    <?php 
    if ($section === 'users') {
        require_once __DIR__ . '/admin/users.php';
    } elseif ($section === 'spots') {
        require_once __DIR__ . '/admin/spots.php';
    } elseif ($section === 'reservations') {
        require_once __DIR__ . '/admin/reservations.php';
    } elseif ($section === 'reports') {
        require_once __DIR__ . '/admin/reports.php';
    } elseif ($section === 'rules') {
        require_once __DIR__ . '/admin/rules.php';
    }
    ?>

</div> 
<script src="public/js/admin-dashboard.js" defer></script>
<script type="module" src="public/js/admin/app.js"></script>
<?php require_once __DIR__ . '/partials/footer.php'; ?>

</body>
</html>