<?php
error_reporting(0);
require_once '../a_func.php';

header('Content-Type: application/json; charset=utf-8;');

function game_return($status, $message, $payload = [])
{
    $json = array_merge(['message' => $message], $payload);
    http_response_code($status ? 200 : 400);
    echo json_encode($json, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    game_return(false, "Method '{$_SERVER['REQUEST_METHOD']}' not allowed!");
}

if (!isset($_SESSION['id'])) {
    game_return(false, 'กรุณาเข้าสู่ระบบ');
}

$admin = dd_q('SELECT id FROM users WHERE id = ? AND rank = 1 LIMIT 1', [$_SESSION['id']]);
if (!$admin || $admin->rowCount() === 0) {
    game_return(false, 'ไม่มีสิทธิ์ใช้งานเมนูนี้');
}

$mode = $_POST['mode'] ?? 'create';
$gameId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$name = trim($_POST['name'] ?? '');
$type = trim($_POST['type'] ?? '');
$entryCost = max(0, (int) ($_POST['entry_cost'] ?? 0));
$status = isset($_POST['status']) && (int) $_POST['status'] === 1 ? 1 : 0;
$image = trim($_POST['image'] ?? '');
$description = trim($_POST['description'] ?? '');
$configRaw = $_POST['config'] ?? '{}';

$allowedTypes = ['wheel', 'number', 'rps'];
if (!in_array($type, $allowedTypes, true)) {
    game_return(false, 'ประเภทเกมไม่ถูกต้อง');
}

if ($name === '') {
    game_return(false, 'กรุณากรอกชื่อเกม');
}

$configData = json_decode($configRaw, true);
if (!is_array($configData)) {
    $configData = [];
}

if ($type === 'wheel') {
    $duration = max(1500, (int) ($configData['spin_duration'] ?? 4500));
    $pointer = $configData['pointer_color'] ?? '#ffffff';
    $configData = [
        'spin_duration' => $duration,
        'pointer_color' => $pointer
    ];
} elseif ($type === 'number') {
    $min = (int) ($configData['number_min'] ?? 1);
    $max = (int) ($configData['number_max'] ?? 9);
    if ($min >= $max) {
        game_return(false, 'ตั้งค่าช่วงตัวเลขให้ถูกต้อง (ค่าต่ำสุดต้องน้อยกว่าค่าสูงสุด)');
    }
    $configData = [
        'number_min' => $min,
        'number_max' => $max,
        'consolation' => trim($configData['consolation'] ?? '')
    ];
} elseif ($type === 'rps') {
    $configData = [
        'host' => trim($configData['host'] ?? 'Dealer AI'),
        'intro' => trim($configData['intro'] ?? '')
    ];
}

$configJson = json_encode($configData, JSON_UNESCAPED_UNICODE);

if ($mode === 'edit') {
    if ($gameId <= 0) {
        game_return(false, 'ไม่พบรหัสเกม');
    }

    $update = dd_q(
        "UPDATE game_sets SET name = ?, image = ?, description = ?, entry_cost = ?, is_active = ?, config = ? WHERE id = ?",
        [$name, $image, $description, $entryCost, $status, $configJson, $gameId]
    );

    if ($update) {
        game_return(true, 'อัปเดตข้อมูลเกมเรียบร้อยแล้ว');
    }

    game_return(false, 'ไม่สามารถบันทึกข้อมูลเกมได้');
} else {
    $insert = dd_q(
        "INSERT INTO game_sets (name, image, description, type, entry_cost, is_active, config) VALUES (?,?,?,?,?,?,?)",
        [$name, $image, $description, $type, $entryCost, $status, $configJson]
    );

    if ($insert) {
        game_return(true, 'เพิ่มเกมใหม่เรียบร้อยแล้ว');
    }

    game_return(false, 'ไม่สามารถเพิ่มเกมได้');
}
