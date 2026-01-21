<?php
require_once 'lib/DBConfig.php';

class Post {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAllPosts() {
        $sql = "SELECT p.post_id, p.text, p.division, p.city, p.created_at, p.user_id, u.name, u.email
                FROM posts p LEFT JOIN users u ON p.user_id = u.user_id
                ORDER BY p.created_at DESC";
        $res = $this->conn->query($sql);
        return ($res) ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function create($text, $division, $city, $userId) {
        $stmt = $this->conn->prepare("INSERT INTO posts (text, division, city, user_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $text, $division, $city, $userId);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM posts WHERE post_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function getAllAreas() {
        $sql = "SELECT area_id, division, city FROM areas ORDER BY division, city";
        $res = $this->conn->query($sql);
        return ($res) ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function createArea($division, $city) {
        $stmt = $this->conn->prepare("INSERT INTO areas (division, city) VALUES (?, ?)");
        $stmt->bind_param("ss", $division, $city);
        $stmt->execute();
        return [
            'success' => $stmt->affected_rows > 0,
            'id' => $stmt->insert_id
        ];
    }
}
?>
