<?php
require_once __DIR__ . '/../lib/DBConfig.php';
header('Content-Type: application/json');

$db = new Database();
$conn = $db->getConnection();

$action = $_GET['action'] ?? '';

try {
    if ($action === 'getUsers') {
        $sql = "SELECT user_id, name, email, user_type FROM users ORDER BY acc_creation DESC";
        $res = $conn->query($sql);
        $rows = [];
        while ($r = $res->fetch_assoc()) $rows[] = $r;
        echo json_encode($rows);
        exit;
    }

    if ($action === 'deleteUser' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = intval($data['user_id'] ?? 0);
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        echo json_encode(['success' => $stmt->affected_rows > 0]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['error' => 'Invalid action']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
