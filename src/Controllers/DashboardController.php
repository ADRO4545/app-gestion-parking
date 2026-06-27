<?php
namespace App\Controllers;
use App\Models\SpotModel;

class DashboardController {
    
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        $spotModel = new SpotModel();
        
        $searched = false;
        $searchStart = $_GET['start_time'] ?? '';
        $searchEnd = $_GET['end_time'] ?? '';
        $isHandicap = isset($_GET['is_handicap']) ? true : false;
        $places = [];
        $erreur = "";

        // Si l'utilisateur n'a pas encore fait de recherche, on initialise avec la date/heure actuelle
        if (empty($searchStart) || empty($searchEnd)) {
            $now = new \DateTime();
            $searchStart = $now->format('Y-m-d\TH:i');
            // Par défaut, on propose une simulation de disponibilité sur un créneau d'une heure
            $searchEnd = (clone $now)->modify('+1 hour')->format('Y-m-d\TH:i');
        } else {
            $searched = true;
        }

        // Exécution de la recherche avec récupération de la prochaine réservation
        if ($searchStart < $searchEnd) {
            $places = $spotModel->getAvailableSpotsWithNextReservation($searchStart, $searchEnd, $isHandicap);
        } else {
            $erreur = "La date de départ doit être postérieure à la date d'arrivée.";
            // En cas d'erreur de saisie, on affiche tout de même les places actuelles pour éviter une page vide
            $now = new \DateTime();
            $places = $spotModel->getAvailableSpotsWithNextReservation($now->format('Y-m-d\TH:i'), (clone $now)->modify('+1 hour')->format('Y-m-d\TH:i'), $isHandicap);
        }

        require_once __DIR__ . '/../Views/dashboard.php';
    }
}