<?php

    namespace tcash;

    class Account {

        const     TRANSACTION_LIMIT = 100;
        protected $accountid;
        protected $fullname;
		protected $accounttype;
		protected $accountgroup;
		protected $accbalance;
        protected $bankname;
		protected $transaction = array();

     /*  ============================================
        FUNCTION:   __construct
        PARAMS:     ac      account id
                    ag      account group id
                    fn      full name
                    b       account balance
                    t       account type
                    bk      bank name
        RETURNS:    (none)  
        PURPOSE:    Constructs the account object from the parameters and
                    retrieves the current balance from the db if required
        ============================================  */
		public function __construct($ag=null, $ac=null, $fn=null, $b=null, $t=null, $bk=null) {
			$this->accountid = $ac;
			$this->accountgroup = $ag;
			$this->fullname = $fn;
			$this->accounttype = $t;
            $this->accbalance = $b; 
            $this->bankname = $bk;

            //get basic account data, if not supplied
            if (!($this->fullname && $this->accounttype)) {
                $accdata = self::getAccountData($ag, $ac);
                $this->fullname = $accdata["name"];
                $this->accounttype = $accdata["type"];
                $this->bankname = $accdata["bank"];
            }
            
            //get balance data, if not supplied
            if (!$this->accbalance) {
                $this->accbalance = self::getAccBalance($this->accountgroup, $this->accountid);
            }
		}
        
        
    //Simple GET methods
		public function getAccountId()    { return $this->accountid;   }
		public function getFullName()     { return $this->fullname;    }
		public function getTransactions() { return $this->transaction; }
		public function getAccountType()  { return $this->accounttype; }
        public function getBankName()     { return $this->bankname;    }
        
        public function getBalance() {
            return number_format((float)$this->accbalance, 2, ".", ","); 
        }

        
    //Complex GET methods
        public function getTodaysBalance() {
           foreach ($this->transaction as $txn) {
               if (strtotime($txn->getDate()) <= strtotime('today')) {
                   return array ("balance" => $txn->getTxnBalance(),
                                 "id" => $txn->getId());   
               }
           }
        }

            
    /*  ============================================
        FUNCTION:   populateTransactions
        PARAMS:     tl      transaction limit. Defaults to constant TRANSACTION_LIMIT
        RETURNS:    (none)  
        PURPOSE:    Retrieves transaction data from the database
                    and creates Transaction objects for each, storing
                    in the $this->transaction array
        ============================================  */
		public function populateTransactions($tl = Account::TRANSACTION_LIMIT) {

			//get all transactions for this account
            try {
                $ta = self::getAllTxnForAcct($this->accountgroup, $this->accountid, $tl);
            } 
            catch (TCASHException $e) {
                throw new TCASHException ("Error populating transactions", $e->displayOutput()); 
            }
            
            //decumulator variable for account balance
            $decbalance = $this->accbalance;

			foreach ($ta as $t) {	
				
				//add the transaction record, using the "decbalance" property as 
                //the "txnbalance" of the transaction
				$this->transaction[] = new Transaction($this->accountgroup, 
                                            $t["id"], $t["date"], $t["accountid"], 
                                            $t["payee"], $t["category"], $t["amountcr"], 
                                            $t["notes"], $t["iscleared"], 
                                            $t["isplaceholder"], $decbalance);

				//decrease the minbalance variable. Round to 2dp to ensure we 
                //don't get random overflows.
				$decbalance = round($decbalance - $t["amountcr"], 2);
			}
		}

        
        
//==STATIC FUNCTIONS==================================================//

        
        
    /*  ============================================
        FUNCTION:   getAllTxnForAcct (static)
        PARAMS:     ag      the account group to use
                    ac      the account id to search
                    tl      the transaction limit, i.e. how many rows to return
        RETURNS:    (array of associative arrays) transaction data
        PURPOSE:    Retrieves transaction data from the database.
        ============================================  */
        private static function getAllTxnForAcct($ag, $ac, $tl = Account::TRANSACTION_LIMIT) {
            try {
                $pdo = Database::connect();
                $sql = "CALL getAllTxnForAcct(:ac, :tl, :ag)";
                $qry = $pdo->prepare($sql);
                $qry->bindValue(":ac", $ac);
                $qry->bindValue(":tl", $tl);
                $qry->bindValue(":ag", $ag);
                $qry->execute();
                return $qry->fetchall(\PDO::FETCH_ASSOC); 
            }
            catch (PDOException $e) {
                throw new TCASHException ("Error retrieving transactions from db", $e->getMessage());
            }
        }

        
    /*  ============================================
        FUNCTION:   getBalance (static)
        PARAMS:     ag      accountgroup to use
                    ac      account id to use
        RETURNS:    (single associative array) balance data
        PURPOSE:    Retrieves balance of a requested account from the db
        ============================================  */
        public static function getAccBalance($ag, $ac) {
            try {
                $pdo = Database::connect();
                $sql = "CALL getBalance(:ac, :ag)"; 
                $qry = $pdo->prepare($sql);
                $qry->bindValue(':ac', $ac);
                $qry->bindValue(':ag', $ag);
                $qry->execute();
                $rec = $qry->fetch(\PDO::FETCH_ASSOC);
                return number_format((float)$rec["balance"], 2, ".", ","); 
            } 
            catch (\PDOException $e) {
                throw new TCASHException ("Unable to retrieve account balance", $e->getMessage());
            } 
        }

        
    /*  ============================================
        FUNCTION:   getAccountData (static)
        PARAMS:     ag      accountgroup to use
                    ac      account id to use
        RETURNS:    (single associative array) account data
        PURPOSE:    Retrieves basic data about a requested account from the db
        ============================================  */
        public static function getAccountData($ag, $ac) {
            try {
                $pdo = Database::connect();
                $sql = "CALL getAccount(:ag, :ac)"; 
                $qry = $pdo->prepare($sql);
                $qry->bindValue(':ag', $ag);
                $qry->bindValue(':ac', $ac);
                $qry->execute();
                return $qry->fetch(\PDO::FETCH_ASSOC);
            } 
            catch (\PDOException $e) {
                throw new TCASHException ("Unable to retrieve account data", $e->getMessage());
            } 
        }

        
    /*  ============================================
        FUNCTION:   updateAccount (static)
        PARAMS:     ag      accountgroup to use
                    ac      account id to use
                    nm      account name to set
                    ty      account type to set
                    bk      bank name to set
        RETURNS:    (boolean) indicates whether update succeeded
        PURPOSE:    Updates name and type of an existing account
        ============================================  */
        public static function updateAccount($ac, $nm, $ty, $bk, $ag) {
            
            try {
                $pdo = Database::connect();
                $sql = "CALL updateAccount(:ac, :nm, :ty, :bk, :ag)"; 
                $qry = $pdo->prepare($sql);
                $qry->bindValue(':ac', $ac);
                $qry->bindValue(':nm', $nm);
                $qry->bindValue(':ty', $ty);
                $qry->bindValue(':bk', $bk);
                $qry->bindValue(':ag', $ag);
                return $qry->execute();
            } 
            catch (\PDOException $e) {
                throw new TCASHException ("Unable to update account data", $e->getMessage());
            } 
        }

        
    /*  ============================================
        FUNCTION:   addAccount (static)
        PARAMS:     ac      account id to set
                    nm      account name to set
                    ag      accountgroup to use
                    bk      bankname to use
                    ty      account type to set
        RETURNS:    (boolean) indicates whether update succeeded
        PURPOSE:    Adds a new account to the current account group
        ============================================  */
        public static function addAccount($ac, $nm, $ty, $bk, $ag) {
            try {
                $pdo = Database::connect();
                $sql = "CALL addAccount(:ac, :nm, :ty, :bk, :ag)"; 
                $qry = $pdo->prepare($sql);
                $qry->bindValue(':ac', $ac);
                $qry->bindValue(':nm', $nm);
                $qry->bindValue(':ty', $ty);
                $qry->bindValue(':bk', $bk);
                $qry->bindValue(':ag', $ag);
                return $qry->execute();
            } 
            catch (\PDOException $e) {
                Logger::log(new LogMessage("errored... " . $e));
                throw new TCASHException ("Unable to add new account data", $e->getMessage());
            } 
        }
        
        
    }