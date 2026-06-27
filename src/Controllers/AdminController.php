<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Models\SpotModel;
use App\Models\ReservationModel;
use App\Models\TarifModel;

class AdminController {
    
public function dashboard() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
            header('Location: index.php?action=dashboard');
            exit();
        }

        $userModel = new UserModel();
        $spotModel = new SpotModel();
        $reservationModel = new ReservationModel();

        // --- GESTION DES ACTIONS (POST) ---
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['sub_action'])) {
            $section = $_GET['section'] ?? 'users';
            
            // Actions de l'onglet Utilisateurs
            if ($section === 'users') {
                $targetId = $_POST['user_id'] ?? null;
                if ($targetId) {
                    if ($_GET['sub_action'] === 'activate') $userModel->updateStatus($targetId, 'active');
                    if ($_GET['sub_action'] === 'deactivate') $userModel->updateStatus($targetId, 'banned');
                    if ($_GET['sub_action'] === 'delete') $userModel->deleteUser($targetId);
                }
                header('Location: index.php?action=admin_dashboard&section=users');
                exit();
            }

            elseif ($section === 'spots') {
                if ($_GET['sub_action'] === 'add') {
                    $creationMode = $_POST['creation_mode'] ?? 'single';
                    $typeId = $_POST['type_id'];
                    $status = $_POST['status'];

                    if ($creationMode === 'bulk') {
                        $start = (int)$_POST['spot_number_start'];
                        $end = (int)$_POST['spot_number_end'];
                        if ($start > 0 && $end >= $start) {
                            $spotModel->createMultipleSpots($start, $end, $typeId, $status);
                        }
                    } else {
                        $spotNumber = trim($_POST['spot_number']);
                        // On force la vérification numérique
                        if (is_numeric($spotNumber)) {
                            $spotModel->createSpot($spotNumber, $typeId, $status);
                        }
                    }
                }
                if ($_GET['sub_action'] === 'edit') {
                    $spotModel->updateSpot($_POST['spot_id'], $_POST['spot_number'], $_POST['type_id'], $_POST['status']);
                }
                if ($_GET['sub_action'] === 'delete') {
                    $spotModel->deleteSpot($_POST['spot_id']);
                }
                header('Location: index.php?action=admin_dashboard&section=spots');
                exit();
            }
            elseif ($section === 'rules') {
                $tarifModel = new TarifModel();
                if ($_GET['sub_action'] === 'add') {
                    $tarifModel->createTarif($_POST['name'], $_POST['start_time'], $_POST['end_time'], $_POST['rate_per_15min'], (int)$_POST['priority']);
                }
                if ($_GET['sub_action'] === 'edit') {
                    $tarifModel->updateTarif($_POST['tarif_id'], $_POST['name'], $_POST['start_time'], $_POST['end_time'], $_POST['rate_per_15min'], (int)$_POST['priority']);
                }
                if ($_GET['sub_action'] === 'delete') {
                    $tarifModel->deleteTarif($_POST['tarif_id']);
                }
                header('Location: index.php?action=admin_dashboard&section=rules');
                exit();
}
        }

        // --- PRÉPARATION DES DONNÉES SELON L'ONGLET ---
        $section = $_GET['section'] ?? 'users';
        $users = []; $spots = []; $reservations = []; $spotTypes = [];
        $limit = 10; $currentPage = 1; $totalPage = 1;

        if ($section === 'users') {
            $sortName = $_GET['sort_name'] ?? '';
            $emailSearch = $_GET['email_search'] ?? '';
            $roleId = $_GET['role_id'] ?? '';
            $statusFilter = $_GET['user_status'] ?? '';
            $dateStart = $_GET['date_start'] ?? '';
            $dateEnd = $_GET['date_end'] ?? '';
            $hasReserved = $_GET['has_reserved'] ?? '';

            $users = $userModel->getUsersWithFilters($sortName, $emailSearch, $roleId, $statusFilter, $dateStart, $dateEnd, $hasReserved);
            $allHistoriesJson = json_encode($reservationModel->getAllUserReservationsGrouped());
        }
        elseif ($section === 'rules') {
            $tarifModel = new TarifModel();
            $tarifs = $tarifModel->getAllTarifs();
        }
        // NOUVEAU : Récupération des données pour l'onglet des places
        elseif ($section === 'spots') {
            $typeFilter = $_GET['type_id'] ?? null;
            $statusFilter = $_GET['status'] ?? null;
            $spotSearch = $_GET['spot_search'] ?? null; 
            
            $spots = $spotModel->getAllSpots($typeFilter, $statusFilter, $spotSearch);
            $spotTypes = $spotModel->getSpotTypes(); // Pour charger les listes déroulantes
        } 
        elseif ($section === 'reservations') {
            $filters = [
                'spot' => $_GET['res_spot'] ?? '',
                'email' => $_GET['res_email'] ?? '',
                'status' => $_GET['res_status'] ?? '',
                'date_start' => $_GET['res_date_start'] ?? '',
                'date_end' => $_GET['res_date_end'] ?? '',
            ];

            $allowedLimits = [10, 30, 50, 100];
            $limit = isset($_GET['limit']) && in_array((int)$_GET['limit'], $allowedLimits) ? (int)$_GET['limit'] : 10;
            $currentPage = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
            
            $total = $reservationModel->getTotalReservationsCountWithFilters($filters);
            $totalPage = ceil($total / $limit) ?: 1;
            
            if ($currentPage > $totalPage) $currentPage = $totalPage;
            $offset = ($currentPage - 1) * $limit;
            
            $reservations = $reservationModel->getReservationsWithFilters($limit, $offset, $filters);
        }

        elseif ($section === 'reports') {
            $financialStats = $reservationModel->getFinancialReport();
            $recentPayments = $reservationModel->getRecentPayments(100); // Les 100 derniers paiements
        }

        require_once __DIR__ . '/../Views/admin_dashboard.php';
    }
}