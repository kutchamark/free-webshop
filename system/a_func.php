
        <?php
            session_start();
            $host = "localhost";
            $db_user = "root";
            $db_pass = "";
            $db =  "webshoplazyv1byjarvincenzo";
            //connect to database
            $conn = new PDO("mysql:host=$host;dbname=$db",$db_user,$db_pass);
            $conn->exec("set names utf8mb4");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //end connect to database
            //function query
            function dd_q($str, $arr = []) {
                global $conn;
                try {
                    $exec = $conn->prepare($str);
                    $exec->execute($arr);
                } catch (PDOException $e) {
                    return false;
                }
                return $exec;
            }
            //end function query

            function ensure_game_tables()
            {
                dd_q("
                    CREATE TABLE IF NOT EXISTS game_sets (
                        id INT(11) NOT NULL AUTO_INCREMENT,
                        name VARCHAR(255) NOT NULL,
                        type VARCHAR(32) NOT NULL,
                        image VARCHAR(255) DEFAULT NULL,
                        description VARCHAR(500) DEFAULT NULL,
                        entry_cost INT(11) NOT NULL DEFAULT 0,
                        is_active TINYINT(1) NOT NULL DEFAULT 1,
                        config TEXT DEFAULT NULL,
                        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        PRIMARY KEY (id)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                ");

                dd_q("
                    CREATE TABLE IF NOT EXISTS game_rewards (
                        id INT(11) NOT NULL AUTO_INCREMENT,
                        game_id INT(11) NOT NULL,
                        label VARCHAR(255) NOT NULL,
                        reward_type VARCHAR(32) NOT NULL DEFAULT 'text',
                        reward_value VARCHAR(500) DEFAULT NULL,
                        reward_amount INT(11) DEFAULT 0,
                        weight INT(11) NOT NULL DEFAULT 0,
                        color VARCHAR(32) DEFAULT NULL,
                        image VARCHAR(255) DEFAULT NULL,
                        rule_value VARCHAR(255) DEFAULT NULL,
                        sort_order INT(11) DEFAULT 0,
                        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        PRIMARY KEY (id),
                        KEY game_id (game_id)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                ");

                dd_q("
                    CREATE TABLE IF NOT EXISTS game_logs (
                        id INT(11) NOT NULL AUTO_INCREMENT,
                        game_id INT(11) NOT NULL,
                        user_id INT(11) NOT NULL,
                        username VARCHAR(100) NOT NULL,
                        game_type VARCHAR(32) NOT NULL,
                        entry_cost INT(11) NOT NULL,
                        choice_value VARCHAR(255) DEFAULT NULL,
                        system_value VARCHAR(255) DEFAULT NULL,
                        result_label VARCHAR(255) NOT NULL,
                        reward_type VARCHAR(32) NOT NULL,
                        reward_detail TEXT DEFAULT NULL,
                        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        PRIMARY KEY (id),
                        KEY game_id (game_id),
                        KEY user_id (user_id)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                ");
            }

            ensure_game_tables();

            require_once __DIR__ . '/modules/platform_features.php';
            PlatformFeatureService::boot($conn);

            //function check login
            function check_login(){
                if(!isset($_SESSION['id'])){
                    return false;
                }else{
                    return true;
                }
            }
            function checknull($var = []){
                foreach ($var as $key => $value) {
                    if($value == "" || empty($value) || !isset($value)){
                        return false;
                    }
                }
                return true;
            }
            $conf['sitekey'] = "6LdmDFkkAAAAAEKni0zQPY4MEtv2nxLodGLEQvVO";
            $conf['secretkey'] = "6LdmDFkkAAAAAAFYBGr37VPuRl-L1hfwraFwO5pW";
            function base_url(){
                return "";
            }
            $get_setting = dd_q("SELECT * FROM setting");
            $config = $get_setting->fetch(PDO::FETCH_ASSOC);
       
            $byshop = dd_q("SELECT * FROM byshop")->fetch(PDO::FETCH_ASSOC);
            $byshop_status = $byshop["status"];
            $byshop_key = $byshop["apikey"];
            $byshop_cost = $byshop["cost"];


            if (isset($_SESSION['id'])) {
                $q1 = dd_q("SELECT * FROM users WHERE id = ? LIMIT 1", [$_SESSION['id']]);
                $user = $q1->fetch(PDO::FETCH_ASSOC);
            }
                    
