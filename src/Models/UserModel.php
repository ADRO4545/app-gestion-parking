<?php
namespace App\Models;

class UserModel {
    private $db;

    public function __construct() {
        global $pdo;
        $this->db = $pdo;
    }

    public function getUserByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function getUsersWithFilters($sortName = '', $emailSearch = '', $roleId = '', $status = '', $dateStart = '', $dateEnd = '', $hasReserved = '') {
        $sql = "SELECT u.*, r.name AS role_name, 
                       (SELECT COUNT(*) FROM reservations res WHERE res.user_id = u.id) as total_reservations 
                FROM users u 
                JOIN roles r ON u.role_id = r.id 
                WHERE 1=1";
        $params = [];

        if (!empty($emailSearch)) {
            $sql .= " AND u.email LIKE ?";
            $params[] = "%" . $emailSearch . "%"; 
        }
        if (!empty($roleId)) {
            $sql .= " AND u.role_id = ?";
            $params[] = $roleId;
        }
        if (!empty($status)) {
            $sql .= " AND u.status = ?";
            $params[] = $status;
        }
        if (!empty($dateStart)) {
            $sql .= " AND DATE(u.created_at) >= ?";
            $params[] = $dateStart;
        }
        if (!empty($dateEnd)) {
            $sql .= " AND DATE(u.created_at) <= ?";
            $params[] = $dateEnd;
        }
        
        if ($hasReserved === 'yes') {
            $sql .= " AND (SELECT COUNT(*) FROM reservations res WHERE res.user_id = u.id) > 0";
        } elseif ($hasReserved === 'no') {
            $sql .= " AND (SELECT COUNT(*) FROM reservations res WHERE res.user_id = u.id) = 0";
        }

        if ($sortName === 'asc') {
            $sql .= " ORDER BY u.name ASC";
        } elseif ($sortName === 'desc') {
            $sql .= " ORDER BY u.name DESC";
        } else {
            $sql .= " ORDER BY u.id DESC"; 
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }   

    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE users SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function deleteUser($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function createUserVerified($name, $email, $password, $phone) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $role_id = 1;
        
        $stmt = $this->db->prepare("INSERT INTO users (name, email, password_hash, phone, role_id, is_verified) VALUES (?, ?, ?, ?, ?, 1)");
        return $stmt->execute([$name, $email, $password_hash, $phone, $role_id]);
    }

    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    public function updateProfile($id, $name, $email, $phone) {
        $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
        return $stmt->execute([$name, $email, $phone, $id]);
    }
}