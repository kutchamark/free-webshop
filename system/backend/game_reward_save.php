<?php
error_reporting(0);
require_once '../a_func.php';

header('Content-Type: application/json; charset=utf-8;');

function reward_return($status, $message)
{
    http_response_code($status ? 200 : 400);
    echo json_encode(['message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    reward_return(false, "Method '{$_SERVER['REQUEST_METHOD']}' not allowed!");
}

if (!isset($_SESSION['id'])) {
    reward_return(false, 'กรุณาเข้าสู่ระบบ');
}

$admin = dd_q('SELECT id FROM users WHERE id = ? AND rank = 1 LIMIT 1', [$_SESSION['id']]);
if (!$admin || $admin->rowCount() === 0) {
    reward_return(false, 'ไม่มีสิทธิ์ใช้งานเมนูนี้');
}

$rewardId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$gameId = isset($_POST['game_id']) ? (int) $_POST['game_id'] : 0;
$label = trim($_POST['label'] ?? '');
$rewardType = $_POST['reward_type'] ?? 'text';
$rewardAmount = max(0, (int) ($_POST['reward_amount'] ?? 0));
$rewardValue = trim($_POST['reward_value'] ?? '');
$weight = max(0, (int) ($_POST['weight'] ?? 0));
$sortOrder = (int) ($_POST['sort_order'] ?? 0);
$color = trim($_POST['color'] ?? '');
$image = trim($_POST['image'] ?? '');
$ruleValue = trim($_POST['rule_value'] ?? '');

if ($gameId <= 0) {
    reward_return(false, 'ไม่พบรหัสเกม');
}

$gameStmt = dd_q('SELECT type FROM game_sets WHERE id = ? LIMIT 1', [$gameId]);
if (!$gameStmt || $gameStmt->rowCount() === 0) {
    reward_return(false, 'ข้อมูลเกมไม่ถูกต้อง');
}

$game = $gameStmt->fetch(PDO::FETCH_ASSOC);
$gameType = $game['type'];

$allowedRewardTypes = ['text', 'points'];
if (!in_array($rewardType, $allowedRewardTypes, true)) {
    reward_return(false, 'ประเภทของรางวัลไม่รองรับ');
}

if ($label === '') {
    reward_return(false, 'กรุณากรอกชื่อรางวัล');
}

if ($rewardType === 'points' && $rewardAmount <= 0) {
    reward_return(false, 'จำนวนพอยท์ต้องมากกว่า 0');
}

if ($gameType === 'wheel' && $weight <= 0) {
    reward_return(false, 'วงล้อจำเป็นต้องกำหนดน้ำหนักเปอร์เซ็นต์');
}

if ($gameType === 'number' && !in_array($ruleValue, ['match', 'miss'], true)) {
    reward_return(false, 'เกมเดาเลขต้องเลือกเงื่อนไขทายถูกหรือทายผิด');
}

if ($gameType === 'rps' && !in_array($ruleValue, ['win', 'draw', 'lose'], true)) {
    reward_return(false, 'เกมเป่ายิ้งฉุบต้องเลือกผลลัพธ์ที่ต้องการ');
}

if ($gameType === 'wheel') {
    $ruleValue = '';
}

$params = [
    'game_id' => $gameId,
    'label' => $label,
    'reward_type' => $rewardType,
    'reward_value' => $rewardValue,
    'reward_amount' => $rewardAmount,
    'weight' => $weight,
    'color' => $color,
    'image' => $image,
    'rule_value' => $ruleValue,
    'sort_order' => $sortOrder
];

if ($rewardId > 0) {
    $params['id'] = $rewardId;
    $update = dd_q(
        "UPDATE game_rewards SET label = :label, reward_type = :reward_type, reward_value = :reward_value, reward_amount = :reward_amount,
         weight = :weight, color = :color, image = :image, rule_value = :rule_value, sort_order = :sort_order
         WHERE id = :id AND game_id = :game_id",
        $params
    );

    if ($update) {
        reward_return(true, 'อัปเดตรางวัลเรียบร้อยแล้ว');
    }
    reward_return(false, 'ไม่สามารถอัปเดตรางวัลได้');
} else {
    $insert = dd_q(
        "INSERT INTO game_rewards (game_id, label, reward_type, reward_value, reward_amount, weight, color, image, rule_value, sort_order)
        VALUES (:game_id, :label, :reward_type, :reward_value, :reward_amount, :weight, :color, :image, :rule_value, :sort_order)",
        $params
    );

    if ($insert) {
        reward_return(true, 'เพิ่มรางวัลใหม่เรียบร้อยแล้ว');
    }

    reward_return(false, 'ไม่สามารถเพิ่มรางวัลได้');
}
