<?php


    namespace tcash;


    class Portfolio {
        
		public $account;
		protected $accountgroup;


    /*  ============================================
        FUNCTION:   __construct
        PARAMS:     ag     - string account group
        RETURNS:    boolean
        ============================================  */
		public function __construct($ag) {

			$this->accountgroup = $ag;

			//populate portfolio based on supplied account group
			$balancesData = self::getAllBalances($this->accountgroup);
            
			if (isset($balancesData)) {
				foreach ($balancesData as $accbal) {
                    
                    try {
                        $this->addAccount(new Account($accbal["id"], $this->accountgroup,
                                             $accbal["name"], $accbal["balance"],
                                             $accbal["type"]));
                    } 
                    catch (Exception $e) {
                       throw new TCASHException ("Error adding accounts to the portfolio", 
                                                 $e->getMessage());  
                    }
				}
			}	
            
            return true;
		}

        
    /*  ============================================
        FUNCTION:   addAccount
        PARAMS:     ac     - Account type object
        RETURNS:    boolean
        ============================================  */
		public function addAccount(Account $ac) {
            $this->account[] = $ac;
            return true;
		}
        

    /*  ============================================
        FUNCTION:   getAccount
        PARAMS:     id     - account id
        RETURNS:    Account type object
        ============================================  */
		public function getAccount($id) {
			foreach ($this->account as $ac) {
				if ($ac->getName() == $id) {
					return $ac;
				}
			}
		}

        
    /*  ============================================
        FUNCTION:   getAllAccounts
        PARAMS:     (none)
        RETURNS:    array of Account type objects
        ============================================  */
		public function getAllAccounts() {
			return $this->account;
		}

        
//==STATIC FUNCTIONS==================================================//
        
        
    /*  ============================================
        FUNCTION:   getAccountList 
        PARAMS:     ag     - accountgroup to search
                    actype - account type to query
        RETURNS:    array of Account objects
        ============================================  */
        public static function getAccountList($ag, $actype="%") {

            try {
                $pdo = Database::connect();
                $sql = "CALL getAccountList(:ag, :at)"; 
                $qry = $pdo->prepare($sql);
                $qry->bindValue(":ag", $ag);
                $qry->bindValue(":at", $actype);        
                $qry->execute();
                $rsp = $qry->fetchall(\PDO::FETCH_ASSOC);
                
                if ($rsp) {
                    foreach ($rsp as $rec) {
                        $retArray[] = new Account($rec["accountgroup"], $rec["id"],
                                             $rec["name"], 0.00, $rec["type"], $rec["bank"]);
                    }
                   return $retArray; 
                }
            } 
            catch (\PDOException $e) {
                throw new TCASHException ("Unable to retrieve account list", $e->getMessage());
            } 
            
        }

        
    /*  ============================================
        FUNCTION:   getAllBalances 
        PARAMS:     ag     - accountgroup to search
        RETURNS:    associative array
        ============================================  */
        public static function getAllBalances($ag) {

            try {
                $pdo = Database::connect();
                $sql = "CALL getAllBalances(:ag)"; 
                $qry = $pdo->prepare($sql);
                $qry->bindValue(":ag", $ag);
                $qry->execute();
                return $qry->fetchall(\PDO::FETCH_ASSOC);
            } 
            catch (\PDOException $e) {
                throw new TCASHException ("Unable to retrieve balances list", $e->getMessage());
            } 
        }

            
        
    /*  ============================================
        FUNCTION:   getPayeeList (static)
        PARAMS:     $ag - string account group
                    $pt - string payee type (P|A| )
        RETURNS:    associative array of payees
        ============================================  */
        public static function getPayeeList($ag, $pt) {

            try {
                $pdo = Database::connect();
                $sql = "CALL getPayeeList(:ag, :pt)"; 
                $qry = $pdo->prepare($sql);
                $qry->bindValue(":ag", $ag);
                $qry->bindValue(":pt", $pt);
                $qry->execute();
                return $qry->fetchall(\PDO::FETCH_ASSOC);
            } 
            catch (\PDOException $e) {
                throw new TCASHException ("Unable to retrieve payee list", $e->getMessage());
            } 
		}

        
        
    /*  ============================================
        FUNCTION:   getCategoryList (static)
        PARAMS:     ag - string account group
        RETURNS:    associative array of categories
        ============================================  */
		public static function getCategoryList($ag) {

            try {
                $pdo = Database::connect();
                $sql = "CALL getCategoryList(:ag)"; 
                $qry = $pdo->prepare($sql);
                $qry->bindValue(":ag", $ag);
                $qry->execute();
                return $qry->fetchall(\PDO::FETCH_ASSOC);
            } 
            catch (\PDOException $e) {
                throw new TCASHException ("Unable to retrieve category list", $e->getMessage());
            } 
		}
                
        
    }