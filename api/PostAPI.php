<?php
require_once __DIR__ . '/../lib/DBConfig.php';
header('Content-Type: application/json');

$db = new Database();
$conn = $db->getConnection();

$action = $_GET['action'] ?? '';

try {
    if ($action === 'getPosts') {
        $sql = "SELECT p.post_id, p.text, p.division, p.city, p.created_at, p.user_id, u.name, u.email
                FROM posts p LEFT JOIN users u ON p.user_id = u.user_id
                ORDER BY p.created_at DESC";
        $res = $conn->query($sql);
        $rows = [];
        while ($r = $res->fetch_assoc()) $rows[] = $r;
        echo json_encode($rows);
        exit;
    }

    if ($action === 'deletePost' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = intval($data['post_id'] ?? 0);
        $stmt = $conn->prepare("DELETE FROM posts WHERE post_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        echo json_encode(['success' => $stmt->affected_rows > 0]);
        exit;
    }

    if ($action === 'getAreas') {
        $sql = "SELECT area_id, division, city FROM areas ORDER BY division, city";
        $res = $conn->query($sql);
        $rows = [];
        while ($r = $res->fetch_assoc()) $rows[] = $r;
        echo json_encode($rows);
        exit;
    }

    if ($action === 'addArea' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $division = trim($data['division'] ?? '');
        $city = trim($data['city'] ?? '');
        if ($division === '') { http_response_code(400); echo json_encode(['error'=>'division required']); exit; }
        $stmt = $conn->prepare("INSERT INTO areas (division, city) VALUES (?, ?)");
        $stmt->bind_param("ss", $division, $city);
        $stmt->execute();
        echo json_encode(['success' => $stmt->affected_rows > 0, 'id' => $stmt->insert_id]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['error' => 'Invalid action']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
