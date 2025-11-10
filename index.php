<?php include 'data.php'; ?>
<?php
session_start();
error_reporting(0);
require_once("system/a_func.php");
require_once("system/ui.php");

if (isset($_SESSION['id'])) {
    $q1 = dd_q("SELECT * FROM users WHERE id = ? LIMIT 1", [$_SESSION['id']]);
    if ($q1 && $q1->rowCount() === 1) {
        $user = $q1->fetch(PDO::FETCH_ASSOC);
    } else {
        session_destroy();
        $_GET['page'] = "login";
    }
}

$get_static = dd_q("SELECT * FROM static");
$static = $get_static->fetch(PDO::FETCH_ASSOC);

function admin($user)
{
    return isset($_SESSION['id'], $user['rank']) && $user['rank'] === "1";
}

$navItems = [
    ['slug' => 'home', 'href' => '/?page=home', 'label' => 'หน้าหลัก'],
    ['slug' => 'shop', 'href' => '/?page=shop', 'label' => 'ร้านค้า'],
    ['slug' => 'games', 'href' => '/?page=games', 'label' => 'มินิเกม'],
    ['slug' => 'topup', 'href' => '/?page=topup', 'label' => 'เติมเงิน'],
];

if (isset($byshop_status) && $byshop_status === 'on') {
    $navItems[] = ['slug' => 'premiumapp', 'href' => '/?page=premiumapp', 'label' => 'แอปพรีเมียม'];
}

$navItems[] = ['slug' => 'question', 'href' => '/?page=question', 'label' => 'ศูนย์ช่วยเหลือ'];
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($config['name']); ?></title>
    <meta property="og:title" content="<?= e($config['name']); ?> | Homepage">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://<?= e($config['name']); ?>.jvcz.xyz">
    <meta property="og:image" content="<?= e($config['logo']); ?>">
    <meta property="og:description" content="<?= e($config['des']); ?>">
    <link rel="shortcut icon" href="<?= e($config['logo']); ?>" type="image/png" sizes="16x16">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Kanit:wght@400;500;600&family=Prompt:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="system/css/second.css">
    <link rel="stylesheet" href="system/css/option.css">
    <style>
        <?php
        @readfile(__DIR__ . '/system/css/second.css');
        echo PHP_EOL;
        @readfile(__DIR__ . '/system/css/option.css');
        ?>
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"
        integrity="sha512-pumBsjNRGGqkPzKHndZMaAG+bir374sORyzM3uulLV14lN5LyykqNk8eEeUlUkB3U0M4FApyaHraT65ihJhDpQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="//cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <link href="https://kit-pro.fontawesome.com/releases/v6.2.0/css/pro.min.css" rel="stylesheet">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>
    <style>
        :root {
            --brand-primary: <?= $config["main_color"] ?: '#ff6b2c'; ?>;
            --brand-secondary: <?= $config["sec_color"] ?: '#fcca46'; ?>;
            --page-bg-image: url('<?= e($config['bg']); ?>');
        }
    </style>
</head>

<body>
    <div class="app-shell">
        <header class="app-header">
            <a class="brand-mark" href="/?page=home">
                <img src="<?= e($config['logo']); ?>" alt="<?= e($config['name']); ?> logo">
                <div class="brand-copy">
                    <span><?= e($config['des']); ?></span>
                    <strong><?= e($config['name']); ?></strong>
                </div>
            </a>
            <button class="nav-toggle" type="button" aria-label="Toggle navigation" data-nav-toggle>
                <i class="fa-regular fa-bars"></i>
            </button>
            <nav class="app-nav" data-nav-panel>
                <?php foreach ($navItems as $item) : ?>
                    <a href="<?= e($item['href']); ?>" class="nav-link <?= nav_active($item['slug']); ?>">
                        <?= e($item['label']); ?>
                    </a>
                <?php endforeach; ?>
            </nav>
            <div class="nav-actions">
                <?php if (!isset($_SESSION['id'])) : ?>
                    <a class="ghost-btn" href="?page=login">เข้าสู่ระบบ</a>
                    <a class="cta-btn" href="?page=register">สมัครสมาชิก</a>
                <?php else : ?>
                    <?php
                    $initial = 'U';
                    if (!empty($user['username'])) {
                        $initial = strtoupper(mb_substr($user['username'], 0, 1, 'UTF-8'));
                    }
                    ?>
                    <div class="user-menu" data-user-menu>
                        <button class="user-pill" type="button" data-user-toggle>
                            <span class="avatar"><?= e($initial); ?></span>
                            <div>
                                <small class="badge-dot">เข้าสู่ระบบแล้ว</small>
                                <strong><?= e($user['username']); ?></strong>
                            </div>
                        </button>
                        <div class="user-dropdown">
                            <div class="stacked-card mb-3">
                                <p class="badge-dot">ยอดเงินคงเหลือ</p>
                                <h4 class="m-0" id="currentPointBalance" data-current-point="<?= format_number($user['point'], 2); ?>">฿<?= format_number($user['point'], 2); ?></h4>
                                <p class="badge-dot mt-3">ยอดใช้จ่ายสะสม · ฿<?= format_number($user['total'], 2); ?></p>
                            </div>
                            <div class="dropdown-links">
                                <a href="?page=profile">ข้อมูลบัญชี</a>
                                <a href="?page=profile&subpage=buyhis">ประวัติการสั่งซื้อ</a>
                                <a href="?page=profile&subpage=myapp">แอปของฉัน</a>
                                <a href="?page=profile&subpage=topuphis">ประวัติเติมเงิน</a>
                                <a href="?page=profile&subpage=cpass">จัดการความปลอดภัย</a>
                                <?php if (admin($user)) : ?>
                                    <a href="?page=backend">จัดการระบบ</a>
                                <?php endif; ?>
                                <a href="?page=logout">ออกจากระบบ</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </header>

        <main class="app-main">
            <?php
            if (isset($_GET['page']) && $_GET['page'] == "menu") {
                require_once('page/simple.php');
            } elseif (isset($_GET['page']) && $_GET['page'] == "login" && !isset($_SESSION['id'])) {
                require_once('page/login.php');
            } elseif (isset($_GET['page']) && $_GET['page'] == "logout" && isset($_SESSION['id'])) {
                session_destroy();
                echo "<script>window.location.href = '';</script>";
            } elseif (isset($_GET['page']) && $_GET['page'] == "profile" && isset($_SESSION['id'])) {
                require_once('page/profile.php');
            } elseif (isset($_GET['page']) && $_GET['page'] == "topup") {
                if (isset($_SESSION['id'])) {
                    require_once('page/topup.php');
                } else {
                    require_once('page/login.php');
                }
            } elseif (isset($_GET['page']) && $_GET['page'] == "redeem") {
                if (isset($_SESSION['id'])) {
                    require_once('page/redeem.php');
                } else {
                    require_once('page/login.php');
                }
            } elseif (isset($_GET['page']) && $_GET['page'] == "buy") {
                if (isset($_SESSION['id'])) {
                    require_once('page/buy.php');
                } else {
                    require_once('page/login.php');
                }
            } elseif (isset($_GET['page']) && $_GET['page'] == "id") {
                if (isset($_SESSION['id'])) {
                    require_once('page/id.php');
                } else {
                    require_once('page/login.php');
                }
            } elseif (isset($_GET['page']) && $_GET['page'] == "gp") {
                if (isset($_SESSION['id'])) {
                    require_once('page/gp.php');
                } else {
                    require_once('page/login.php');
                }
            } elseif (isset($_GET['page']) && $_GET['page'] == "product" && isset($_GET['id'])) {
                if (isset($_SESSION['id'])) {
                    require_once('page/product.php');
                } else {
                    require_once('page/login.php');
                }
            } elseif (isset($_GET['page']) && $_GET['page'] == "slidebloxfruit") {
                if (isset($_SESSION['id'])) {
                    require_once('page/csgo_1.php');
                } else {
                    require_once('page/login.php');
                }
            } elseif (isset($_GET['page']) && $_GET['page'] == "id_p" && isset($_GET['id'])) {
                if (isset($_SESSION['id'])) {
                    require_once('page/id_p.php');
                } else {
                    require_once('page/login.php');
                }
            } elseif (isset($_GET['page']) && $_GET['page'] == "shop") {
                if (isset($_SESSION['id'])) {
                    require_once('page/shop.php');
        } else {
            require_once('page/login.php');
        }
    } elseif (isset($_GET['page']) && $_GET['page'] == "games") {
        if (isset($_SESSION['id'])) {
            require_once('page/games.php');
        } else {
            require_once('page/login.php');
        }
    } elseif (isset($_GET['page']) && $_GET['page'] == "premiumapp") {
        if (isset($_SESSION['id'])) {
            require_once('page/premiumapp.php');
                } else {
                    require_once('page/login.php');
                }
            } elseif (isset($_GET['page']) && $_GET['page'] == "buyapp") {
                if (isset($_SESSION['id'])) {
                    require_once('page/buyapp.php');
                } else {
                    require_once('page/login.php');
                }
            } elseif (isset($_GET['page']) && $_GET['page'] == "question") {
                if (isset($_SESSION['id'])) {
                    require_once('page/question.php');
                } else {
                    require_once('page/login.php');
                }
            } elseif (isset($_GET['page']) && $_GET['page'] == "my_premiumapp") {
                if (isset($_SESSION['id'])) {
                    require_once('page/myapp.php');
                } else {
                    require_once('page/login.php');
                }
            } elseif (isset($_GET['page']) && $_GET['page'] == "register" && !isset($_SESSION['id'])) {
                require_once('page/register.php');
            } elseif (admin($user) && isset($_GET['page']) && in_array($_GET['page'], [
                "backend",
                "user_edit",
                "product_manage",
                "stock_manage",
                "code_manage",
                "slip_manage",
                "category_manage",
                "backend_buy_history",
                "backend_topup_history",
                "carousel_manage",
                "recom_manage",
                "crecom_manage",
                "game_manage",
                "website",
                "apibyshop",
                "apibyshop_his"
            ])) {
                require_once('page/backend/menu_manage.php');
            } else {
                require_once('page/simple.php');
            }
            ?>
        </main>

        <footer class="app-footer">
            <span>© <?= date('Y'); ?> <?= e($config['name']); ?> · All rights reserved.</span>
            <div class="footer-links">
                <?php if (!empty($config['contact'])) : ?>
                    <a href="<?= e($config['contact']); ?>" target="_blank" rel="noopener">Support</a>
                <?php endif; ?>
                <a href="mailto:<?= e($config['email'] ?? 'support@example.com'); ?>">Email</a>
            </div>
        </footer>
    </div>

    <div class="modal fade" id="buy_count" tabindex="-1" aria-labelledby="modal_title" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal_title">ยืนยันจำนวนที่ต้องการ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="field-label" for="b_count">จำนวนที่ต้องการเปิด</label>
                        <input type="number" id="b_count" class="form-control text-center" value="1" min="1">
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge-dot">พร้อมจำหน่าย: <code id="s">0</code></span>
                        <small class="pill" id="b">ระบบจะส่งข้อมูลทันทีหลังชำระเงิน</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="shop-btn" class="shopbtn w-100" onclick="buybox()" data-id="" data-name="">
                        <i class="fa-regular fa-cart-shopping"></i>&nbsp;ยืนยันการสั่งซื้อ
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="product_detail" tabindex="-1" aria-labelledby="productDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productDetailLabel">รายละเอียดสินค้า</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 align-items-center">
                        <div class="col-lg-5">
                            <img id="p_img" src="" alt="" class="img-fluid rounded">
                        </div>
                        <div class="col-lg-7">
                            <h4 id="p_name" class="mb-3"></h4>
                            <p id="p_des" class="text-muted"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function tobuy(id, name, stock, note) {
            $("#modal_title").text(name);
            $("#shop-btn").attr("data-id", id);
            $("#shop-btn").attr("data-name", name);
            $("#s").text(stock);
            $("#b").text(note);
            const myModal = new bootstrap.Modal('#buy_count', { keyboard: false });
            myModal.show();
        }

        function detail(id) {
            var formData = new FormData();
            formData.append('id', id);

            $.ajax({
                type: 'POST',
                url: 'system/call/product_detail.php',
                data: formData,
                contentType: false,
                processData: false,
            }).done(function(res) {
                $("#p_img").attr("src", res.img);
                $("#p_name").text(res.name);
                $("#p_des").text(res.des);
                const myModal = new bootstrap.Modal('#product_detail', { keyboard: false });
                myModal.show();
            }).fail(function(jqXHR) {
                const res = jqXHR.responseJSON || {};
                Swal.fire({
                    icon: 'error',
                    title: 'ไม่สามารถดึงข้อมูลสินค้า',
                    text: res.message || 'โปรดลองใหม่อีกครั้ง'
                });
            });
        }

        async function shake_alert(status, result) {
            if (status) {
                if (result.salt === "prize") {
                    Swal.fire({
                        icon: 'success',
                        title: 'ทำรายการสำเร็จ',
                        text: result.message
                    }).then(function() {
                        window.location = "?page=profile&subpage=buyhis";
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'ไม่สามารถทำรายการได้',
                        text: result.message
                    });
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: result.message
                });
            }
        }

        function buybox() {
            var name = $("#shop-btn").attr("data-name");
            var formData = new FormData();
            formData.append('id', $("#shop-btn").attr("data-id"));
            formData.append('count', $("#b_count").val());
            Swal.fire({
                title: 'ยืนยันการซื้อหรือไม่?',
                text: 'ต้องการสั่งซื้อ ' + name + ' ตอนนี้หรือไม่?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0ea5e9',
                cancelButtonColor: '#d33',
                confirmButtonText: 'ยืนยันการซื้อ'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: 'system/buybox.php',
                        data: formData,
                        contentType: false,
                        processData: false,
                        beforeSend: function() {
                            $('#shop-btn').attr('disabled', 'disabled');
                            $('#shop-btn').html('<span class="spinner-border spinner-border-sm me-2" role="status"></span> กำลังดำเนินการ...');
                        },
                    }).done(function(res) {
                        shake_alert(true, res);
                        $('#shop-btn').html('<i class="fa-regular fa-cart-shopping"></i> ยืนยันการสั่งซื้อ');
                        $('#shop-btn').removeAttr('disabled');
                    }).fail(function(jqXHR) {
                        const res = jqXHR.responseJSON || {};
                        shake_alert(false, res);
                        $('#shop-btn').html('<i class="fa-regular fa-cart-shopping"></i> ยืนยันการสั่งซื้อ');
                        $('#shop-btn').removeAttr('disabled');
                    });
                }
            });
        }

        document.addEventListener('click', (event) => {
            const navToggle = event.target.closest('[data-nav-toggle]');
            if (navToggle) {
                const panel = document.querySelector('[data-nav-panel]');
                if (panel) {
                    panel.classList.toggle('is-open');
                }
            }

            const userToggle = event.target.closest('[data-user-toggle]');
            document.querySelectorAll('[data-user-menu]').forEach((menu) => {
                if (menu.contains(event.target)) {
                    if (userToggle) {
                        menu.classList.toggle('is-open');
                    }
                } else {
                    menu.classList.remove('is-open');
                }
            });
        });

        AOS.init();
    </script>
</body>

</html>
