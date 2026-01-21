<?php
require_once 'models/Post.php';
require_once 'lib/DBConfig.php';

class PostApiController {
    private $postModel;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();

        $this->postModel = new Post($db);
        
        header('Content-Type: application/json');
    }

    private function sendError($message, $code = 500) {
        http_response_code($code);
        echo json_encode(['error' => $message]);
        exit;
    }

    public function getPosts() {
        try {
            $data = $this->postModel->getAllPosts();
            echo json_encode($data);
        } catch (Exception $e) {
            $this->sendError($e->getMessage());
        }
    }

    public function UpvoteOrDownvote() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $id = intval($data['post_id'] ?? 0);
            $type = $data['type'] ?? '';

            if ($id <= 0) {
                $this->sendError("Invalid Post ID", 400);
            }

            if (!in_array($type, ['up', 'down', 'report'])) {
                $this->sendError("Invalid vote type", 400);
            }

            $newCount = $this->postModel->incrementVote($id, $type);

            if ($newCount !== false) {
                echo json_encode(['success' => true, 'new_count' => $newCount]);
            } else {
                $this->sendError("Update failed", 500);
            }
        } catch (Exception $e) {
            $this->sendError($e->getMessage());
        }
    }

    public function addPost() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $text = trim($data['text'] ?? '');
        $div = trim($data['division'] ?? '');
        $city = trim($data['city'] ?? '');

        if (empty($text) || empty($div)) {
            http_response_code(400);
            echo json_encode(['error' => 'Text and Division required']);
            return;
        }

        $success = $this->postModel->create($text, $div, $city, $_SESSION['user_id']);
        echo json_encode(['success' => $success]);
    }

    public function deletePost() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $id = intval($data['post_id'] ?? 0);
            
            if ($id <= 0) $this->sendError("Invalid Post ID", 400);

            $success = $this->postModel->delete($id);
            echo json_encode(['success' => $success]);
        } catch (Exception $e) {
            $this->sendError($e->getMessage());
        }
    }

    public function getAreas() {
        try {
            $data = $this->postModel->getAllAreas();
            echo json_encode($data);
        } catch (Exception $e) {
            $this->sendError($e->getMessage());
        }
    }

    public function addArea() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $division = trim($data['division'] ?? '');
            $city = trim($data['city'] ?? '');
            
            if (empty($division)) $this->sendError('Division is required', 400);

            $result = $this->postModel->createArea($division, $city);
            echo json_encode($result);
        } catch (Exception $e) {
            $this->sendError($e->getMessage());
        }
    }
}
?>
