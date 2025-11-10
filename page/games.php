<?php
$gameStmt = dd_q("SELECT * FROM game_sets WHERE is_active = 1 ORDER BY FIELD(type,'wheel','number','rps'), id DESC");
$games = $gameStmt ? $gameStmt->fetchAll(PDO::FETCH_ASSOC) : [];

$collections = [
    'wheel' => [],
    'number' => [],
    'rps' => []
];

$colorPalette = ['#ff6b6b', '#feca57', '#54a0ff', '#5f27cd', '#10ac84', '#ff9ff3', '#48dbfb', '#ff9f43'];

function build_wheel_view(array $rewards, array $palette): array
{
    if (empty($rewards)) {
        $rewards[] = [
            'id' => 0,
            'label' => 'กำลังตั้งค่ารางวัล',
            'color' => '#343a40',
            'weight' => 1
        ];
    }

    $segments = [];
    $totalWeight = 0;
    foreach ($rewards as $index => $reward) {
        $weight = max(1, (int) ($reward['weight'] ?? 1));
        $segments[] = [
            'id' => (int) ($reward['id'] ?? 0),
            'label' => $reward['label'] ?? 'รางวัล',
            'color' => ($reward['color'] ?? '') ?: $palette[$index % count($palette)],
            'weight' => $weight
        ];
        $totalWeight += $weight;
    }

    $cursor = 0;
    $gradient = [];
    foreach ($segments as &$segment) {
        $portion = ($segment['weight'] / $totalWeight) * 360;
        $start = $cursor;
        $end = $cursor + $portion;
        $segment['start'] = $start;
        $segment['end'] = $end;
        $segment['center'] = $start + ($portion / 2);
        $gradient[] = "{$segment['color']} {$start}deg {$end}deg";
        $cursor = $end;
    }

    return [
        'segments' => $segments,
        'gradient' => implode(', ', $gradient),
        'total' => $totalWeight
    ];
}

foreach ($games as $game) {
    $rewardStmt = dd_q("SELECT * FROM game_rewards WHERE game_id = ? ORDER BY sort_order ASC, id ASC", [$game['id']]);
    $game['config'] = json_decode($game['config'] ?? '{}', true) ?? [];
    $game['rewards'] = $rewardStmt ? $rewardStmt->fetchAll(PDO::FETCH_ASSOC) : [];

    if ($game['type'] === 'wheel') {
        $game['wheel'] = build_wheel_view($game['rewards'], $colorPalette);
    }

    if (isset($collections[$game['type']])) {
        $collections[$game['type']][] = $game;
    }
}

$gameScriptVersion = @filemtime(__DIR__ . '/../system/js/game.js') ?: time();
?>

<section class="page-heading" data-aos="fade-up">
    <div>
        <span class="eyebrow-pill">Game Arcade</span>
        <h1>ลานมินิเกม</h1>
        <p>สุ่มวงล้อ ทายเลข และดวลเป่ายิ้งฉุบเพื่อรับของรางวัล สนุกได้ทุกเมื่อที่คุณต้องการ</p>
    </div>
</section>

<?php if (empty($games)) : ?>
    <section class="empty-state">
        <h3>ยังไม่มีเกมให้เล่น</h3>
        <p>ผู้ดูแลยังไม่เปิด Mini Game หากต้องการให้เปิดใช้งานสามารถแจ้งแอดมินได้เลย</p>
    </section>
<?php else : ?>
    <?php if (!empty($collections['wheel'])) : ?>
        <section class="game-section" data-aos="fade-up">
            <header class="section-head">
                <div>
                    <p class="eyebrow">Spin & Win</p>
                    <h2>วงล้อมหาเฮง</h2>
                </div>
            </header>
            <div class="game-grid">
                <?php foreach ($collections['wheel'] as $wheel) :
                    $wheelData = $wheel['wheel'];
                    $gamePayload = [
                        'id' => (int) $wheel['id'],
                        'type' => 'wheel',
                        'cost' => (int) $wheel['entry_cost'],
                        'config' => $wheel['config'],
                        'segments' => $wheelData['segments']
                    ];
                ?>
                    <article class="game-card wheel-card" data-game='<?= e(json_encode($gamePayload)); ?>'>
                        <div class="wheel-illustration">
                            <div class="wheel-pointer" style="--pointer-color: <?= e($wheel['config']['pointer_color'] ?? '#ffffff'); ?>"></div>
                            <div class="wheel-graphic" data-wheel-graphic style="background: conic-gradient(<?= e($wheelData['gradient']); ?>);"></div>
                        </div>
                        <div class="game-card__body">
                            <div>
                                <p class="eyebrow"><?= e($wheel['description']); ?></p>
                                <h3><?= e($wheel['name']); ?></h3>
                                <p class="text-muted mb-3">กดปุ่มหมุนเพื่อสุ่มรับหนึ่งรางวัลจากวงล้อ</p>
                            </div>
                            <div class="game-card__footer">
                                <div class="price-chip">ค่าเล่น ฿<?= number_format((int) $wheel['entry_cost']); ?></div>
                                <button class="cta-btn spin-btn" data-action="spin">หมุนเลย</button>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if (!empty($collections['number'])) : ?>
        <section class="game-section" data-aos="fade-up" data-aos-delay="100">
            <header class="section-head">
                <div>
                    <p class="eyebrow">Number Match</p>
                    <h2>เกมเดาเลข</h2>
                </div>
            </header>
            <div class="game-grid">
                <?php foreach ($collections['number'] as $numberGame) :
                    $config = $numberGame['config'];
                    $min = (int) ($config['number_min'] ?? 1);
                    $max = (int) ($config['number_max'] ?? 9);
                    $payload = [
                        'id' => (int) $numberGame['id'],
                        'type' => 'number',
                        'cost' => (int) $numberGame['entry_cost'],
                        'min' => $min,
                        'max' => $max,
                        'config' => $config
                    ];
                ?>
                    <article class="game-card number-card" data-game='<?= e(json_encode($payload)); ?>'>
                        <div class="game-card__body">
                            <p class="eyebrow"><?= e($numberGame['name']); ?></p>
                            <h3>เลือกเลข <?= $min; ?> - <?= $max; ?></h3>
                            <p class="text-muted">ถ้าทายถูกตรงกับเลขที่ระบบสุ่ม คุณรับรางวัลทันที</p>
                            <div class="number-input-group">
                                <input type="number" min="<?= $min; ?>" max="<?= $max; ?>" class="form-control guess-input" placeholder="ใส่เลขของคุณ">
                                <button class="ghost-btn" data-action="guess">ส่งคำตอบ</button>
                            </div>
                            <div class="game-card__footer">
                                <div class="price-chip">ค่าเล่น ฿<?= number_format((int) $numberGame['entry_cost']); ?></div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if (!empty($collections['rps'])) : ?>
        <section class="game-section" data-aos="fade-up" data-aos-delay="150">
            <header class="section-head">
                <div>
                    <p class="eyebrow">Rock · Paper · Scissors</p>
                    <h2>ดวลกับบอท</h2>
                </div>
            </header>
            <div class="game-grid">
                <?php foreach ($collections['rps'] as $rpsGame) :
                    $payload = [
                        'id' => (int) $rpsGame['id'],
                        'type' => 'rps',
                        'cost' => (int) $rpsGame['entry_cost'],
                        'config' => $rpsGame['config']
                    ];
                ?>
                    <article class="game-card rps-card" data-game='<?= e(json_encode($payload)); ?>'>
                        <div class="game-card__body">
                            <p class="eyebrow"><?= e($rpsGame['config']['host'] ?? 'Dealer AI'); ?></p>
                            <h3><?= e($rpsGame['name']); ?></h3>
                            <p class="text-muted"><?= e($rpsGame['description'] ?: 'เลือกค้อน กรรไกร หรือกระดาษ ถ้าชนะรับรางวัลทันที'); ?></p>
                            <div class="rps-buttons">
                                <button class="rps-btn" data-choice="rock">
                                    <i class="fa-duotone fa-hand-back-fist"></i>
                                    <span>ค้อน</span>
                                </button>
                                <button class="rps-btn" data-choice="paper">
                                    <i class="fa-duotone fa-hand"></i>
                                    <span>กระดาษ</span>
                                </button>
                                <button class="rps-btn" data-choice="scissors">
                                    <i class="fa-duotone fa-hand-scissors"></i>
                                    <span>กรรไกร</span>
                                </button>
                            </div>
                            <div class="game-card__footer">
                                <div class="price-chip">ค่าเล่น ฿<?= number_format((int) $rpsGame['entry_cost']); ?></div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
<?php endif; ?>

<script src="system/js/game.js?v=<?= $gameScriptVersion; ?>"></script>
