<?php
require_once 'lib/DBConfig.php';

class User {
    private $connection;
    
    public function __construct($db) {
        $this->connection = $db;
    }

    public function getAll() {
        $query = "SELECT user_id, name, email, user_type, acc_creation FROM users ORDER BY acc_creation DESC";
        $result = $this->connection->query($query);
        return ($result) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function delete($id) {
        $stmt = $this->connection->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function updateProfile($id, $data) {
        if ($data['password']) {
            // If password is being changed
            $query = "UPDATE users SET name = ?, district = ?, password = ? WHERE user_id = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("sssi", $data['name'], $data['district'], $data['password'], $id);
        } else {
            // Only update name and district
            $query = "UPDATE users SET name = ?, district = ? WHERE user_id = ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("ssi", $data['name'], $data['district'], $id);
        }

        return $stmt->execute();
    }

    public function getUserById($id) {
        $query = "SELECT * FROM users WHERE user_id = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getUserByEmail($email) {
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($data)
    {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $query = "INSERT INTO users (name, email, phone, password, sex, DOB, district, security_q, security_a, user_type) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'member')";
        
        $stmt = $this->connection->prepare($query);
        
        $sex = ucfirst(strtolower($data['sex'])); 

        $stmt->bind_param("sssssssss", 
            $data['name'], 
            $data['email'], 
            $data['phone'], 
            $hashedPassword, 
            $sex, 
            $data['dob'], 
            $data['district'],
            $data['security_q'],
            $data['security_a']
        );

        if ($stmt->execute()) {
            return $this->connection->insert_id;
        }

        error_log("Insert Error: " . $stmt->error);
        return false;
    }

    public function checkLogin($email, $password) {
        $query = "SELECT user_id, name, password, user_type FROM users WHERE email = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            // DEBUG: Check if the hash in DB is truncated (should be 60 chars)
            error_log("DB Hash: " . $user['password']); 
            
            if (password_verify($password, $user['password'])) {
                return $user;
            } else {
                error_log("Password verify failed for " . $email);
            }
        } else {
            error_log("No user found with email " . $email);
        }
        return false;
    }

    public function validate($data)
    {
        $errors = [];
        if (empty($data['name']) || strlen(trim($data['name'])) < 2) {
            $errors['name'] = 'Name must be at least 2 characters long';
        }
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address';
        }
        if (empty($data['password']) || strlen($data['password']) < 6) {
            $errors['password'] = 'Password must be at least 6 characters';
        }
        return $errors;
    }

    public function emailExists($email, $excludeId = null)
    {
        $query = "SELECT user_id FROM users WHERE email = ?";
        if ($excludeId) {
            $query .= " AND user_id != ?";
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("si", $email, $excludeId);
        } else {
            $stmt = $this->connection->prepare($query);
            $stmt->bind_param("s", $email);
        }

        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
}
?>
