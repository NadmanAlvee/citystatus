<?php
require_once 'models/User.php';
require_once 'lib/DBConfig.php';

class UserApiController {
    private $userModel;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();

        $this->userModel = new User($db);

        header('Content-Type: application/json');
    }

    private function sendError($message, $code = 500) {
        http_response_code($code);
        echo json_encode(['error' => $message]);
        exit;
    }

    public function getUsers() {
        try {
            $users = $this->userModel->getAll();
            echo json_encode($users);
        } catch (Exception $e) {
            $this->sendError($e->getMessage());
        }
    }

    public function update() {
        session_start();
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'error' => 'Not authenticated']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        // Determine if admin is updating another user or user is updating themselves
        $isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
        $targetUserId = isset($data['user_id']) ? intval($data['user_id']) : $_SESSION['user_id'];
        
        // Regular users can only update themselves
        if (!$isAdmin && $targetUserId !== $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        // Regular users cannot change email or user_type
        if (!$isAdmin) {
            unset($data['email']);
            unset($data['user_type']);
        }

        // Validate required fields
        $requiredFields = ['name', 'phone', 'sex', 'DOB', 'district'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                echo json_encode(['success' => false, 'error' => ucfirst($field) . ' is required']);
                return;
            }
        }

        // Update user profile
        if ($this->userModel->updateProfile($targetUserId, $data)) {
            echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update profile']);
        }
    }

    public function deleteUser() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $id = intval($data['user_id'] ?? 0);

            if ($id <= 0) {
                $this->sendError("Invalid User ID", 400);
            }

            $success = $this->userModel->delete($id);
            echo json_encode(['success' => $success]);
        } catch (Exception $e) {
            $this->sendError($e->getMessage());
        }
    }

    public function login() {
        $data = json_decode(file_get_contents('php://input'), true);
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        $user = $this->userModel->checkLogin($email, $password);

        if ($user) {
            if (session_status() === PHP_SESSION_NONE) session_start();

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];

            if($user['user_type'] === 'admin'){
                $_SESSION['is_admin'] = true;
            }

            echo json_encode(['success' => true]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
        }
    }

    public function signup() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            $errors = $this->userModel->validate($data);

            if (isset($data['email']) && $this->userModel->emailExists($data['email'])) {
                $errors['email'] = 'This email is already registered.';
            }
            if (!empty($errors)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'errors' => $errors]);
                exit;
            }
            $userId = $this->userModel->create($data);

            if ($userId) {
                if (session_status() === PHP_SESSION_NONE) session_start();
                
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_name'] = $data['name'];
                $_SESSION['user_type'] = 'user';

                echo json_encode(['success' => true, 'message' => 'Registration successful!']);
            } else {
                throw new Exception("Could not save user to database.");
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = array();
        session_unset();
        session_destroy();
        echo json_encode(['success' => true]);
        exit;
    }
<<<<<<< HEAD

    
=======
    public function forgotPassword() {
        header('Content-Type: application/json');

        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $email      = trim($data['email'] ?? '');
            $security_q = $data['security_q'] ?? '';
            $security_a = trim($data['security_a'] ?? '');

            if (!$email || !$security_q || !$security_a) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'All fields are required']);
                return;
            }

            $user = $this->userModel->getUserByEmail($email);

            if (!$user) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'No account found with that email']);
                return;
            }

            if ($user['security_q'] !== $security_q || 
                strtolower($user['security_a']) !== strtolower($security_a)) {
                
                http_response_code(401);
                echo json_encode(['success' => false, 'error' => 'Security answer is incorrect']);
                return;
            }

            echo json_encode(['success' => true]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
        }
    }
    public function resetPassword() {
      try {
        $data = json_decode(file_get_contents('php://input'), true);
        $email = $data['email'] ?? '';
        $newPassword = $data['newPassword'] ?? '';

        if (!$email || !$newPassword) {
            http_response_code(400);
            echo json_encode(['error' => 'Email and new password are required']);
            return;
        }

        $user = $this->userModel->getUserByEmail($email);

        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
            return;
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateData = ['password' => $hashedPassword];

        if ($this->userModel->updateProfile($user['user_id'], $updateData)) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update password']);
        }
      } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
      }
    }
>>>>>>> f77d14e8004d437066ae4b798f794df90daf9f06
}
?>
