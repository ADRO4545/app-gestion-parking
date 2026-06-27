<?php
namespace App\Controllers;

use App\Models\UserModel;

class AccountController {
    
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        $userModel = new UserModel();
        $user = $userModel->getUserById($_SESSION['user_id']);
        
        $successMsg = isset($_GET['success']) ? "Votre profil a été mis à jour avec succès." : "";

        require_once __DIR__ . '/../Views/account.php';
    }

    public function update() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $phone = trim($_POST['phone']);

            $userModel = new UserModel();
            // On ne passe plus que l'id, le nom, l'email et le téléphone
            $userModel->updateProfile($_SESSION['user_id'], $name, $email, $phone);

            // Mise à jour de la session
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;

            header('Location: index.php?action=account&success=1');
            exit();
        }
    }
}