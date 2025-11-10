<?php
error_reporting(0);
require_once '../a_func.php';

header('Content-Type: application/json; charset=utf-8;');

function game_status_return($status, $message)
{
    http_response_code($status ? 200 : 400);
    echo json_encode(['message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    game_status_return(false, "Method '{$_SERVER['REQUEST_METHOD']}' not allowed!");
}

if (!isset($_SESSION['id'])) {
    game_status_return(false, 'กรุณาเข้าสู่ระบบ');
}

$admin = dd_q('SELECT id FROM users WHERE id = ? AND rank = 1 LIMIT 1', [$_SESSION['id']]);
if (!$admin || $admin->rowCount() === 0) {
    game_status_return(false, 'ไม่มีสิทธิ์ใช้งานเมนูนี้');
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$status = isset($_POST['status']) && (int) $_POST['status'] === 1 ? 1 : 0;

if ($id <= 0) {
    game_status_return(false, 'ไม่พบรหัสเกม');
}

$update = dd_q("UPDATE game_sets SET is_active = ? WHERE id = ?", [$status, $id]);

if ($update) {
    game_status_return(true, 'อัปเดตสถานะเกมเรียบร้อยแล้ว');
}

game_status_return(false, 'ไม่สามารถอัปเดตสถานะได้');
