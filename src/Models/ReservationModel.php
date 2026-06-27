<?php
namespace App\Models;

class ReservationModel {
    private $db;

    public function __construct() {
        global $pdo;
        $this->db = $pdo;
    }
    
    public function create($userId, $spotId, $startTime, $endTime, $totalPrice) {
        // numéro de réservation composée de l'année du mois et 6 chiffres aléatoires
        $reservationNumber = date('Ym') . sprintf('%06d', mt_rand(0, 999999));

        $sql = "INSERT INTO reservations (reservation_number, user_id, spot_id, start_time, end_time, total_price, status) 
                VALUES (?, ?, ?, ?, ?, ?, 'confirmed')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$reservationNumber, $userId, $spotId, $startTime, $endTime, $totalPrice]);
        
        return [
            'id' => $this->db->lastInsertId(),
            'reservation_number' => $reservationNumber
        ];
    }

    public function createPayment($reservationId, $amount, $method, $transactionId) {
        $sql = "INSERT INTO payments (reservation_id, amount, payment_method, transaction_id, status, paid_at) 
                VALUES (?, ?, ?, ?, 'completed', NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$reservationId, $amount, $method, $transactionId]);
    }
    public function getAllUserReservationsGrouped() {
        $sql = "SELECT r.*, p.spot_number 
                FROM reservations r 
                JOIN parking_spots p ON r.spot_id = p.id 
                ORDER BY r.start_time DESC";
        $stmt = $this->db->query($sql);
        $allRes = $stmt->fetchAll();

        // On groupe par user_id pour le passer facilement au JavaScript
        $grouped = [];
        foreach ($allRes as $res) {
            $grouped[$res['user_id']][] = $res;
        }
        return $grouped;
    }

    public function getReservationsWithFilters($limit, $offset, $filters) {
        $sql = "SELECT r.*, u.name AS user_name, u.email AS user_email, p.spot_number 
                FROM reservations r
                JOIN users u ON r.user_id = u.id
                JOIN parking_spots p ON r.spot_id = p.id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['spot'])) {
            $sql .= " AND p.spot_number LIKE ?";
            $params[] = "%" . $filters['spot'] . "%";
        }
        if (!empty($filters['email'])) {
            $sql .= " AND u.email LIKE ?";
            $params[] = "%" . $filters['email'] . "%";
        }
        if (!empty($filters['status'])) {
            $sql .= " AND r.status = ?";
            $params[] = $filters['status'];
        }
        if (!empty($filters['date_start'])) {
            $sql .= " AND DATE(r.start_time) >= ?";
            $params[] = $filters['date_start'];
        }
        if (!empty($filters['date_end'])) {
            $sql .= " AND DATE(r.start_time) <= ?";
            $params[] = $filters['date_end'];
        }

        $sql .= " ORDER BY r.start_time DESC LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        
        // On bind les paramètres dynamiques
        $paramIndex = 1;
        foreach ($params as $param) {
            $stmt->bindValue($paramIndex++, $param);
        }
        // On bind PDO::PARAM_INT pour le LIMIT et OFFSET
        $stmt->bindValue($paramIndex++, (int)$limit, \PDO::PARAM_INT);
        $stmt->bindValue($paramIndex, (int)$offset, \PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTotalReservationsCountWithFilters($filters) {
        $sql = "SELECT COUNT(*) FROM reservations r
                JOIN users u ON r.user_id = u.id
                JOIN parking_spots p ON r.spot_id = p.id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['spot'])) {
            $sql .= " AND p.spot_number LIKE ?";
            $params[] = "%" . $filters['spot'] . "%";
        }
        if (!empty($filters['email'])) {
            $sql .= " AND u.email LIKE ?";
            $params[] = "%" . $filters['email'] . "%";
        }
        if (!empty($filters['status'])) {
            $sql .= " AND r.status = ?";
            $params[] = $filters['status'];
        }
        if (!empty($filters['date_start'])) {
            $sql .= " AND DATE(r.start_time) >= ?";
            $params[] = $filters['date_start'];
        }
        if (!empty($filters['date_end'])) {
            $sql .= " AND DATE(r.start_time) <= ?";
            $params[] = $filters['date_end'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }
    public function updateReservation($resId, $userId, $startTime, $endTime, $totalPrice) {
        $sql = "UPDATE reservations 
                SET start_time = ?, end_time = ?, total_price = ? 
                WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$startTime, $endTime, $totalPrice, $resId, $userId]);
    }

    public function getReservationByIdAndUser($resId, $userId) {
        $sql = "SELECT r.*, p.spot_number 
                FROM reservations r 
                JOIN parking_spots p ON r.spot_id = p.id 
                WHERE r.id = ? AND r.user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$resId, $userId]);
        return $stmt->fetch();
    }

    public function updateStatus($resId, $status) {
        $sql = "UPDATE reservations SET status = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $resId]);
    }
    // NOUVEAU : Récupération des revenus totaux et du nombre de paiements
    public function getFinancialReport() {
        $sql = "SELECT SUM(amount) as total_revenue, COUNT(id) as total_payments 
                FROM payments 
                WHERE status = 'completed'";
        $stmt = $this->db->query($sql);
        return $stmt->fetch();
    }

    // NOUVEAU : Récupération de l'historique des paiements
    public function getRecentPayments($limit = 50) {
        $sql = "SELECT p.*, r.reservation_number, sp.spot_number
                FROM payments p
                JOIN reservations r ON p.reservation_id = r.id
                JOIN parking_spots sp ON r.spot_id = sp.id
                ORDER BY p.paid_at DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, (int)$limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Récupère l'historique des réservations d'un utilisateur spécifique
     */
    public function getUserReservations($userId) {
        $sql = "SELECT r.*, p.spot_number 
                FROM reservations r 
                JOIN parking_spots p ON r.spot_id = p.id 
                WHERE r.user_id = ? 
                ORDER BY r.start_time DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

}