<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Services\EmailService; // NOUVEAU

class AuthController {
    
    public function login() {
        $erreur = "";
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $code = $_POST['code'] ?? '';

            if (!isset($_SESSION['login_code']) || $_SESSION['login_email'] !== $email) {
                $erreur = "Veuillez d'abord valider vos identifiants.";
            } elseif (time() > $_SESSION['login_code_expires']) {
                $erreur = "Le code a expiré. Veuillez recommencer.";
            } elseif ($code !== $_SESSION['login_code']) {
                $erreur = "Code de vérification incorrect.";
            } else {
                // Le code est bon, on connecte définitivement l'utilisateur !
                $userModel = new UserModel();
                $user = $userModel->getUserByEmail($email);
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['role_id'] = $user['role_id']; 
                $_SESSION['user_email'] = $user['email'];
                
                // On nettoie la session des variables de vérification
                unset($_SESSION['login_code'], $_SESSION['login_email'], $_SESSION['login_code_expires']);
                
                header('Location: index.php?action=dashboard');
                exit();
            }
        }
        require_once __DIR__ . '/../Views/connexion.php';
    }

    public function loginAjax() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $userModel = new UserModel();
            $user = $userModel->getUserByEmail($email);

            if ($user && $user['status'] == 'banned') {
                echo json_encode(['success' => false, 'message' => 'Votre compte a été suspendu.']);
                return;
            } elseif ($user && $user['is_verified'] == 0) {
                echo json_encode(['success' => false, 'message' => 'Veuillez d\'abord vérifier votre email via le lien d\'inscription.']);
                return;
            } elseif ($user && password_verify($password, $user['password_hash'])) {
                // Les identifiants sont corrects -> On génère et on envoie le code
                $code = sprintf("%06d", mt_rand(1, 999999));
                $_SESSION['login_email'] = $email;
                $_SESSION['login_code'] = $code;
                $_SESSION['login_code_expires'] = time() + (15 * 60);

                $emailService = new EmailService();
                $emailService->sendLoginCode($email, $code);

                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Email ou mot de passe incorrect.']);
            }
        }
    }

    public function sendCodeAjax() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Email invalide.']);
                return;
            }

            $userModel = new UserModel();
            if ($userModel->getUserByEmail($email)) {
                echo json_encode(['success' => false, 'message' => 'Cet email est déjà utilisé.']);
                return;
            }

            // Génération du code et stockage en session
            $code = sprintf("%06d", mt_rand(1, 999999));
            $_SESSION['register_email'] = $email;
            $_SESSION['register_code'] = $code;
            $_SESSION['register_code_expires'] = time() + (15 * 60); // Expire dans 15 min

            $emailService = new EmailService();
            $emailService->sendVerificationCode($email, $code);

            echo json_encode(['success' => true]);
        }
    }

    public function register() {
        $erreur = "";
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $code = $_POST['code'] ?? '';
            
            // Vérifications du code soumis
            if (!isset($_SESSION['register_code']) || $_SESSION['register_email'] !== $email) {
                $erreur = "Veuillez d'abord demander un code de vérification.";
            } elseif (time() > $_SESSION['register_code_expires']) {
                $erreur = "Le code a expiré. Veuillez rafraîchir la page.";
            } elseif ($code !== $_SESSION['register_code']) {
                $erreur = "Code de vérification incorrect.";
            } else {
                $userModel = new UserModel();
                if ($userModel->getUserByEmail($email)) {
                    $erreur = "Cet email est déjà utilisé.";
                } else {
                    $fullName = trim($_POST['prenom']) . ' ' . trim($_POST['nom']);
                    
                    // Création du compte validé
                    $userModel->createUserVerified($fullName, $email, $_POST['password'], $_POST['phone']);
                    
                    // Nettoyage de la session
                    unset($_SESSION['register_code'], $_SESSION['register_email'], $_SESSION['register_code_expires']);
                    
                    header('Location: index.php?action=login');
                    exit();
                }
            }
        }
        require_once __DIR__ . '/../Views/inscription.php';
    }


    public function logout() {
        session_destroy();
        header('Location: index.php?action=login');
        exit();
    }
}