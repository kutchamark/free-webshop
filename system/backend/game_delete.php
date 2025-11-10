<?php
error_reporting(0);
require_once '../a_func.php';

header('Content-Type: application/json; charset=utf-8;');

function game_delete_return($status, $message)
{
    http_response_code($status ? 200 : 400);
    echo json_encode(['message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    game_delete_return(false, "Method '{$_SERVER['REQUEST_METHOD']}' not allowed!");
}

if (!isset($_SESSION['id'])) {
    game_delete_return(false, 'กรุณาเข้าสู่ระบบ');
}

$admin = dd_q('SELECT id FROM users WHERE id = ? AND rank = 1 LIMIT 1', [$_SESSION['id']]);
if (!$admin || $admin->rowCount() === 0) {
    game_delete_return(false, 'ไม่มีสิทธิ์ใช้งานเมนูนี้');
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
if ($id <= 0) {
    game_delete_return(false, 'ไม่พบรหัสเกม');
}

dd_q('DELETE FROM game_rewards WHERE game_id = ?', [$id]);
$delete = dd_q('DELETE FROM game_sets WHERE id = ?', [$id]);

if ($delete) {
    game_delete_return(true, 'ลบข้อมูลเกมเรียบร้อยแล้ว');
}

game_delete_return(false, 'ไม่สามารถลบข้อมูลเกมได้');
