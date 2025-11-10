<?php

/**
 * Schema + service helpers for the expanded commerce feature-set.
 * The schema is created lazily as soon as the file is loaded, so the rest of
 * the application only needs to call `feature_service()` when it needs any of
 * the new capabilities.
 */
class PlatformFeatureSchema
{
    /**
     * Ensure all necessary tables exist. Subsequent calls are cheap because of
     * the IF NOT EXISTS guards.
     */
    public static function ensure(PDO $conn)
    {
        $statements = [
            "CREATE TABLE IF NOT EXISTS user_wallets (
                id INT(11) NOT NULL AUTO_INCREMENT,
                user_id INT(11) NOT NULL,
                balance DECIMAL(12,2) NOT NULL DEFAULT 0,
                hold_balance DECIMAL(12,2) NOT NULL DEFAULT 0,
                loyalty_points INT(11) NOT NULL DEFAULT 0,
                vip_level VARCHAR(32) NOT NULL DEFAULT 'standard',
                vip_since DATETIME DEFAULT NULL,
                last_daily_login DATE DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uniq_user_wallet (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS wallet_transactions (
                id BIGINT(20) NOT NULL AUTO_INCREMENT,
                user_id INT(11) NOT NULL,
                direction ENUM('credit','debit') NOT NULL,
                txn_type VARCHAR(64) NOT NULL,
                reference VARCHAR(128) DEFAULT NULL,
                currency VARCHAR(16) NOT NULL DEFAULT 'THB',
                amount DECIMAL(12,2) NOT NULL,
                status VARCHAR(32) NOT NULL DEFAULT 'completed',
                metadata LONGTEXT DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_wallet_txn_user (user_id),
                KEY idx_wallet_txn_ref (reference)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS wallet_transfers (
                id BIGINT(20) NOT NULL AUTO_INCREMENT,
                from_user_id INT(11) NOT NULL,
                to_user_id INT(11) NOT NULL,
                amount DECIMAL(12,2) NOT NULL,
                fee_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
                note VARCHAR(255) DEFAULT NULL,
                status VARCHAR(32) NOT NULL DEFAULT 'completed',
                metadata LONGTEXT DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_transfer_from (from_user_id),
                KEY idx_transfer_to (to_user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS discount_coupons (
                id INT(11) NOT NULL AUTO_INCREMENT,
                code VARCHAR(64) NOT NULL,
                name VARCHAR(255) NOT NULL,
                coupon_type ENUM('percent','fixed','wallet','shipping') NOT NULL DEFAULT 'percent',
                value DECIMAL(12,2) NOT NULL DEFAULT 0,
                max_discount DECIMAL(12,2) DEFAULT NULL,
                min_spend DECIMAL(12,2) DEFAULT 0,
                start_at DATETIME DEFAULT NULL,
                end_at DATETIME DEFAULT NULL,
                usage_limit INT(11) DEFAULT NULL,
                per_user_limit INT(11) DEFAULT NULL,
                used_count INT(11) NOT NULL DEFAULT 0,
                status ENUM('draft','scheduled','active','paused','expired') NOT NULL DEFAULT 'draft',
                metadata LONGTEXT DEFAULT NULL,
                created_by INT(11) DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uniq_coupon_code (code),
                KEY idx_coupon_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS coupon_redemptions (
                id BIGINT(20) NOT NULL AUTO_INCREMENT,
                coupon_id INT(11) NOT NULL,
                user_id INT(11) NOT NULL,
                order_id VARCHAR(64) DEFAULT NULL,
                order_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
                discount_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_coupon_redemption_coupon (coupon_id),
                KEY idx_coupon_redemption_user (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS loyalty_tiers (
                id INT(11) NOT NULL AUTO_INCREMENT,
                tier_key VARCHAR(32) NOT NULL,
                name VARCHAR(100) NOT NULL,
                min_points INT(11) NOT NULL DEFAULT 0,
                multiplier DECIMAL(5,2) NOT NULL DEFAULT 1.00,
                benefits TEXT DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uniq_tier_key (tier_key)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS loyalty_activities (
                id BIGINT(20) NOT NULL AUTO_INCREMENT,
                user_id INT(11) NOT NULL,
                activity_type VARCHAR(64) NOT NULL,
                points INT(11) NOT NULL,
                reference VARCHAR(64) DEFAULT NULL,
                metadata LONGTEXT DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_loyalty_user (user_id),
                KEY idx_loyalty_activity (activity_type)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS payment_channels (
                id INT(11) NOT NULL AUTO_INCREMENT,
                code VARCHAR(32) NOT NULL,
                name VARCHAR(100) NOT NULL,
                provider VARCHAR(64) NOT NULL,
                fee_percent DECIMAL(6,3) NOT NULL DEFAULT 0,
                fee_flat DECIMAL(12,2) NOT NULL DEFAULT 0,
                currency VARCHAR(16) NOT NULL DEFAULT 'THB',
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                config LONGTEXT DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uniq_payment_code (code)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS payment_transactions (
                id BIGINT(20) NOT NULL AUTO_INCREMENT,
                user_id INT(11) DEFAULT NULL,
                channel_code VARCHAR(32) NOT NULL,
                amount DECIMAL(12,2) NOT NULL,
                currency VARCHAR(16) NOT NULL DEFAULT 'THB',
                status VARCHAR(32) NOT NULL DEFAULT 'pending',
                reference VARCHAR(128) DEFAULT NULL,
                external_id VARCHAR(128) DEFAULT NULL,
                metadata LONGTEXT DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_payment_user (user_id),
                KEY idx_payment_channel (channel_code),
                KEY idx_payment_reference (reference)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS affiliate_partners (
                id INT(11) NOT NULL AUTO_INCREMENT,
                user_id INT(11) NOT NULL,
                code VARCHAR(32) NOT NULL,
                rate_percent DECIMAL(5,2) NOT NULL DEFAULT 5.00,
                status VARCHAR(32) NOT NULL DEFAULT 'active',
                total_commission DECIMAL(12,2) NOT NULL DEFAULT 0,
                metadata LONGTEXT DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uniq_affiliate_code (code)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS affiliate_referrals (
                id BIGINT(20) NOT NULL AUTO_INCREMENT,
                partner_id INT(11) NOT NULL,
                referred_user_id INT(11) DEFAULT NULL,
                order_id VARCHAR(64) DEFAULT NULL,
                order_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
                commission_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
                status VARCHAR(32) NOT NULL DEFAULT 'pending',
                metadata LONGTEXT DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_affiliate_partner (partner_id),
                KEY idx_affiliate_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS affiliate_payouts (
                id BIGINT(20) NOT NULL AUTO_INCREMENT,
                partner_id INT(11) NOT NULL,
                amount DECIMAL(12,2) NOT NULL,
                status VARCHAR(32) NOT NULL DEFAULT 'pending',
                method VARCHAR(32) DEFAULT NULL,
                metadata LONGTEXT DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_affiliate_payout_partner (partner_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS product_reviews (
                id BIGINT(20) NOT NULL AUTO_INCREMENT,
                product_id INT(11) NOT NULL,
                user_id INT(11) NOT NULL,
                rating TINYINT(1) NOT NULL,
                title VARCHAR(255) DEFAULT NULL,
                content TEXT DEFAULT NULL,
                media LONGTEXT DEFAULT NULL,
                status VARCHAR(32) NOT NULL DEFAULT 'pending',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_review_product (product_id),
                KEY idx_review_user (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS flash_sales (
                id INT(11) NOT NULL AUTO_INCREMENT,
                name VARCHAR(255) NOT NULL,
                description TEXT DEFAULT NULL,
                start_at DATETIME NOT NULL,
                end_at DATETIME NOT NULL,
                discount_type ENUM('percent','fixed') NOT NULL DEFAULT 'percent',
                discount_value DECIMAL(12,2) NOT NULL,
                max_purchase INT(11) DEFAULT NULL,
                status VARCHAR(32) NOT NULL DEFAULT 'draft',
                metadata LONGTEXT DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS flash_sale_items (
                id INT(11) NOT NULL AUTO_INCREMENT,
                flash_sale_id INT(11) NOT NULL,
                product_id INT(11) NOT NULL,
                promo_price DECIMAL(12,2) DEFAULT NULL,
                stock INT(11) NOT NULL DEFAULT 0,
                sold INT(11) NOT NULL DEFAULT 0,
                limit_per_user INT(11) DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_flash_sale (flash_sale_id),
                KEY idx_flash_sale_product (product_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS bundle_sets (
                id INT(11) NOT NULL AUTO_INCREMENT,
                name VARCHAR(255) NOT NULL,
                description TEXT DEFAULT NULL,
                price DECIMAL(12,2) NOT NULL,
                status VARCHAR(32) NOT NULL DEFAULT 'draft',
                metadata LONGTEXT DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS bundle_items (
                id INT(11) NOT NULL AUTO_INCREMENT,
                bundle_id INT(11) NOT NULL,
                product_id INT(11) NOT NULL,
                quantity INT(11) NOT NULL DEFAULT 1,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_bundle_item_bundle (bundle_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS inventory_movements (
                id BIGINT(20) NOT NULL AUTO_INCREMENT,
                product_id INT(11) NOT NULL,
                movement_type VARCHAR(32) NOT NULL,
                quantity INT(11) NOT NULL,
                reference VARCHAR(64) DEFAULT NULL,
                metadata LONGTEXT DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_inventory_product (product_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS inventory_snapshots (
                id BIGINT(20) NOT NULL AUTO_INCREMENT,
                product_id INT(11) NOT NULL,
                quantity_available INT(11) NOT NULL,
                quantity_reserved INT(11) NOT NULL DEFAULT 0,
                safety_stock INT(11) NOT NULL DEFAULT 0,
                captured_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uniq_inventory_product (product_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS sales_reports (
                id BIGINT(20) NOT NULL AUTO_INCREMENT,
                report_date DATE NOT NULL,
                report_type ENUM('daily','monthly','yearly') NOT NULL DEFAULT 'daily',
                metrics LONGTEXT NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uniq_sales_report (report_date, report_type)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS analytics_snapshots (
                id BIGINT(20) NOT NULL AUTO_INCREMENT,
                snapshot_key VARCHAR(64) NOT NULL,
                payload LONGTEXT NOT NULL,
                captured_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uniq_snapshot_key (snapshot_key)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS realtime_metrics (
                id BIGINT(20) NOT NULL AUTO_INCREMENT,
                metric_key VARCHAR(64) NOT NULL,
                metric_value DECIMAL(18,4) DEFAULT NULL,
                payload LONGTEXT DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_metric_key (metric_key)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS customer_events (
                id BIGINT(20) NOT NULL AUTO_INCREMENT,
                user_id INT(11) DEFAULT NULL,
                event_type VARCHAR(64) NOT NULL,
                payload LONGTEXT DEFAULT NULL,
                channel VARCHAR(32) DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_customer_event_type (event_type),
                KEY idx_customer_event_user (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS live_chat_sessions (
                id BIGINT(20) NOT NULL AUTO_INCREMENT,
                user_id INT(11) DEFAULT NULL,
                agent_id INT(11) DEFAULT NULL,
                channel VARCHAR(32) NOT NULL DEFAULT 'web',
                status VARCHAR(32) NOT NULL DEFAULT 'open',
                metadata LONGTEXT DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                closed_at DATETIME DEFAULT NULL,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS live_chat_messages (
                id BIGINT(20) NOT NULL AUTO_INCREMENT,
                session_id BIGINT(20) NOT NULL,
                sender_type VARCHAR(32) NOT NULL,
                sender_id INT(11) DEFAULT NULL,
                message TEXT NOT NULL,
                metadata LONGTEXT DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_live_chat_session (session_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS support_tickets (
                id BIGINT(20) NOT NULL AUTO_INCREMENT,
                ticket_no VARCHAR(32) NOT NULL,
                user_id INT(11) DEFAULT NULL,
                subject VARCHAR(255) NOT NULL,
                category VARCHAR(64) DEFAULT NULL,
                priority VARCHAR(32) NOT NULL DEFAULT 'normal',
                status VARCHAR(32) NOT NULL DEFAULT 'open',
                channel VARCHAR(32) NOT NULL DEFAULT 'web',
                metadata LONGTEXT DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uniq_ticket_no (ticket_no)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS support_messages (
                id BIGINT(20) NOT NULL AUTO_INCREMENT,
                ticket_id BIGINT(20) NOT NULL,
                sender_type VARCHAR(32) NOT NULL,
                sender_id INT(11) DEFAULT NULL,
                message TEXT NOT NULL,
                attachment TEXT DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_support_ticket (ticket_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS faqs (
                id INT(11) NOT NULL AUTO_INCREMENT,
                category VARCHAR(64) NOT NULL DEFAULT 'general',
                question VARCHAR(255) NOT NULL,
                answer TEXT NOT NULL,
                is_interactive TINYINT(1) NOT NULL DEFAULT 0,
                sort_order INT(11) NOT NULL DEFAULT 0,
                metadata LONGTEXT DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS notifications (
                id BIGINT(20) NOT NULL AUTO_INCREMENT,
                user_id INT(11) DEFAULT NULL,
                channel VARCHAR(32) NOT NULL,
                subject VARCHAR(255) DEFAULT NULL,
                message TEXT NOT NULL,
                status VARCHAR(32) NOT NULL DEFAULT 'queued',
                scheduled_at DATETIME DEFAULT NULL,
                sent_at DATETIME DEFAULT NULL,
                metadata LONGTEXT DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_notification_user (user_id),
                KEY idx_notification_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS daily_login_rewards (
                id INT(11) NOT NULL AUTO_INCREMENT,
                day_index INT(11) NOT NULL,
                reward_type VARCHAR(32) NOT NULL,
                reward_value VARCHAR(64) NOT NULL,
                vip_multiplier DECIMAL(5,2) NOT NULL DEFAULT 1.00,
                metadata LONGTEXT DEFAULT NULL,
                PRIMARY KEY (id),
                UNIQUE KEY uniq_daily_reward_day (day_index)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS daily_login_logs (
                id BIGINT(20) NOT NULL AUTO_INCREMENT,
                user_id INT(11) NOT NULL,
                reward_id INT(11) NOT NULL,
                streak_count INT(11) NOT NULL DEFAULT 1,
                rewarded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_daily_login_user (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        ];

        foreach ($statements as $sql) {
            try {
                $conn->exec($sql);
            } catch (PDOException $e) {
                error_log('[PlatformFeatureSchema] ' . $e->getMessage());
            }
        }

        self::seedDefaults($conn);
    }

    private static function seedDefaults(PDO $conn)
    {
        self::seedLoyaltyTiers($conn);
        self::seedDailyLoginRewards($conn);
        self::seedPaymentChannels($conn);
    }

    private static function seedLoyaltyTiers(PDO $conn)
    {
        try {
            $count = $conn->query("SELECT COUNT(*) FROM loyalty_tiers")->fetchColumn();
            if ($count > 0) {
                return;
            }
        } catch (Throwable $e) {
            return;
        }

        $tiers = [
            ['standard', 'Standard', 0, 1.00, 'Base tier'],
            ['silver', 'Silver', 5000, 1.20, '2% bonus earn rate'],
            ['gold', 'Gold', 15000, 1.50, '5% bonus earn rate'],
            ['platinum', 'Platinum', 30000, 1.80, '8% bonus earn rate'],
        ];

        $stmt = $conn->prepare("INSERT INTO loyalty_tiers (tier_key, name, min_points, multiplier, benefits) VALUES (:tier_key, :name, :min_points, :multiplier, :benefits)");
        foreach ($tiers as $tier) {
            $stmt->execute([
                'tier_key' => $tier[0],
                'name' => $tier[1],
                'min_points' => $tier[2],
                'multiplier' => $tier[3],
                'benefits' => $tier[4],
            ]);
        }
    }

    private static function seedDailyLoginRewards(PDO $conn)
    {
        try {
            $count = $conn->query("SELECT COUNT(*) FROM daily_login_rewards")->fetchColumn();
            if ($count > 0) {
                return;
            }
        } catch (Throwable $e) {
            return;
        }

        $rewards = [
            ['day' => 1, 'type' => 'wallet_credit', 'value' => '5'],
            ['day' => 2, 'type' => 'wallet_credit', 'value' => '10'],
            ['day' => 3, 'type' => 'loyalty_points', 'value' => '150'],
            ['day' => 4, 'type' => 'wallet_credit', 'value' => '15'],
            ['day' => 5, 'type' => 'coupon', 'value' => 'FLASH5'],
            ['day' => 6, 'type' => 'wallet_credit', 'value' => '25'],
            ['day' => 7, 'type' => 'wallet_credit', 'value' => '50'],
        ];

        $stmt = $conn->prepare("INSERT INTO daily_login_rewards (day_index, reward_type, reward_value, vip_multiplier) VALUES (:day_index, :reward_type, :reward_value, :vip_multiplier)");
        foreach ($rewards as $reward) {
            $stmt->execute([
                'day_index' => $reward['day'],
                'reward_type' => $reward['type'],
                'reward_value' => $reward['value'],
                'vip_multiplier' => $reward['day'] >= 6 ? 1.5 : 1.0,
            ]);
        }
    }

    private static function seedPaymentChannels(PDO $conn)
    {
        try {
            $count = $conn->query("SELECT COUNT(*) FROM payment_channels")->fetchColumn();
            if ($count > 0) {
                return;
            }
        } catch (Throwable $e) {
            return;
        }

        $channels = [
            ['truemoney', 'TrueMoney Wallet', 'truemoney', 1.50, 0, ['auto_settle' => true]],
            ['promptpay', 'PromptPay QR', 'promptpay', 0.00, 0, ['qr_valid_minutes' => 15]],
            ['bank_transfer', 'Manual Bank Transfer', 'bank', 0.00, 0, ['requires_slip' => true]],
            ['credit_card', 'Credit Card', 'stripe', 2.90, 3.00, ['capture' => 'auto']],
        ];

        $stmt = $conn->prepare("INSERT INTO payment_channels (code, name, provider, fee_percent, fee_flat, config) VALUES (:code, :name, :provider, :fee_percent, :fee_flat, :config)");
        foreach ($channels as $channel) {
            $stmt->execute([
                'code' => $channel[0],
                'name' => $channel[1],
                'provider' => $channel[2],
                'fee_percent' => $channel[3],
                'fee_flat' => $channel[4],
                'config' => json_encode($channel[5], JSON_UNESCAPED_UNICODE),
            ]);
        }
    }
}

class PlatformFeatureService
{
    private static $instance;
    /** @var PDO */
    private $conn;

    private function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public static function boot(PDO $conn)
    {
        if (!self::$instance) {
            PlatformFeatureSchema::ensure($conn);
            self::$instance = new self($conn);
        }

        return self::$instance;
    }

    public static function instance()
    {
        if (!self::$instance) {
            throw new RuntimeException('PlatformFeatureService not booted');
        }

        return self::$instance;
    }

    public function ensureUserWallet($userId)
    {
        $stmt = $this->conn->prepare("INSERT IGNORE INTO user_wallets (user_id) VALUES (:user_id)");
        $stmt->execute(['user_id' => $userId]);
    }

    public function getWallet($userId)
    {
        $this->ensureUserWallet($userId);
        $stmt = $this->conn->prepare("SELECT * FROM user_wallets WHERE user_id = :user_id LIMIT 1");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function creditWallet($userId, $amount, $type = 'system', $reference = null, array $meta = [], $currency = 'THB')
    {
        return $this->mutateWallet('credit', $userId, $amount, $type, $reference, $meta, $currency);
    }

    public function debitWallet($userId, $amount, $type = 'purchase', $reference = null, array $meta = [], $currency = 'THB')
    {
        return $this->mutateWallet('debit', $userId, $amount, $type, $reference, $meta, $currency);
    }

    public function transferWallet($fromUserId, $toUserId, $amount, $note = null, array $meta = [])
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Amount must be positive');
        }

        $this->conn->beginTransaction();

        try {
            $this->lockWalletRow($fromUserId);
            $this->lockWalletRow($toUserId);

            $sender = $this->getWallet($fromUserId);
            if ($sender['balance'] < $amount) {
                throw new RuntimeException('Insufficient balance');
            }

            $this->conn->prepare("UPDATE user_wallets SET balance = balance - :amount WHERE user_id = :user_id")
                ->execute(['amount' => $amount, 'user_id' => $fromUserId]);

            $this->conn->prepare("UPDATE user_wallets SET balance = balance + :amount WHERE user_id = :user_id")
                ->execute(['amount' => $amount, 'user_id' => $toUserId]);

            $transferStmt = $this->conn->prepare("INSERT INTO wallet_transfers (from_user_id, to_user_id, amount, note, metadata) VALUES (:from_user_id, :to_user_id, :amount, :note, :metadata)");
            $transferStmt->execute([
                'from_user_id' => $fromUserId,
                'to_user_id' => $toUserId,
                'amount' => $amount,
                'note' => $note,
                'metadata' => $meta ? json_encode($meta, JSON_UNESCAPED_UNICODE) : null,
            ]);

            $transferId = $this->conn->lastInsertId();
            $this->insertWalletTransaction($fromUserId, 'debit', 'transfer_out', $amount, 'THB', $transferId, $meta);
            $this->insertWalletTransaction($toUserId, 'credit', 'transfer_in', $amount, 'THB', $transferId, $meta);

            $this->conn->commit();
            return $transferId;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    private function mutateWallet($direction, $userId, $amount, $type, $reference, array $meta, $currency)
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Amount must be positive');
        }

        $this->conn->beginTransaction();

        try {
            $wallet = $this->lockWalletRow($userId);
            if ($direction === 'debit' && $wallet['balance'] < $amount) {
                throw new RuntimeException('Insufficient balance');
            }

            $delta = $direction === 'credit' ? $amount : -1 * $amount;
            $update = $this->conn->prepare("UPDATE user_wallets SET balance = balance + :delta WHERE user_id = :user_id");
            $update->execute(['delta' => $delta, 'user_id' => $userId]);

            $this->insertWalletTransaction($userId, $direction, $type, $amount, $currency, $reference, $meta);

            $this->conn->commit();
            return $this->getWallet($userId);
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    private function insertWalletTransaction($userId, $direction, $type, $amount, $currency, $reference, array $meta)
    {
        $stmt = $this->conn->prepare("INSERT INTO wallet_transactions (user_id, direction, txn_type, amount, currency, reference, metadata) VALUES (:user_id, :direction, :type, :amount, :currency, :reference, :metadata)");
        $stmt->execute([
            'user_id' => $userId,
            'direction' => $direction,
            'type' => $type,
            'amount' => $amount,
            'currency' => $currency,
            'reference' => $reference,
            'metadata' => $meta ? json_encode($meta, JSON_UNESCAPED_UNICODE) : null,
        ]);
    }

    private function lockWalletRow($userId)
    {
        $this->ensureUserWallet($userId);
        $stmt = $this->conn->prepare("SELECT * FROM user_wallets WHERE user_id = :user_id FOR UPDATE");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Coupons + promo codes
    public function applyCoupon($code, $userId, $orderTotal)
    {
        $coupon = $this->fetchCouponByCode($code);
        if (!$coupon) {
            return ['is_valid' => false, 'reason' => 'not_found'];
        }

        $now = new DateTimeImmutable('now');
        if ($coupon['status'] !== 'active') {
            return ['is_valid' => false, 'reason' => 'inactive'];
        }
        if ($coupon['start_at'] && $now < new DateTimeImmutable($coupon['start_at'])) {
            return ['is_valid' => false, 'reason' => 'not_started'];
        }
        if ($coupon['end_at'] && $now > new DateTimeImmutable($coupon['end_at'])) {
            return ['is_valid' => false, 'reason' => 'expired'];
        }
        if ($coupon['usage_limit'] && $coupon['used_count'] >= $coupon['usage_limit']) {
            return ['is_valid' => false, 'reason' => 'limit_reached'];
        }
        if ($coupon['min_spend'] && $orderTotal < $coupon['min_spend']) {
            return ['is_valid' => false, 'reason' => 'min_spend'];
        }

        if ($coupon['per_user_limit']) {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM coupon_redemptions WHERE coupon_id = :coupon_id AND user_id = :user_id");
            $stmt->execute(['coupon_id' => $coupon['id'], 'user_id' => $userId]);
            if ($stmt->fetchColumn() >= $coupon['per_user_limit']) {
                return ['is_valid' => false, 'reason' => 'user_limit'];
            }
        }

        $discount = $coupon['coupon_type'] === 'percent'
            ? ($orderTotal * $coupon['value']) / 100
            : $coupon['value'];

        if ($coupon['max_discount'] && $discount > $coupon['max_discount']) {
            $discount = $coupon['max_discount'];
        }
        if ($discount > $orderTotal) {
            $discount = $orderTotal;
        }

        return [
            'is_valid' => true,
            'discount' => (float) $discount,
            'coupon' => $coupon,
        ];
    }

    public function redeemCoupon($code, $userId, $orderId, $orderTotal)
    {
        $result = $this->applyCoupon($code, $userId, $orderTotal);
        if (!$result['is_valid']) {
            throw new RuntimeException('Coupon invalid: ' . $result['reason']);
        }

        $coupon = $result['coupon'];
        $discount = $result['discount'];

        $this->conn->beginTransaction();

        try {
            $stmt = $this->conn->prepare("INSERT INTO coupon_redemptions (coupon_id, user_id, order_id, order_amount, discount_amount) VALUES (:coupon_id, :user_id, :order_id, :order_amount, :discount_amount)");
            $stmt->execute([
                'coupon_id' => $coupon['id'],
                'user_id' => $userId,
                'order_id' => $orderId,
                'order_amount' => $orderTotal,
                'discount_amount' => $discount,
            ]);

            $this->conn->prepare("UPDATE discount_coupons SET used_count = used_count + 1 WHERE id = :id")
                ->execute(['id' => $coupon['id']]);

            $this->conn->commit();
            return $discount;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    private function fetchCouponByCode($code)
    {
        $stmt = $this->conn->prepare("SELECT * FROM discount_coupons WHERE code = :code LIMIT 1");
        $stmt->execute(['code' => $code]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Loyalty + VIP
    public function addLoyaltyPoints($userId, $points, $activityType, $reference = null, array $meta = [])
    {
        if (!$points) {
            return false;
        }

        $this->ensureUserWallet($userId);
        $this->conn->prepare("UPDATE user_wallets SET loyalty_points = loyalty_points + :points WHERE user_id = :user_id")
            ->execute(['points' => $points, 'user_id' => $userId]);

        $stmt = $this->conn->prepare("INSERT INTO loyalty_activities (user_id, activity_type, points, reference, metadata) VALUES (:user_id, :activity_type, :points, :reference, :metadata)");
        $stmt->execute([
            'user_id' => $userId,
            'activity_type' => $activityType,
            'points' => $points,
            'reference' => $reference,
            'metadata' => $this->encodeMeta($meta),
        ]);

        return $this->syncVipLevel($userId);
    }

    public function syncVipLevel($userId)
    {
        $wallet = $this->getWallet($userId);
        if (!$wallet) {
            return null;
        }

        $tiers = $this->conn->query("SELECT tier_key, min_points FROM loyalty_tiers ORDER BY min_points DESC")->fetchAll(PDO::FETCH_ASSOC);
        $tier = 'standard';
        foreach ($tiers as $row) {
            if ($wallet['loyalty_points'] >= $row['min_points']) {
                $tier = $row['tier_key'];
                break;
            }
        }

        $this->conn->prepare("UPDATE user_wallets SET vip_level = :tier, vip_since = IF(vip_level = :tier, vip_since, NOW()) WHERE user_id = :user_id")
            ->execute(['tier' => $tier, 'user_id' => $userId]);

        return $tier;
    }

    // Multi-channel payments
    public function createPaymentTransaction($userId, $channelCode, $amount, array $meta = [], $currency = 'THB', $reference = null)
    {
        $stmt = $this->conn->prepare("INSERT INTO payment_transactions (user_id, channel_code, amount, currency, metadata, reference) VALUES (:user_id, :channel_code, :amount, :currency, :metadata, :reference)");
        $stmt->execute([
            'user_id' => $userId,
            'channel_code' => $channelCode,
            'amount' => $amount,
            'currency' => $currency,
            'metadata' => $this->encodeMeta($meta),
            'reference' => $reference,
        ]);

        return $this->conn->lastInsertId();
    }

    public function updatePaymentStatus($transactionId, $status, array $meta = [])
    {
        $stmt = $this->conn->prepare("UPDATE payment_transactions SET status = :status, metadata = :metadata, updated_at = NOW() WHERE id = :id");
        return $stmt->execute([
            'status' => $status,
            'metadata' => $this->encodeMeta($meta),
            'id' => $transactionId,
        ]);
    }

    // Affiliate program
    public function registerAffiliatePartner($userId, $code = null, $ratePercent = 5.0, array $meta = [])
    {
        $code = $code ?: $this->generateAffiliateCode($userId);
        $stmt = $this->conn->prepare("INSERT INTO affiliate_partners (user_id, code, rate_percent, metadata) VALUES (:user_id, :code, :rate_percent, :metadata)");
        $stmt->execute([
            'user_id' => $userId,
            'code' => $code,
            'rate_percent' => $ratePercent,
            'metadata' => $this->encodeMeta($meta),
        ]);

        return $code;
    }

    public function recordAffiliateReferral($code, $referredUserId, $orderId, $orderAmount, array $meta = [])
    {
        $partner = $this->fetchAffiliateByCode($code);
        if (!$partner) {
            throw new RuntimeException('Affiliate code not found');
        }

        $commission = ($orderAmount * $partner['rate_percent']) / 100;

        $stmt = $this->conn->prepare("INSERT INTO affiliate_referrals (partner_id, referred_user_id, order_id, order_amount, commission_amount, metadata) VALUES (:partner_id, :referred_user_id, :order_id, :order_amount, :commission_amount, :metadata)");
        $stmt->execute([
            'partner_id' => $partner['id'],
            'referred_user_id' => $referredUserId,
            'order_id' => $orderId,
            'order_amount' => $orderAmount,
            'commission_amount' => $commission,
            'metadata' => $this->encodeMeta($meta),
        ]);

        $this->conn->prepare("UPDATE affiliate_partners SET total_commission = total_commission + :commission WHERE id = :id")
            ->execute(['commission' => $commission, 'id' => $partner['id']]);

        return $commission;
    }

    public function queueAffiliatePayout($partnerId, $amount, $method = 'wallet', array $meta = [])
    {
        $stmt = $this->conn->prepare("INSERT INTO affiliate_payouts (partner_id, amount, method, metadata) VALUES (:partner_id, :amount, :method, :metadata)");
        $stmt->execute([
            'partner_id' => $partnerId,
            'amount' => $amount,
            'method' => $method,
            'metadata' => $this->encodeMeta($meta),
        ]);

        return $this->conn->lastInsertId();
    }

    private function fetchAffiliateByCode($code)
    {
        $stmt = $this->conn->prepare("SELECT * FROM affiliate_partners WHERE code = :code LIMIT 1");
        $stmt->execute(['code' => $code]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Reviews / ratings
    public function submitProductReview($productId, $userId, $rating, $title, $content, array $media = [])
    {
        $stmt = $this->conn->prepare("INSERT INTO product_reviews (product_id, user_id, rating, title, content, media) VALUES (:product_id, :user_id, :rating, :title, :content, :media)");
        $stmt->execute([
            'product_id' => $productId,
            'user_id' => $userId,
            'rating' => $rating,
            'title' => $title,
            'content' => $content,
            'media' => $this->encodeMeta($media),
        ]);

        return $this->conn->lastInsertId();
    }

    // Flash sale / bundles
    public function createFlashSale($name, DateTimeInterface $startAt, DateTimeInterface $endAt, $discountType, $discountValue, array $meta = [], $description = null, $maxPurchase = null)
    {
        $stmt = $this->conn->prepare("INSERT INTO flash_sales (name, description, start_at, end_at, discount_type, discount_value, max_purchase, status, metadata) VALUES (:name, :description, :start_at, :end_at, :discount_type, :discount_value, :max_purchase, :status, :metadata)");
        $stmt->execute([
            'name' => $name,
            'description' => $description,
            'start_at' => $startAt->format('Y-m-d H:i:s'),
            'end_at' => $endAt->format('Y-m-d H:i:s'),
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'max_purchase' => $maxPurchase,
            'status' => 'scheduled',
            'metadata' => $this->encodeMeta($meta),
        ]);

        return $this->conn->lastInsertId();
    }

    public function attachFlashSaleItem($flashSaleId, $productId, $promoPrice, $stock, $limitPerUser = null)
    {
        $stmt = $this->conn->prepare("INSERT INTO flash_sale_items (flash_sale_id, product_id, promo_price, stock, limit_per_user) VALUES (:flash_sale_id, :product_id, :promo_price, :stock, :limit_per_user)");
        return $stmt->execute([
            'flash_sale_id' => $flashSaleId,
            'product_id' => $productId,
            'promo_price' => $promoPrice,
            'stock' => $stock,
            'limit_per_user' => $limitPerUser,
        ]);
    }

    public function createBundle($name, $price, array $items = [], $description = null, $status = 'draft', array $meta = [])
    {
        $stmt = $this->conn->prepare("INSERT INTO bundle_sets (name, description, price, status, metadata) VALUES (:name, :description, :price, :status, :metadata)");
        $stmt->execute([
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'status' => $status,
            'metadata' => $this->encodeMeta($meta),
        ]);

        $bundleId = $this->conn->lastInsertId();

        if ($items) {
            $itemStmt = $this->conn->prepare("INSERT INTO bundle_items (bundle_id, product_id, quantity) VALUES (:bundle_id, :product_id, :quantity)");
            foreach ($items as $item) {
                $itemStmt->execute([
                    'bundle_id' => $bundleId,
                    'product_id' => $item['product_id'],
                    'quantity' => isset($item['quantity']) ? $item['quantity'] : 1,
                ]);
            }
        }

        return $bundleId;
    }

    // Inventory + stock analytics
    public function recordInventoryMovement($productId, $quantity, $movementType, $reference = null, array $meta = [])
    {
        $stmt = $this->conn->prepare("INSERT INTO inventory_movements (product_id, movement_type, quantity, reference, metadata) VALUES (:product_id, :movement_type, :quantity, :reference, :metadata)");
        $stmt->execute([
            'product_id' => $productId,
            'movement_type' => $movementType,
            'quantity' => $quantity,
            'reference' => $reference,
            'metadata' => $this->encodeMeta($meta),
        ]);

        $snapshot = $this->conn->prepare("INSERT INTO inventory_snapshots (product_id, quantity_available, captured_at) VALUES (:product_id, :quantity, NOW())
            ON DUPLICATE KEY UPDATE quantity_available = quantity_available + VALUES(quantity_available), captured_at = NOW()");
        $snapshot->execute([
            'product_id' => $productId,
            'quantity' => $quantity,
        ]);
    }

    public function snapshotSalesReport($reportType, DateTimeInterface $date, array $metrics = [])
    {
        $stmt = $this->conn->prepare("INSERT INTO sales_reports (report_date, report_type, metrics) VALUES (:report_date, :report_type, :metrics)
            ON DUPLICATE KEY UPDATE metrics = VALUES(metrics), created_at = NOW()");
        $stmt->execute([
            'report_date' => $date->format('Y-m-d'),
            'report_type' => $reportType,
            'metrics' => $this->encodeMeta($metrics),
        ]);
    }

    public function trackCustomerEvent($userId, $eventType, array $payload = [], $channel = null)
    {
        $stmt = $this->conn->prepare("INSERT INTO customer_events (user_id, event_type, payload, channel) VALUES (:user_id, :event_type, :payload, :channel)");
        $stmt->execute([
            'user_id' => $userId,
            'event_type' => $eventType,
            'payload' => $this->encodeMeta($payload),
            'channel' => $channel,
        ]);
    }

    public function storeRealtimeMetric($metricKey, $value, array $payload = [])
    {
        $stmt = $this->conn->prepare("INSERT INTO realtime_metrics (metric_key, metric_value, payload) VALUES (:metric_key, :metric_value, :payload)");
        $stmt->execute([
            'metric_key' => $metricKey,
            'metric_value' => $value,
            'payload' => $this->encodeMeta($payload),
        ]);
    }

    public function storeAnalyticsSnapshot($snapshotKey, array $payload)
    {
        $stmt = $this->conn->prepare("INSERT INTO analytics_snapshots (snapshot_key, payload, captured_at) VALUES (:snapshot_key, :payload, NOW())
            ON DUPLICATE KEY UPDATE payload = VALUES(payload), captured_at = NOW()");
        $stmt->execute([
            'snapshot_key' => $snapshotKey,
            'payload' => $this->encodeMeta($payload),
        ]);
    }

    // Support center + live chat
    public function openLiveChat($userId, $channel = 'web', $agentId = null, array $meta = [])
    {
        $stmt = $this->conn->prepare("INSERT INTO live_chat_sessions (user_id, agent_id, channel, metadata) VALUES (:user_id, :agent_id, :channel, :metadata)");
        $stmt->execute([
            'user_id' => $userId,
            'agent_id' => $agentId,
            'channel' => $channel,
            'metadata' => $this->encodeMeta($meta),
        ]);

        return $this->conn->lastInsertId();
    }

    public function appendLiveChatMessage($sessionId, $senderType, $senderId, $message, array $meta = [])
    {
        $stmt = $this->conn->prepare("INSERT INTO live_chat_messages (session_id, sender_type, sender_id, message, metadata) VALUES (:session_id, :sender_type, :sender_id, :message, :metadata)");
        $stmt->execute([
            'session_id' => $sessionId,
            'sender_type' => $senderType,
            'sender_id' => $senderId,
            'message' => $message,
            'metadata' => $this->encodeMeta($meta),
        ]);
    }

    public function closeLiveChat($sessionId)
    {
        $stmt = $this->conn->prepare("UPDATE live_chat_sessions SET status = 'closed', closed_at = NOW() WHERE id = :id");
        return $stmt->execute(['id' => $sessionId]);
    }

    public function openTicket($userId, $subject, $category = 'general', $priority = 'normal', $channel = 'web', array $meta = [])
    {
        $ticketNo = $this->generateTicketNumber();
        $stmt = $this->conn->prepare("INSERT INTO support_tickets (ticket_no, user_id, subject, category, priority, channel, metadata) VALUES (:ticket_no, :user_id, :subject, :category, :priority, :channel, :metadata)");
        $stmt->execute([
            'ticket_no' => $ticketNo,
            'user_id' => $userId,
            'subject' => $subject,
            'category' => $category,
            'priority' => $priority,
            'channel' => $channel,
            'metadata' => $this->encodeMeta($meta),
        ]);

        return $ticketNo;
    }

    public function replyTicket($ticketNo, $senderType, $senderId, $message, $attachment = null)
    {
        $ticket = $this->fetchTicket($ticketNo);
        if (!$ticket) {
            throw new RuntimeException('Ticket not found');
        }

        $stmt = $this->conn->prepare("INSERT INTO support_messages (ticket_id, sender_type, sender_id, message, attachment) VALUES (:ticket_id, :sender_type, :sender_id, :message, :attachment)");
        $stmt->execute([
            'ticket_id' => $ticket['id'],
            'sender_type' => $senderType,
            'sender_id' => $senderId,
            'message' => $message,
            'attachment' => $attachment,
        ]);

        $newStatus = $senderType === 'agent' ? 'pending-user' : 'open';
        $this->conn->prepare("UPDATE support_tickets SET status = :status, updated_at = NOW() WHERE id = :id")
            ->execute(['status' => $newStatus, 'id' => $ticket['id']]);
    }

    public function closeTicket($ticketNo)
    {
        $stmt = $this->conn->prepare("UPDATE support_tickets SET status = 'closed', updated_at = NOW() WHERE ticket_no = :ticket_no");
        return $stmt->execute(['ticket_no' => $ticketNo]);
    }

    private function fetchTicket($ticketNo)
    {
        $stmt = $this->conn->prepare("SELECT * FROM support_tickets WHERE ticket_no = :ticket_no LIMIT 1");
        $stmt->execute(['ticket_no' => $ticketNo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function upsertFaq($question, $answer, $category = 'general', $isInteractive = 0, $sortOrder = 0, array $meta = [], $faqId = null)
    {
        if ($faqId) {
            $stmt = $this->conn->prepare("UPDATE faqs SET question = :question, answer = :answer, category = :category, is_interactive = :is_interactive, sort_order = :sort_order, metadata = :metadata WHERE id = :id");
            return $stmt->execute([
                'question' => $question,
                'answer' => $answer,
                'category' => $category,
                'is_interactive' => $isInteractive,
                'sort_order' => $sortOrder,
                'metadata' => $this->encodeMeta($meta),
                'id' => $faqId,
            ]);
        }

        $stmt = $this->conn->prepare("INSERT INTO faqs (question, answer, category, is_interactive, sort_order, metadata) VALUES (:question, :answer, :category, :is_interactive, :sort_order, :metadata)");
        return $stmt->execute([
            'question' => $question,
            'answer' => $answer,
            'category' => $category,
            'is_interactive' => $isInteractive,
            'sort_order' => $sortOrder,
            'metadata' => $this->encodeMeta($meta),
        ]);
    }

    public function queueNotification($userId, $channel, $subject, $message, DateTimeInterface $scheduleAt = null, array $meta = [])
    {
        $stmt = $this->conn->prepare("INSERT INTO notifications (user_id, channel, subject, message, scheduled_at, metadata) VALUES (:user_id, :channel, :subject, :message, :scheduled_at, :metadata)");
        $stmt->execute([
            'user_id' => $userId,
            'channel' => $channel,
            'subject' => $subject,
            'message' => $message,
            'scheduled_at' => $scheduleAt ? $scheduleAt->format('Y-m-d H:i:s') : null,
            'metadata' => $this->encodeMeta($meta),
        ]);

        return $this->conn->lastInsertId();
    }

    public function markNotificationSent($notificationId)
    {
        $stmt = $this->conn->prepare("UPDATE notifications SET status = 'sent', sent_at = NOW() WHERE id = :id");
        return $stmt->execute(['id' => $notificationId]);
    }

    // Daily login reward track
    public function rewardDailyLogin($userId)
    {
        $wallet = $this->getWallet($userId);
        $today = (new DateTimeImmutable('today'))->format('Y-m-d');
        if ($wallet['last_daily_login'] === $today) {
            return ['status' => 'already_claimed'];
        }

        $lastLog = $this->conn->prepare("SELECT * FROM daily_login_logs WHERE user_id = :user_id ORDER BY rewarded_at DESC LIMIT 1");
        $lastLog->execute(['user_id' => $userId]);
        $previous = $lastLog->fetch(PDO::FETCH_ASSOC);

        $streak = 1;
        if ($previous) {
            $lastDate = (new DateTimeImmutable($previous['rewarded_at']))->format('Y-m-d');
            if ($lastDate === (new DateTimeImmutable('yesterday'))->format('Y-m-d')) {
                $streak = $previous['streak_count'] + 1;
            }
        }

        $rewardCount = (int) $this->conn->query("SELECT COUNT(*) FROM daily_login_rewards")->fetchColumn();
        if ($rewardCount === 0) {
            return ['status' => 'missing_config'];
        }

        $dayIndex = (($streak - 1) % $rewardCount) + 1;
        $rewardStmt = $this->conn->prepare("SELECT * FROM daily_login_rewards WHERE day_index = :day_index LIMIT 1");
        $rewardStmt->execute(['day_index' => $dayIndex]);
        $reward = $rewardStmt->fetch(PDO::FETCH_ASSOC);
        if (!$reward) {
            return ['status' => 'missing_config'];
        }

        $payload = ['day_index' => $dayIndex, 'reward_type' => $reward['reward_type']];

        if ($reward['reward_type'] === 'wallet_credit') {
            $amount = (float) $reward['reward_value'];
            if ($wallet['vip_level'] !== 'standard') {
                $amount = $amount * (float) $reward['vip_multiplier'];
            }
            $this->creditWallet($userId, $amount, 'daily_login', $reward['id'], ['streak' => $streak]);
            $payload['amount'] = $amount;
        } elseif ($reward['reward_type'] === 'loyalty_points') {
            $points = (int) $reward['reward_value'];
            $this->addLoyaltyPoints($userId, $points, 'daily_login', $reward['id'], ['streak' => $streak]);
            $payload['points'] = $points;
        } elseif ($reward['reward_type'] === 'coupon') {
            $payload['coupon_code'] = $reward['reward_value'];
        }

        $this->conn->prepare("INSERT INTO daily_login_logs (user_id, reward_id, streak_count) VALUES (:user_id, :reward_id, :streak_count)")
            ->execute([
                'user_id' => $userId,
                'reward_id' => $reward['id'],
                'streak_count' => $streak,
            ]);

        $this->conn->prepare("UPDATE user_wallets SET last_daily_login = :today WHERE user_id = :user_id")
            ->execute(['today' => $today, 'user_id' => $userId]);

        return ['status' => 'rewarded', 'payload' => $payload, 'streak' => $streak];
    }

    public function getDailyLoginProgress($userId)
    {
        $log = $this->conn->prepare("SELECT * FROM daily_login_logs WHERE user_id = :user_id ORDER BY rewarded_at DESC LIMIT 1");
        $log->execute(['user_id' => $userId]);
        $latest = $log->fetch(PDO::FETCH_ASSOC);
        $rewardCount = (int) $this->conn->query("SELECT COUNT(*) FROM daily_login_rewards")->fetchColumn();
        $nextDay = $latest ? (($latest['streak_count']) % max($rewardCount, 1)) + 1 : 1;

        return ['latest' => $latest, 'next_day' => $nextDay];
    }

    private function encodeMeta($meta)
    {
        return $meta ? json_encode($meta, JSON_UNESCAPED_UNICODE) : null;
    }

    private function generateAffiliateCode($userId)
    {
        return strtoupper('AFF' . $userId . dechex(random_int(1000, 999999)));
    }

    private function generateTicketNumber()
    {
        return 'T' . date('ymd') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    }
}

if (!function_exists('feature_service')) {
    function feature_service()
    {
        return PlatformFeatureService::instance();
    }
}
