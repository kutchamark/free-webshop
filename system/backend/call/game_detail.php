<?php
error_reporting(0);
require_once '../../a_func.php';

header('Content-Type: application/json; charset=utf-8;');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => "Method '{$_SERVER['REQUEST_METHOD']}' not allowed!"]);
    exit;
}

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['message' => 'Unauthorized']);
    exit;
}

$admin = dd_q('SELECT id FROM users WHERE id = ? AND rank = 1 LIMIT 1', [$_SESSION['id']]);
if (!$admin || $admin->rowCount() === 0) {
    http_response_code(403);
    echo json_encode(['message' => 'Forbidden']);
    exit;
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['message' => 'Missing id']);
    exit;
}

$stmt = dd_q('SELECT * FROM game_sets WHERE id = ? LIMIT 1', [$id]);
if (!$stmt || $stmt->rowCount() === 0) {
    http_response_code(404);
    echo json_encode(['message' => 'ไม่พบข้อมูล']);
    exit;
}

$row = $stmt->fetch(PDO::FETCH_ASSOC);
$row['config'] = json_decode($row['config'] ?? '{}', true);

echo json_encode($row, JSON_UNESCAPED_UNICODE);
