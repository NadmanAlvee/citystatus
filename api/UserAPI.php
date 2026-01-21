<?php
require_once 'models/User.php';

class UserApiController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
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
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';

            $user = $this->userModel->checkLogin($email, $password);

            if ($user) {
                // Start session and store user info if needed
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_type'] = $user['user_type'];

                echo json_encode(['success' => true, 'user' => [
                    'name' => $user['name'],
                    'user_type' => $user['user_type']
                ]]);
            } else {
                $this->sendError("Invalid email or password", 401);
            }
        } catch (Exception $e) {
            $this->sendError($e->getMessage());
        }
    }
}
?>
