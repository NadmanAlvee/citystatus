<?php
require_once 'lib/DBConfig.php';

class Post {
    private $connection;

    public function __construct($db) {
        $this->connection = $db;
    }

    public function getAllPosts() {
        $sql = "SELECT * FROM posts p LEFT JOIN users u ON p.user_id = u.user_id ORDER BY p.created_at DESC";
        $res = $this->connection->query($sql);
        return ($res) ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function incrementVote($id, $type) {
        $column = ($type === 'up') ? 'upvote' : (($type === 'down') ? 'downvote' : 'report_count');
        
        $query = "UPDATE posts SET $column = $column + 1 WHERE post_id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $res = $this->connection->query("SELECT $column FROM posts WHERE post_id = $id");
            return $res->fetch_assoc()[$column];
        }
        return false;
    }

    public function getPostById($id) {
        $sql = "SELECT p.*, u.name 
                FROM posts p 
                LEFT JOIN users u ON p.user_id = u.user_id 
                WHERE p.post_id = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getUserPosts($userId) {
        $query = "SELECT post_id, text, division, city, upvote, downvote, created_at 
                  FROM posts WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function create($text, $division, $city, $userId) {
        $stmt = $this->connection->prepare("INSERT INTO posts (text, division, city, user_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $text, $division, $city, $userId);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->connection->prepare("DELETE FROM posts WHERE post_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function getAllAreas() {
        $sql = "SELECT area_id, division, city FROM areas ORDER BY division, city";
        $res = $this->connection->query($sql);
        return ($res) ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function createArea($division, $city) {
        $stmt = $this->connection->prepare("INSERT INTO areas (division, city) VALUES (?, ?)");
        $stmt->bind_param("ss", $division, $city);
        $stmt->execute();
        return [
            'success' => $stmt->affected_rows > 0,
            'id' => $stmt->insert_id
        ];
    }
}
?>
