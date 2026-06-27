<?php
namespace App\Controllers;

use App\Models\ReservationModel;
use App\Models\SpotModel;
use App\Models\TarifModel;
use App\Services\PaymentService;

class ReservationController {

    /**
     * Trouve le tarif applicable pour une heure précise de la journée
     */
    private function matchTarif(array $tarifs, string $currentTime): ?array {
        foreach ($tarifs as $tarif) {
            $start = $tarif['start_time'];
            $end = $tarif['end_time'];

            // Cas classique : la plage horaire ne traverse pas minuit (ex: 17:00 à 23:00)
            if ($start <= $end) {
                if ($currentTime >= $start && $currentTime < $end) {
                    return $tarif;
                }
            } else { 
                // Cas d'une plage qui traverse minuit (ex: 23:00 à 06:00 du matin)
                if ($currentTime >= $start || $currentTime < $end) {
                    return $tarif;
                }
            }
        }
        return null;
    }
    
    // Gère la page de réservation et le calcul du prix
    public function book() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        $spotId = $_GET['id'] ?? null;
        if (!$spotId) {
            header('Location: index.php?action=dashboard');
            exit();
        }

        $spotModel = new SpotModel();
        $spot = $spotModel->getSpotById($spotId);

        $tarifModel = new TarifModel();
        $tarifs = $tarifModel->getAllTarifs();

        // SI LE FORMULAIRE EST SOUMIS ET QUE LA POPUP JS A ÉTÉ VALIDÉE
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $startTime = $_POST['start_date'] . ' ' . $_POST['start_time']; 
            $endTime = $_POST['end_date'] . ' ' . $_POST['end_time'];
                            
            $startDateTime = new \DateTime($startTime);
            $endDateTime = new \DateTime($endTime);

            // Calcul du prix
            $totalPrice = 0.0;
            $currentPointer = clone $startDateTime;
            while ($currentPointer < $endDateTime) {
                $currentTimeStr = $currentPointer->format('H:i:s');
                $applicableTarif = $this->matchTarif($tarifs, $currentTimeStr);
                
                if ($applicableTarif) {
                    $totalPrice += (float)$applicableTarif['rate_per_15min'];
                }
                $currentPointer->modify('+15 minutes');
            }
            
            $finalPrice = round($totalPrice, 2);

            // -- TRAITEMENT DIRECT EN BASE DE DONNÉES --
            $reservationModel = new ReservationModel();
            
            // 1. Création de la réservation
            $creationResult = $reservationModel->create(
                $_SESSION['user_id'], 
                $spotId, 
                $startTime, 
                $endTime, 
                $finalPrice
            );
            $newResId = $creationResult['id'];
            $reservationNumber = $creationResult['reservation_number'];

            // 2. Création du paiement associé dans la DB (Simulation bancaire validée)
            $transactionId = strtoupper(uniqid()); 
            $reservationModel->createPayment($newResId, $finalPrice, 'card', $transactionId);


            // 4. Récupération de l'email et envoi de la facture
            $userModel = new \App\Models\UserModel();
            $user = $userModel->getUserByEmail($_SESSION['user_email'] ?? '');
            
            if ($user) {
                $emailService = new \App\Services\EmailService();
                $emailDetails = [
                    'reservation_number' => $reservationNumber, // Ajout du numéro ici
                    'spot_number' => $spot['spot_number'],
                    'start_time' => date('d/m/Y H:i', strtotime($startTime)),
                    'end_time' => date('d/m/Y H:i', strtotime($endTime)),
                    'total_price' => $finalPrice,
                    'transaction_id' => $transactionId
                ];
                $emailService->sendReservationConfirmation($user['email'], $emailDetails);
            }

            // 5. Redirection finale vers l'historique
            header('Location: index.php?action=my_reservations');
            exit();
        }

        require_once __DIR__ . '/../Views/book.php';
    }

    // Traite la soumission de la popup de modification
    public function updateReservation() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $resId = $_POST['reservation_id'];
            $startTime = $_POST['start_time'];
            $endTime = $_POST['end_time'];
            
            $startDateTime = new \DateTime($startTime);
            $endDateTime = new \DateTime($endTime);

            // Recalcul du nouveau prix
            $tarifModel = new TarifModel();
            $tarifs = $tarifModel->getAllTarifs();

            $totalPrice = 0.0;
            $currentPointer = clone $startDateTime;

            while ($currentPointer < $endDateTime) {
                $currentTimeStr = $currentPointer->format('H:i:s');
                $applicableTarif = $this->matchTarif($tarifs, $currentTimeStr);
                if ($applicableTarif) {
                    $totalPrice += (float)$applicableTarif['rate_per_15min'];
                }
                $currentPointer->modify('+15 minutes');
            }

            // Mise à jour
            $reservationModel = new ReservationModel();
            $reservationModel->updateReservation($resId, $_SESSION['user_id'], $startTime, $endTime, round($totalPrice, 2));

            $userModel = new \App\Models\UserModel();
            $user = $userModel->getUserByEmail($_SESSION['user_email']); // Nécessite que l'email soit en session
            
            if ($user) {
                // On récupère directement la réservation mise à jour pour avoir le numéro de place
                $updatedRes = $reservationModel->getReservationByIdAndUser($resId, $_SESSION['user_id']);
                $spotNumber = $updatedRes ? $updatedRes['spot_number'] : 'Inconnu';

                $emailService = new \App\Services\EmailService();
                $emailService->sendReservationUpdate($user['email'], [
                    'spot_number' => $spotNumber,
                    'start_time' => date('d/m/Y H:i', strtotime($startTime)),
                    'end_time' => date('d/m/Y H:i', strtotime($endTime)),
                    'total_price' => round($totalPrice, 2)
                ]);
            }

            header('Location: index.php?action=my_reservations');
            exit();
        }
    }
    public function myReservations() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        $reservationModel = new ReservationModel();
        $reservations = $reservationModel->getUserReservations($_SESSION['user_id']);

        require_once __DIR__ . '/../Views/my_reservations.php';
    }

    public function cancelReservation() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $resId = $_POST['reservation_id'];
            $reservationModel = new ReservationModel();
            
            // 1. On vérifie que la réservation appartient bien à l'utilisateur
            $resDetails = $reservationModel->getReservationByIdAndUser($resId, $_SESSION['user_id']);

            if ($resDetails && $resDetails['status'] === 'confirmed') {
                // 2. Mettre la réservation en 'cancelled'
                $reservationModel->updateStatus($resId, 'cancelled');

                // 4. Récupérer l'email et envoyer la confirmation
                $userModel = new \App\Models\UserModel();
                $user = $userModel->getUserByEmail($_SESSION['user_email']);
                if ($user) {
                    $emailService = new \App\Services\EmailService();
                    $emailService->sendReservationCancellation($user['email'], [
                        'spot_number' => $resDetails['spot_number'],
                        'start_time' => date('d/m/Y H:i', strtotime($resDetails['start_time'])),
                        'end_time' => date('d/m/Y H:i', strtotime($resDetails['end_time']))
                    ]);
                }
            }
            // 5. Redirection
            header('Location: index.php?action=my_reservations');
            exit();
        }
    }
}