<?php

    class ip_banner  
    {
        private $pdo;

        /**
         * construct an ip_banner instance.
         * @param PDO a PDO instance connected to the database that the requests will be stored at.
         */
        function __construct( $pdo ) {

            $this->pdo = $pdo;

        }
        /**
     * This function preforms a check for whether to IP ban or not for repeated request attempts at sensitive areas.
     * @param int additional parameter to seperate attempts ( for example : 0 is for user login attempts, 1 is for admin login attempts ).
     * @param int maximum attempts before banning.
     * @param int duration in minutes for which the maximum attempts occur.
     * @param int duration in minutes for which to ban the ip address.
     * @return boolean returns true if the ip should be banned and false otherwise.
     */
        function track_request_attempts( $attempts_seperation=0, $max_attempts = 10, $check_duration = 10, $ban_duration = 10){
            // TODO: preform table cleaning for attempts that are $duration * 2 old and clean ban list as well fire this request before starting the check
            // creating tables if they don't exsist
            $pdo = $this->pdo;
            $pdo->query("CREATE TABLE IF NOT EXISTS `ban_list` ( 
                `id` INT(10) NOT NULL AUTO_INCREMENT, 
                `ip` VARCHAR(20) NOT NULL , 
                `unban_at` INT(20) NOT NULL , 
                PRIMARY KEY (`id`)) ENGINE = InnoDB;
            ");

            $pdo->query("CREATE TABLE IF NOT EXISTS `request_attempts` ( 
                `id` INT(10) NOT NULL AUTO_INCREMENT, 
                `ip` VARCHAR(20) NOT NULL , 
                `timestamp` INT(20) NOT NULL ,
                `seperation` INT(3) NOT NULL ,
                PRIMARY KEY (`id`)) ENGINE = InnoDB;
            ");

            $ip = $_SERVER['REMOTE_ADDR'];
            $timestamp = time();
            $ban_interval = $timestamp - ($check_duration * 60);
            $pdo->query("DELETE FROM request_attempts WHERE timestamp < ". time() - ($check_duration * 2 * 60) );
            $pdo->query("DELETE FROM ban_list WHERE ip = '$_SERVER[REMOTE_ADDR]' AND unban_at < ". time() );
            $result = $pdo->query("SELECT id FROM request_attempts WHERE timestamp > $ban_interval AND ip='$ip' AND seperation=$attempts_seperation");
            $current_time = time();
            if( $pdo->query("SELECT * FROM ban_list WHERE ip = '$_SERVER[REMOTE_ADDR]' and unban_at > $current_time")->rowCount() > 0 )
            {
                return true;
            }
            if ($result->rowCount() >= $max_attempts) {

                $unban_at = $ban_duration * 60 + $current_time;
                $pdo->query("INSERT INTO ban_list ( ip, unban_at ) VALUES( '$ip', '$unban_at' )");
                return true;
            }
            $pdo->query("INSERT INTO request_attempts ( ip, timestamp, seperation ) VALUES( '$ip', $timestamp, $attempts_seperation ) ");
            return false;



        }
    }
    



?>
