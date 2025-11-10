<?php
$gameStmt = dd_q("SELECT * FROM game_sets ORDER BY id DESC");
$games = $gameStmt ? $gameStmt->fetchAll(PDO::FETCH_ASSOC) : [];

function game_type_label(string $type): string
{
    switch ($type) {
        case 'wheel':
            return 'วงล้อสุ่มรางวัล';
        case 'number':
            return 'เกมเดาเลข';
        case 'rps':
            return 'เป่ายิ้งฉุบ';
        default:
            return strtoupper($type);
    }
}
?>

<style>
    .game-manage-header {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 1rem;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .game-manage-card {
        background: #0e0e0e;
        border-radius: 16px;
        padding: 1.5rem;
        color: #f5f5f5;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    }

    .btn-gradient {
        border: none;
        border-radius: 999px;
        font-weight: 600;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        padding: 0.65rem 1.75rem;
        background: linear-gradient(90deg, #ff6b6b 0%, #feca57 100%);
        color: #1b1b1b;
    }

    .status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
    }

    .status-live {
        background: #1dd1a1;
        box-shadow: 0 0 0 4px rgba(29, 209, 161, 0.2);
    }

    .status-off {
        background: #576574;
        box-shadow: 0 0 0 4px rgba(87, 101, 116, 0.2);
    }

    .config-hint {
        font-size: 0.85rem;
        color: #9ca3af;
    }

    .reward-badge {
        font-size: 0.75rem;
        border-radius: 999px;
        padding: 0.2rem 0.75rem;
        background: rgba(255, 255, 255, 0.08);
        color: #f5f5f5;
    }

    .table-dark th {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }
</style>

<div class="game-manage-card">
    <div class="game-manage-header">
        <div>
            <p class="text-secondary small mb-1">Game control center</p>
            <h4 class="m-0">Mini game manager</h4>
            <p class="config-hint mt-2 mb-0">เพิ่มเกมสุ่มรางวัล, เกมเดาเลข และเกมเป่ายิ้งฉุบพร้อมเซ็ตของรางวัลได้จากหน้าจอนี้</p>
        </div>
        <button class="btn-gradient" id="openGameModal">
            <i class="fa-duotone fa-plus"></i> เพิ่มเกมใหม่
        </button>
    </div>

    <div class="table-responsive">
        <table id="adminGameTable" class="table table-dark table-striped align-middle text-center">
            <thead>
                <tr>
                    <th>#</th>
                    <th>ภาพ</th>
                    <th>ชื่อเกม</th>
                    <th>ประเภท</th>
                    <th>ค่าเข้าเล่น</th>
                    <th>สถานะ</th>
                    <th>อัปเดตล่าสุด</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($games as $row) : ?>
                    <tr>
                        <td><?= (int) $row['id']; ?></td>
                        <td>
                            <?php if (!empty($row['image'])) : ?>
                                <img src="<?= e($row['image']); ?>" alt="<?= e($row['name']); ?>" style="width:80px;height:80px;object-fit:cover;border-radius:12px;">
                            <?php else : ?>
                                <span class="text-muted">no image</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-start">
                            <strong><?= e($row['name']); ?></strong>
                            <div class="config-hint"><?= e($row['description']); ?></div>
                        </td>
                        <td><?= game_type_label($row['type']); ?></td>
                        <td>฿<?= number_format((int) $row['entry_cost']); ?></td>
                        <td>
                            <span class="status-dot <?= $row['is_active'] ? 'status-live' : 'status-off'; ?>"></span>
                            <?= $row['is_active'] ? 'เปิดใช้งาน' : 'ปิดชั่วคราว'; ?>
                        </td>
                        <td><?= e($row['updated_at']); ?></td>
                        <td>
                            <div class="d-flex flex-column gap-2">
                                <button class="btn btn-sm btn-outline-light" onclick='openRewardModal(<?= (int) $row['id']; ?>, <?= json_encode($row['name']); ?>, <?= json_encode($row['type']); ?>)'>
                                    <i class="fa-duotone fa-gift"></i> รางวัล
                                </button>
                                <button class="btn btn-sm btn-warning" onclick="editGame(<?= (int) $row['id']; ?>)">
                                    <i class="fa-duotone fa-pen-to-square"></i> แก้ไข
                                </button>
                                <button class="btn btn-sm <?= $row['is_active'] ? 'btn-outline-secondary' : 'btn-success'; ?>" onclick="toggleGameStatus(<?= (int) $row['id']; ?>, <?= $row['is_active'] ? 0 : 1; ?>)">
                                    <?= $row['is_active'] ? 'ปิดการแสดง' : 'เปิดใช้งาน'; ?>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick='deleteGame(<?= (int) $row['id']; ?>, <?= json_encode($row['name']); ?>)'>
                                    <i class="fa-duotone fa-trash"></i> ลบ
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Game form modal -->
<div class="modal fade" id="gameFormModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="gameModalTitle">เพิ่มเกมใหม่</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="game_form_mode" value="create">
                <input type="hidden" id="game_form_id">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">ชื่อเกม</label>
                        <input type="text" class="form-control" id="game_name" placeholder="ตัวอย่าง: วงล้อลุ้นโชค">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">ลิงก์ภาพหน้าปก</label>
                        <input type="text" class="form-control" id="game_image" placeholder="https://">
                    </div>
                    <div class="col-12">
                        <label class="form-label">คำอธิบาย</label>
                        <textarea class="form-control" id="game_description" rows="2" placeholder="ใส่รายละเอียดสั้น ๆ เพื่อบอกผู้เล่น"></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">ประเภทเกม</label>
                        <select class="form-select" id="game_type">
                            <option value="wheel">วงล้อสุ่ม</option>
                            <option value="number">เกมเดาเลข</option>
                            <option value="rps">เป่ายิ้งฉุบ</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">ค่าเข้าเล่น (พอยท์)</label>
                        <input type="number" class="form-control" id="game_cost" min="0" step="1" value="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">สถานะ</label>
                        <select class="form-select" id="game_status">
                            <option value="1">เปิดใช้งาน</option>
                            <option value="0">ซ่อนชั่วคราว</option>
                        </select>
                    </div>
                </div>

                <hr class="text-muted my-4">
                <p class="mb-3 fw-semibold">ตั้งค่าเพิ่มเติม</p>
                <div class="type-config" data-config-type="wheel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">ระยะเวลาหมุน (มิลลิวินาที)</label>
                            <input type="number" class="form-control" id="wheel_duration" value="4800" min="1000" step="100">
                            <p class="config-hint mt-1 mb-0">ระยะเวลายิ่งมากอนิเมชันจะยิ่งนุ่มนวล</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">สีหัวลูกศร</label>
                            <input type="color" class="form-control form-control-color" id="wheel_pointer_color" value="#ffffff">
                        </div>
                    </div>
                </div>

                <div class="type-config d-none" data-config-type="number">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">เลขต่ำสุด</label>
                            <input type="number" class="form-control" id="number_min" value="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">เลขสูงสุด</label>
                            <input type="number" class="form-control" id="number_max" value="9">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ข้อความปลอบใจ</label>
                            <input type="text" class="form-control" id="number_consolation" placeholder="เกือบแล้ว!">
                        </div>
                    </div>
                </div>

                <div class="type-config d-none" data-config-type="rps">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">ชื่อคู่แข่ง</label>
                            <input type="text" class="form-control" id="rps_host" value="Dealer AI">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ข้อความทักทาย</label>
                            <input type="text" class="form-control" id="rps_intro" placeholder="พร้อมสู้ก็เข้ามาเลย!">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary" id="game_save_btn">
                    <i class="fa-duotone fa-floppy-disk"></i> บันทึกข้อมูล
                </button>
            </div>
        </div>
    </div>
    </div>

<!-- Reward modal -->
<div class="modal fade" id="rewardModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">ตั้งค่ารางวัล</h5>
                    <p class="config-hint mb-0" id="rewardModalSubtitle"></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="reward_game_id">
                <input type="hidden" id="reward_game_type">
                <div class="table-responsive mb-4">
                    <table class="table table-striped table-hover text-center align-middle" id="rewardTable">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>ชื่อรางวัล</th>
                                <th>ประเภท</th>
                                <th>ค่าพอยท์</th>
                                <th>น้ำหนัก/เงื่อนไข</th>
                                <th>คำสั่ง</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <div class="border rounded-3 p-3">
                    <p class="fw-semibold mb-3">เพิ่ม/แก้ไขรางวัล</p>
                    <input type="hidden" id="reward_id">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">ชื่อรางวัล</label>
                            <input type="text" class="form-control" id="reward_label" placeholder="ตัวอย่าง: Mega Prize">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ประเภท</label>
                            <select class="form-select" id="reward_type">
                                <option value="text">ข้อความ/โค้ด</option>
                                <option value="points">เพิ่มพอยท์</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ค่าแต้ม (กรณีเพิ่มพอยท์)</label>
                            <input type="number" class="form-control" id="reward_amount" value="0" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">รายละเอียด/ข้อความที่แสดง</label>
                            <textarea class="form-control" id="reward_value" rows="2" placeholder="URL, โค้ด หรือข้อความแจ้งรางวัล"></textarea>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">น้ำหนัก (%)</label>
                            <input type="number" class="form-control" id="reward_weight" value="10" min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">ลำดับแสดง</label>
                            <input type="number" class="form-control" id="reward_order" value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">สีพื้นหลัง</label>
                            <input type="text" class="form-control" id="reward_color" placeholder="#ff6b6b">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ภาพประกอบ</label>
                            <input type="text" class="form-control" id="reward_image" placeholder="https://">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">เงื่อนไข/Rule</label>
                            <select class="form-select" id="reward_rule">
                                <option value="">ไม่ใช้</option>
                            </select>
                        </div>
                    </div>
                    <p class="config-hint mt-3" id="rewardRuleHint"></p>
                    <div class="d-flex gap-2 mt-3">
                        <button class="btn btn-outline-secondary" type="button" onclick="resetRewardForm()">ล้างฟอร์ม</button>
                        <button class="btn btn-primary ms-auto" type="button" id="reward_save_btn">
                            <i class="fa-duotone fa-floppy-disk"></i> บันทึกรางวัล
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let gameFormModal;
    let rewardModal;
    let rewardRulesOptions = {
        wheel: [{
            value: '',
            label: 'ไม่ใช้ (แบ่งด้วยน้ำหนักเปอร์เซ็นต์)'
        }],
        number: [{
                value: 'match',
                label: 'ทายถูก'
            },
            {
                value: 'miss',
                label: 'ทายผิด'
            }
        ],
        rps: [{
                value: 'win',
                label: 'ชนะ'
            },
            {
                value: 'draw',
                label: 'เสมอ'
            },
            {
                value: 'lose',
                label: 'แพ้'
            }
        ]
    };

    document.addEventListener('DOMContentLoaded', () => {
        ['gameFormModal', 'rewardModal'].forEach(id => {
            const modal = document.getElementById(id);
            if (modal && modal.parentNode !== document.body) {
                document.body.appendChild(modal);
            }
        });

        gameFormModal = new bootstrap.Modal(document.getElementById('gameFormModal'));
        rewardModal = new bootstrap.Modal(document.getElementById('rewardModal'));

        $('#adminGameTable').DataTable();

        $('#openGameModal').on('click', () => openGameModal());
        $('#game_type').on('change', handleTypeChange);
        $('#game_save_btn').on('click', saveGame);
        $('#reward_save_btn').on('click', saveReward);

        handleTypeChange();
    });

    function handleTypeChange() {
        const current = $('#game_type').val();
        $('[data-config-type]').addClass('d-none');
        $(`[data-config-type=\"${current}\"]`).removeClass('d-none');
    }

    function collectConfig(type) {
        const config = {};
        if (type === 'wheel') {
            config.spin_duration = parseInt($('#wheel_duration').val(), 10) || 4500;
            config.pointer_color = $('#wheel_pointer_color').val() || '#ffffff';
        } else if (type === 'number') {
            config.number_min = parseInt($('#number_min').val(), 10) || 1;
            config.number_max = parseInt($('#number_max').val(), 10) || 9;
            config.consolation = $('#number_consolation').val() || '';
        } else if (type === 'rps') {
            config.host = $('#rps_host').val() || 'Dealer AI';
            config.intro = $('#rps_intro').val() || '';
        }
        return config;
    }

    function openGameModal(mode = 'create') {
        $('#game_form_mode').val(mode);
        $('#game_form_id').val('');
        $('#game_name, #game_image, #game_description').val('');
        $('#game_cost').val(0);
        $('#game_status').val(1);
        $('#game_type').val('wheel').prop('disabled', mode === 'edit');
        $('#wheel_duration').val(4800);
        $('#wheel_pointer_color').val('#ffffff');
        $('#number_min').val(1);
        $('#number_max').val(9);
        $('#number_consolation').val('');
        $('#rps_host').val('Dealer AI');
        $('#rps_intro').val('');
        $('#gameModalTitle').text(mode === 'create' ? 'เพิ่มเกมใหม่' : 'แก้ไขเกม');
        handleTypeChange();
        gameFormModal.show();
    }

    function editGame(id) {
        $.post('system/backend/call/game_detail.php', {
            id
        }, function(res) {
            openGameModal('edit');
            $('#game_form_id').val(res.id);
            $('#game_name').val(res.name);
            $('#game_image').val(res.image);
            $('#game_description').val(res.description);
            $('#game_cost').val(res.entry_cost);
            $('#game_status').val(res.is_active);
            $('#game_type').val(res.type).prop('disabled', true);
            const cfg = res.config || {};
            $('#wheel_duration').val(cfg.spin_duration || 4800);
            $('#wheel_pointer_color').val(cfg.pointer_color || '#ffffff');
            $('#number_min').val(cfg.number_min || 1);
            $('#number_max').val(cfg.number_max || 9);
            $('#number_consolation').val(cfg.consolation || '');
            $('#rps_host').val(cfg.host || 'Dealer AI');
            $('#rps_intro').val(cfg.intro || '');
            handleTypeChange();
        }).fail(handleAjaxError);
    }

    function saveGame() {
        const mode = $('#game_form_mode').val();
        const payload = {
            mode,
            id: $('#game_form_id').val(),
            name: $('#game_name').val(),
            image: $('#game_image').val(),
            description: $('#game_description').val(),
            type: $('#game_type').val(),
            entry_cost: $('#game_cost').val(),
            status: $('#game_status').val(),
            config: JSON.stringify(collectConfig($('#game_type').val()))
        };

        $.post('system/backend/game_save.php', payload, function(res) {
            Swal.fire('สำเร็จ', res.message, 'success').then(() => location.reload());
        }).fail(handleAjaxError);
    }

    function toggleGameStatus(id, status) {
        $.post('system/backend/game_status.php', {
            id,
            status
        }, function(res) {
            Swal.fire('สำเร็จ', res.message, 'success').then(() => location.reload());
        }).fail(handleAjaxError);
    }

    function deleteGame(id, name) {
        Swal.fire({
            title: 'ยืนยันการลบ?',
            text: `เกม ${name} จะถูกลบถาวร`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ลบเลย',
            cancelButtonText: 'ยกเลิก'
        }).then(result => {
            if (result.isConfirmed) {
                $.post('system/backend/game_delete.php', {
                    id
                }, function(res) {
                    Swal.fire('ลบแล้ว', res.message, 'success').then(() => location.reload());
                }).fail(handleAjaxError);
            }
        });
    }

    function openRewardModal(id, name, type) {
        $('#reward_game_id').val(id);
        $('#reward_game_type').val(type);
        $('#rewardModalSubtitle').text(`เกม: ${name}`);
        buildRuleOptions(type);
        resetRewardForm();
        loadRewardList();
        rewardModal.show();
    }

    function buildRuleOptions(type) {
        const select = $('#reward_rule');
        select.empty();
        (rewardRulesOptions[type] || [{
            value: '',
            label: 'ไม่ใช้'
        }]).forEach(opt => {
            select.append(`<option value=\"${opt.value}\">${opt.label}</option>`);
        });
        if (type === 'wheel') {
            $('#rewardRuleHint').text('วงล้อจะอิงเปอร์เซ็นต์น้ำหนักเพื่อคำนวณโอกาส');
        } else if (type === 'number') {
            $('#rewardRuleHint').text('กรณีทายถูก ให้เลือกรูปแบบ "ทายถูก" และตั้งรางวัลชนะ ส่วน "ทายผิด" ใช้เพื่อโชว์ข้อความปลอบใจ');
        } else if (type === 'rps') {
            $('#rewardRuleHint').text('เลือกให้ตรงกับผลลัพธ์ที่ต้องการ เช่น ใส่รางวัลเฉพาะตอนชนะ');
        } else {
            $('#rewardRuleHint').text('');
        }
    }

    function loadRewardList() {
        const game_id = $('#reward_game_id').val();
        $.post('system/backend/game_reward_list.php', {
            game_id
        }, function(res) {
            const tbody = $('#rewardTable tbody');
            tbody.empty();
            res.forEach(item => {
                const badge = `<span class="reward-badge">${item.rule_value || '-'}</span>`;
                const encoded = encodeURIComponent(JSON.stringify(item));
                tbody.append(`<tr>
                    <td>${item.id}</td>
                    <td class="text-start">
                        <strong>${item.label}</strong>
                        <div class="config-hint">${item.reward_value || '-'}</div>
                    </td>
                    <td>${item.reward_type}</td>
                    <td>${item.reward_amount || 0}</td>
                    <td>${item.weight}% ${badge}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary btn-edit-reward" data-reward="${encoded}">แก้ไข</button>
                            <button class="btn btn-outline-danger" onclick="deleteReward(${item.id})">ลบ</button>
                        </div>
                    </td>
                </tr>`);
            });
        }).fail(handleAjaxError);
    }

    $('#rewardTable').on('click', '.btn-edit-reward', function() {
        const data = $(this).data('reward');
        if (!data) return;
        const item = JSON.parse(decodeURIComponent(data));
        editReward(item);
    });

    function editReward(item) {
        $('#reward_id').val(item.id);
        $('#reward_label').val(item.label);
        $('#reward_type').val(item.reward_type);
        $('#reward_amount').val(item.reward_amount);
        $('#reward_value').val(item.reward_value);
        $('#reward_weight').val(item.weight);
        $('#reward_order').val(item.sort_order);
        $('#reward_color').val(item.color);
        $('#reward_image').val(item.image);
        $('#reward_rule').val(item.rule_value || '');
    }

    function resetRewardForm() {
        $('#reward_id').val('');
        $('#reward_label').val('');
        $('#reward_type').val('text');
        $('#reward_amount').val(0);
        $('#reward_value').val('');
        $('#reward_weight').val(10);
        $('#reward_order').val(0);
        $('#reward_color').val('');
        $('#reward_image').val('');
        $('#reward_rule').val('');
    }

    function saveReward() {
        const payload = {
            id: $('#reward_id').val(),
            game_id: $('#reward_game_id').val(),
            type: $('#reward_game_type').val(),
            label: $('#reward_label').val(),
            reward_type: $('#reward_type').val(),
            reward_amount: $('#reward_amount').val(),
            reward_value: $('#reward_value').val(),
            weight: $('#reward_weight').val(),
            sort_order: $('#reward_order').val(),
            color: $('#reward_color').val(),
            image: $('#reward_image').val(),
            rule_value: $('#reward_rule').val()
        };

        $.post('system/backend/game_reward_save.php', payload, function(res) {
            Swal.fire('สำเร็จ', res.message, 'success');
            resetRewardForm();
            loadRewardList();
        }).fail(handleAjaxError);
    }

    function deleteReward(id) {
        Swal.fire({
            title: 'ลบรายการรางวัล?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก'
        }).then(result => {
            if (result.isConfirmed) {
                $.post('system/backend/game_reward_delete.php', {
                    id
                }, function(res) {
                    Swal.fire('ลบแล้ว', res.message, 'success');
                    loadRewardList();
                }).fail(handleAjaxError);
            }
        });
    }

    function handleAjaxError(xhr) {
        const res = xhr.responseJSON || {};
        Swal.fire('เกิดข้อผิดพลาด', res.message || 'ไม่สามารถทำรายการได้', 'error');
        console.error(xhr.responseText);
    }
</script>
