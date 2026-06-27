<?php
namespace App\Models;

class TarifModel {
    private $db;

    public function __construct() {
        global $pdo;
        $this->db = $pdo;
    }

public function getAllTarifs() {
    $sql = "SELECT * FROM tarifs ORDER BY priority DESC";
    $stmt = $this->db->query($sql);
    return $stmt->fetchAll();
}

public function createTarif($name, $startTime, $endTime, $rate, $priority) {
    $stmt = $this->db->prepare("INSERT INTO tarifs (name, start_time, end_time, rate_per_15min, priority) VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([$name, $startTime, $endTime, $rate, $priority]);
}

public function updateTarif($id, $name, $startTime, $endTime, $rate, $priority) {
    $stmt = $this->db->prepare("UPDATE tarifs SET name = ?, start_time = ?, end_time = ?, rate_per_15min = ?, priority = ? WHERE id = ?");
    return $stmt->execute([$name, $startTime, $endTime, $rate, $priority, $id]);
}

public function deleteTarif($id) {
    $stmt = $this->db->prepare("DELETE FROM tarifs WHERE id = ?");
    return $stmt->execute([$id]);
}
}