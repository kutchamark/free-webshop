<?php
require_once '../a_func.php';

header('Content-Type: application/json; charset=utf-8;');

function game_response($status, $message, $payload = [])
{
    http_response_code($status ? 200 : 400);
    echo json_encode(array_merge(['message' => $message], $payload), JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    game_response(false, "Method '{$_SERVER['REQUEST_METHOD']}' not allowed!");
}

if (!isset($_SESSION['id'])) {
    game_response(false, 'กรุณาเข้าสู่ระบบก่อนเล่นเกม');
}

$gameId = isset($_POST['game_id']) ? (int) $_POST['game_id'] : 0;
$action = $_POST['action'] ?? '';

if ($gameId <= 0) {
    game_response(false, 'ไม่พบรหัสเกม');
}

$gameStmt = dd_q('SELECT * FROM game_sets WHERE id = ? AND is_active = 1 LIMIT 1', [$gameId]);
if (!$gameStmt || $gameStmt->rowCount() === 0) {
    game_response(false, 'เกมนี้ยังไม่พร้อมให้เล่น');
}

$game = $gameStmt->fetch(PDO::FETCH_ASSOC);
$game['config'] = json_decode($game['config'] ?? '{}', true) ?? [];
$cost = max(0, (int) $game['entry_cost']);

$rewardStmt = dd_q('SELECT * FROM game_rewards WHERE game_id = ? ORDER BY sort_order ASC, id ASC', [$gameId]);
$rewards = $rewardStmt ? $rewardStmt->fetchAll(PDO::FETCH_ASSOC) : [];

if ($game['type'] === 'wheel' && count($rewards) < 2) {
    game_response(false, 'ยังไม่มีรางวัลบนวงล้อ');
}

if ($game['type'] === 'number') {
    $hasMatch = array_filter($rewards, function ($reward) {
        return ($reward['rule_value'] ?? '') === 'match';
    });
    if (empty($hasMatch)) {
        game_response(false, 'เกมเดาเลขยังไม่ตั้งค่ารางวัล');
    }
}

$meta = [];
$selectedReward = null;
$choiceValue = null;
$systemValue = null;
$resultState = null;

try {
    global $conn;
    $conn->beginTransaction();

    $userStmt = dd_q('SELECT * FROM users WHERE id = ? FOR UPDATE', [$_SESSION['id']]);
    if (!$userStmt || $userStmt->rowCount() === 0) {
        $conn->rollBack();
        game_response(false, 'ไม่พบข้อมูลผู้ใช้');
    }

    $player = $userStmt->fetch(PDO::FETCH_ASSOC);
    if ((int) $player['point'] < $cost) {
        $conn->rollBack();
        game_response(false, 'พอยท์ไม่เพียงพอ');
    }

    if ($cost > 0) {
        dd_q('UPDATE users SET point = point - ? WHERE id = ?', [$cost, $player['id']]);
    }

    if ($game['type'] === 'wheel') {
        $selectedReward = play_wheel($rewards, $game['config'], $meta);
        $resultState = $selectedReward['label'] ?? '';
    } elseif ($game['type'] === 'number') {
        $guess = isset($_POST['guess']) ? (int) $_POST['guess'] : null;
        $numberResult = play_number_guess($rewards, $game['config'], $guess, $meta);
        $selectedReward = $numberResult['reward'];
        $choiceValue = $numberResult['choice'];
        $systemValue = $numberResult['system'];
        $resultState = $numberResult['state'];
    } elseif ($game['type'] === 'rps') {
        $choice = trim($_POST['choice'] ?? '');
        $rpsResult = play_rps($rewards, $choice, $meta);
        $selectedReward = $rpsResult['reward'];
        $choiceValue = $rpsResult['choice'];
        $systemValue = $rpsResult['system'];
        $resultState = $rpsResult['state'];
    } else {
        throw new RuntimeException('รองรับเฉพาะ 3 เกมนี้เท่านั้น');
    }

    $rewardInfo = apply_reward($selectedReward, $player['id']);

    dd_q(
        'INSERT INTO game_logs (game_id, user_id, username, game_type, entry_cost, choice_value, system_value, result_label, reward_type, reward_detail)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
        [
            $gameId,
            $player['id'],
            $player['username'],
            $game['type'],
            $cost,
            $choiceValue,
            $systemValue,
            $resultState ?: ($rewardInfo['label'] ?? ''),
            $rewardInfo['type'] ?? 'text',
            $rewardInfo['detail'] ?? ''
        ]
    );

    $conn->commit();

    $balanceStmt = dd_q('SELECT point FROM users WHERE id = ?', [$player['id']]);
    $balance = $balanceStmt ? $balanceStmt->fetchColumn() : 0;

    if ($systemValue !== null) {
        $meta['system_value'] = $systemValue;
    }
    if ($choiceValue !== null) {
        $meta['choice_value'] = $choiceValue;
    }
    if ($resultState !== null) {
        $meta['result'] = $resultState;
    }

    game_response(true, 'success', [
        'balance' => (float) $balance,
        'reward' => $rewardInfo,
        'meta' => $meta
    ]);
} catch (Exception $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    game_response(false, $e->getMessage());
}

function play_wheel(array $rewards, array $config, array &$meta)
{
    $segments = [];
    $totalWeight = 0;
    foreach ($rewards as $reward) {
        $weight = max(1, (int) $reward['weight']);
        $segments[] = [
            'reward' => $reward,
            'weight' => $weight
        ];
        $totalWeight += $weight;
    }

    $ticket = random_int(1, $totalWeight);
    $cursorWeight = 0;
    $selected = $segments[0];
    $selectedStartWeight = 0;
    foreach ($segments as $segment) {
        $previousWeight = $cursorWeight;
        $cursorWeight += $segment['weight'];
        if ($ticket <= $cursorWeight) {
            $selected = $segment;
            $selectedStartWeight = $previousWeight;
            break;
        }
    }

    $spinDuration = max(1500, (int) ($config['spin_duration'] ?? 4500));
    $baseRotation = random_int(3, 6) * 360;
    $sliceAngle = ($selected['weight'] / $totalWeight) * 360;
    $startAngle = ($selectedStartWeight / $totalWeight) * 360;
    $centerAngle = $startAngle + ($sliceAngle / 2);
    $offset = random_int(-max(1, (int) ($sliceAngle / 3)), max(1, (int) ($sliceAngle / 3)));
    $targetRotation = $baseRotation + (360 - ($centerAngle + $offset));

    $meta['spin'] = [
        'rotation' => $targetRotation,
        'duration' => $spinDuration
    ];

    return $selected['reward'];
}

function play_number_guess(array $rewards, array $config, $guess, array &$meta)
{
    $min = (int) ($config['number_min'] ?? 1);
    $max = (int) ($config['number_max'] ?? 9);
    if (!is_int($guess) || $guess < $min || $guess > $max) {
        throw new InvalidArgumentException("เลือกเลขระหว่าง {$min} - {$max}");
    }

    $system = random_int($min, $max);
    $meta['system_value'] = $system;
    $meta['choice_value'] = $guess;

    $reward = null;
    $state = 'miss';
    if ($guess === $system) {
        foreach ($rewards as $item) {
            if (($item['rule_value'] ?? '') === 'match') {
                $reward = $item;
                break;
            }
        }
        $state = 'match';
    } else {
        foreach ($rewards as $item) {
            if (($item['rule_value'] ?? '') === 'miss') {
                $reward = $item;
                break;
            }
        }
    }

    if (!$reward) {
        $reward = [
            'label' => $state === 'match' ? 'ชนะเกมเดาเลข' : 'พลาดไปนิดเดียว',
            'reward_type' => 'text',
            'reward_value' => $state === 'match'
                ? 'ระบบยังไม่ได้ตั้งค่ารางวัล ให้ติดต่อแอดมิน'
                : ($config['consolation'] ?? 'ลองใหม่อีกครั้ง!')
        ];
    }

    return [
        'reward' => $reward,
        'choice' => $guess,
        'system' => $system,
        'state' => $state
    ];
}

function play_rps(array $rewards, $choice, array &$meta)
{
    $choices = ['rock', 'paper', 'scissors'];
    if (!in_array($choice, $choices, true)) {
        throw new InvalidArgumentException('เลือกค้อน กระดาษ หรือกรรไกร');
    }

    $ai = $choices[array_rand($choices)];
    $meta['choice_value'] = $choice;
    $meta['system_value'] = $ai;

    $state = determine_rps_state($choice, $ai);

    $reward = null;
    foreach ($rewards as $item) {
        if (($item['rule_value'] ?? '') === $state) {
            $reward = $item;
            break;
        }
    }

    if (!$reward) {
        $reward = [
            'label' => $state === 'win' ? 'คุณชนะ!' : ($state === 'draw' ? 'เสมอกัน' : 'พลาดท่า'),
            'reward_type' => 'text',
            'reward_value' => $state === 'win' ? 'ติดต่อแอดมินเพื่อรับรางวัล' : 'ลองใหม่อีกครั้ง'
        ];
    }

    return [
        'reward' => $reward,
        'choice' => $choice,
        'system' => $ai,
        'state' => $state
    ];
}

function determine_rps_state(string $player, string $ai): string
{
    if ($player === $ai) {
        return 'draw';
    }
    $wins = [
        'rock' => 'scissors',
        'scissors' => 'paper',
        'paper' => 'rock'
    ];
    return $wins[$player] === $ai ? 'win' : 'lose';
}

function apply_reward($reward, int $userId): array
{
    if (!$reward) {
        return [
            'label' => 'ไม่มีรางวัล',
            'type' => 'text',
            'detail' => 'ระบบยังไม่ตั้งค่ารางวัล'
        ];
    }

    $info = [
        'label' => $reward['label'] ?? 'รางวัล',
        'type' => $reward['reward_type'] ?? 'text',
        'detail' => $reward['reward_value'] ?? '',
        'amount' => (int) ($reward['reward_amount'] ?? 0)
    ];

    if ($info['type'] === 'points' && $info['amount'] > 0) {
        dd_q('UPDATE users SET point = point + ? WHERE id = ?', [$info['amount'], $userId]);
    }

    return $info;
}
