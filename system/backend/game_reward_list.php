<?php
error_reporting(0);
require_once '../a_func.php';

header('Content-Type: application/json; charset=utf-8;');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => "Method '{$_SERVER['REQUEST_METHOD']}' not allowed!"]);
    exit;
}

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['message' => 'กรุณาเข้าสู่ระบบ']);
    exit;
}

$admin = dd_q('SELECT id FROM users WHERE id = ? AND rank = 1 LIMIT 1', [$_SESSION['id']]);
if (!$admin || $admin->rowCount() === 0) {
    http_response_code(403);
    echo json_encode(['message' => 'ไม่มีสิทธิ์ใช้งานเมนูนี้']);
    exit;
}

$gameId = isset($_POST['game_id']) ? (int) $_POST['game_id'] : 0;
if ($gameId <= 0) {
    echo json_encode([]);
    exit;
}

$stmt = dd_q("SELECT * FROM game_rewards WHERE game_id = ? ORDER BY sort_order ASC, id ASC", [$gameId]);
$rows = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

foreach ($rows as &$row) {
    $row['reward_amount'] = (int) $row['reward_amount'];
    $row['weight'] = (int) $row['weight'];
    $row['sort_order'] = (int) $row['sort_order'];
}

echo json_encode($rows, JSON_UNESCAPED_UNICODE);
