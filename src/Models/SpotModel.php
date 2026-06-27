<?php
namespace App\Models;

class SpotModel {
    private $db;

    public function __construct() {
        global $pdo;
        $this->db = $pdo;
    }


    // Récupère la liste des types (normal, handicapée, réservée) pour les selects
    public function getSpotTypes() {
        $stmt = $this->db->query("SELECT * FROM parking_spot_types ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    public function getSpotById($id) {
        $stmt = $this->db->prepare("SELECT * FROM parking_spots WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    // AJOUT: Modification complète d'une place
    public function updateSpot($id, $spotNumber, $typeId, $status) {
        $stmt = $this->db->prepare("UPDATE parking_spots SET spot_number = ?, type_id = ?, status = ? WHERE id = ?");
        return $stmt->execute([$spotNumber, $typeId, $status, $id]);
    }

    // AJOUT: Suppression d'une place
    public function deleteSpot($id) {
        $stmt = $this->db->prepare("DELETE FROM parking_spots WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function updateStatus($spotId, $status) {
        $stmt = $this->db->prepare("UPDATE parking_spots SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $spotId]);
    }


    public function getAvailableSpotsWithNextReservation($startTime, $endTime, $isHandicap = false) {
        // Sélectionne les places qui ne sont pas en maintenance
        // Et récupère la date de la toute prochaine réservation (statut 'confirmed' ou 'pending') après $startTime
        $sql = "SELECT p.id, p.spot_number, p.status, t.name AS type_name,
                       (SELECT MIN(r.start_time) 
                        FROM reservations r 
                        WHERE r.spot_id = p.id 
                          AND r.status IN ('confirmed', 'pending')
                          AND r.start_time >= ?) AS next_reservation_start
                FROM parking_spots p
                JOIN parking_spot_types t ON p.type_id = t.id
                WHERE p.status != 'maintenance' 
                AND p.id NOT IN (
                    SELECT spot_id 
                    FROM reservations 
                    WHERE status IN ('confirmed', 'pending')
                    AND start_time < ? 
                    AND end_time > ?
                )";

        if ($isHandicap) {
            $sql .= " AND t.name LIKE '%handicap%'";
        } 
        
        $sql .= " ORDER BY CAST(p.spot_number AS UNSIGNED) ASC, p.spot_number ASC";
                
        $stmt = $this->db->prepare($sql);
        // Exécution des paramètres dans l'ordre des '?'
        $stmt->execute([$startTime, $endTime, $startTime]);
        return $stmt->fetchAll();
    }
    // Vérifie si une place existe déjà
    public function spotExists($spotNumber) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM parking_spots WHERE spot_number = ?");
        $stmt->execute([(string)$spotNumber]);
        return $stmt->fetchColumn() > 0;
    }

    // AJOUT/MODIFICATION : Création d'une place avec vérification
    public function createSpot($spotNumber, $typeId, $status) {
        if ($this->spotExists($spotNumber)) {
            return false; // Empêche le doublon
        }
        $stmt = $this->db->prepare("INSERT INTO parking_spots (spot_number, type_id, status) VALUES (?, ?, ?)");
        return $stmt->execute([(string)$spotNumber, $typeId, $status]);
    }

    // NOUVEAU : Création de plusieurs places à la suite
    public function createMultipleSpots($startNum, $endNum, $typeId, $status) {
        $successCount = 0;
        $stmt = $this->db->prepare("INSERT INTO parking_spots (spot_number, type_id, status) VALUES (?, ?, ?)");
        
        for ($i = $startNum; $i <= $endNum; $i++) {
            $spotNumber = (string)$i;
            if (!$this->spotExists($spotNumber)) {
                $stmt->execute([$spotNumber, $typeId, $status]);
                $successCount++;
            }
        }
        return $successCount;
    }

    public function getAllSpots($typeId = null, $status = null, $spotSearch = null) {
        $sql = "SELECT * FROM (
                    SELECT s.*, t.name as type_name,
                        CASE
                            WHEN s.status = 'maintenance' THEN 'maintenance'
                            WHEN EXISTS (
                                SELECT 1 FROM reservations r 
                                WHERE r.spot_id = s.id 
                                AND r.status IN ('confirmed', 'pending') 
                                AND NOW() BETWEEN r.start_time AND r.end_time
                            ) THEN 'occupied'
                            ELSE 'free'
                        END as computed_status
                    FROM parking_spots s
                    JOIN parking_spot_types t ON s.type_id = t.id
                ) as spot_data
                WHERE 1=1";
        $params = [];
        
        if ($typeId) {
            $sql .= " AND type_id = ?";
            $params[] = $typeId;
        }
        if ($status) {
            $sql .= " AND computed_status = ?";
            $params[] = $status;
        }
        if ($spotSearch !== null && $spotSearch !== '') {
            $sql .= " AND spot_number LIKE ?";
            $params[] = "%" . $spotSearch . "%";
        }
        
        $sql .= " ORDER BY CAST(spot_number AS UNSIGNED) ASC, spot_number ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}