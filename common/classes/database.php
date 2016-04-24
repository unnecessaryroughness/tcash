<?php

    namespace tcash;

    class Database {

		const DEV_HOSTNAME = 'localhost';
		const DEV_DBNAME = 'tonksdev_tcash';
		const DEV_UNAME = 'tonksdev_tcashu';
		const DEV_PWD = 'LA:553425';

        const RASPI_HOSTNAME = 'raspberrypi';
		const RASPI_DBNAME = 'tcash';
		const RASPI_UNAME = 'root';
		const RASPI_PWD = 'LA:553425';
        
        const RASPI2_HOSTNAME = 'raspi2';
		const RASPI2_DBNAME = 'tcash';
		const RASPI2_UNAME = 'root';
		const RASPI2_PWD = 'LA:553425';

        const WWW_HOSTNAME = '10.168.1.81';
		const WWW_DBNAME = 'tonksdev_tcash';
		const WWW_UNAME = 'tonksdev_tcashu';
		const WWW_PWD = 'LA:553425';
        
    /*  ============================================
        FUNCTION:   connect 
        PARAMS:     envname - the current environment
        RETURNS:    PDO object
        ============================================  */
        public static function connect($envname=null) {
                        
            //if no environment name passed, default to whatever 
            //is stored in the session variable envname
            $envname = $envname ? : $_SESSION["envname"];
            
            if ($envname == "[DEV]") {
                $hostname = Database::DEV_HOSTNAME;
                $dbname = Database::DEV_DBNAME;
                $uname = Database::DEV_UNAME;
                $pwd = Database::DEV_PWD;
            } elseif ($envname == "[RASPI]") {
                $hostname = Database::RASPI_HOSTNAME;
                $dbname = Database::RASPI_DBNAME;
                $uname = Database::RASPI_UNAME;
                $pwd = Database::RASPI_PWD;
            } elseif ($envname == "[RASPI2]") {
                $hostname = Database::RASPI2_HOSTNAME;
                $dbname = Database::RASPI2_DBNAME;
                $uname = Database::RASPI2_UNAME;
                $pwd = Database::RASPI2_PWD;
            } else {
                $hostname = Database::WWW_HOSTNAME;
                $dbname = Database::WWW_DBNAME;
                $uname = Database::WWW_UNAME;
                $pwd = Database::WWW_PWD;
            }

            try {
                $pdo = new \PDO('mysql:host=' . $hostname . ';dbname=' . $dbname, $uname, $pwd);
                $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $pdo->exec('SET NAMES "utf8"');
                return $pdo;
                
            } catch (\PDOException $e) {
                throw new TCASHException ('Unable to connect to the database server (' 
                                     . $hostname . '/' . $dbname . ')', $e);
		        exit();
            }
        }
        
    }
