<?php
require_once __DIR__ . '/../models/Post.php';

class PostApiController {
    private $postModel;

    public function __construct() {
        $this->postModel = new Post();
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
