<?php 
    
    namespace tcash;

    class Transaction {
        
        protected $accountgroup;
		protected $id;
		protected $txndate;
		protected $accountid;
		protected $payee;
		protected $category;
		protected $amountcr;
		protected $notes;
		protected $iscleared;
		protected $isplaceholder;
        protected $txnbalance;
		protected $transfertxn;
        protected $suppressfetch;
        protected $statetxntype = null;

     /*  ============================================
        FUNCTION:   __construct
        PARAMS:     ag      account group
                    id      transaction id
                    dt      transaction date
                    ac      account id
                    py      payee name
                    ca      category
                    am      amount
                    nt      notes
                    ic      is cleared true/false
                    ip      is placeholder true/false
                    tb      transaction balance - balance of account after this txn
                    tx      transfer transaction id - matching pair of txns
                    sf      suppress fetch - don't retrieve details from db
        RETURNS:    (none)
        PURPOSE:    Creates a new transaction. If only the account group and id are 
                    supplied, retrieves details from database instead of using parameters.
        ============================================  */
		public function __construct($ag=null, $id=null, $dt=null, 
                                    $ac=null, $py=null, $ca=null, 
                                    $am=null, $nt=null, $ic=null, 
                                    $ip=null, $tb=null, $tx=null, $sf=null) {

            $this->accountgroup = $ag;
			$this->id = $id;
			$this->txndate = $dt;
			$this->accountid = $ac;
			$this->payee = $py;
			$this->category = $ca;
			$this->amountcr = $am;
			$this->notes = $nt;
			$this->iscleared = $ic;
			$this->isplaceholder = $ip;
            $this->transfertxn = $tx;
			$this->txnbalance = $tb;
            $this->suppressfetch = $sf;

            //if only the $ag && $id was provided, and autoload was not suppressed,
            //then auto-load from the database
			if ((isset($ag) && !is_null($ag)) 
                && (isset($id) && !is_null($id) && is_numeric($id) && $id > 0) 
                && !$this->suppressfetch) {

                try {
				    $tmp_txn = self::getTxn($this->accountgroup, $this->id);
                } 
                catch (TCASHException $e) {
                    throw new TCASHException ("Error retrieving transaction", $e->displayOutput());
                }
                
				if (isset($tmp_txn)) {
					$this->txndate = $tmp_txn["date"];
					$this->accountid = $tmp_txn["accountid"];
					$this->payee = $tmp_txn["payee"];
					$this->category = $tmp_txn["category"];
					$this->amountcr = $tmp_txn["amountcr"];
					$this->notes = $tmp_txn["notes"];
					$this->iscleared = $tmp_txn["iscleared"];
					$this->isplaceholder = $tmp_txn["isplaceholder"];
					$this->transfertxn = $tmp_txn["transfertxn"];
				} else {
					throw new TCASHException ("No transaction returned", null);
				}
			}
		}

    //Simple GET methods
        public function getId() {return $this->id;}
        public function getDate() {return $this->txndate;}
        public function getAccountId() {return $this->accountid;}
        public function getPayee() {return $this->payee;}
        public function getCategory() {return $this->category;}
        public function getAmountCr() {return $this->amountcr;}
        public function getNotes() {return $this->notes;}
        public function getIsCleared() {return $this->iscleared;}
        public function getIsPlaceholder() {return $this->isplaceholder;}
        public function getTransferTxn() {return $this->transfertxn;}

    //Complex GET methods
        public function getDateTime() {
            $dt = new \DateTime($this->txndate); 
            return $dt;
        }        

        public function isFutureDated() {
            return ( strtotime($this->txndate) > strtotime('now') ); 
        } 

        public function getYear() {
            return $this->getDateTime()->format('Y');
        }

        public function getMonth() {
            return $this->getDateTime()->format('m');
        }

        public function getDay() {
            return $this->getDateTime()->format('d');
        }

        public function getDateFormatted() {
            return $this->getDateTime()->format('d/m/Y');
        }		

        public function getTxnBalance() {
            return number_format((float)$this->txnbalance, 2, ".", ",");
        }

        public function getDisplayDr() {
            if ($this->amountcr <= 0) {
                return number_format((float)-$this->amountcr, 2, '.', '');
            } else {
                return "";
            }
        }

        public function getDisplayCr() {
            if ($this->amountcr > 0) { 
                return number_format((float)$this->amountcr, 2, '.', '');
            } else {
                return "";
            }
        }

     /*  ============================================
        FUNCTION:   getTxnType
        PARAMS:     (none)
        RETURNS:    (string) type of transaction [Transfer|Payment|Deposit]
        PURPOSE:    Determines the transaction type
        ============================================  */
        public function getTxnType() {
            if ($this->transfertxn > 0) {
                return "Transfer"; 
            } else {			
                if ($this->statetxntype != null) {
                    return $this->statetxntype;
                } else {
                    return ($this->amountcr <= 0 ? "Payment" : "Deposit");
                }
            }
        }

    //Simple SET methods
        public function setDate($dt) {
            $this->txndate = $dt;
        }

        public function setAccountId($ac) {
            $this->accountid = $ac;
        }

        public function setPayee($py) {
            $this->payee = $py;
        }

        public function setCategory($ca) {
            $this->category = $ca;
        }

    //Complex SET methods
        public function setStateTxnType($st) {
            if (in_array($st, array("Payment", "Deposit", "Transfer"))) { 
                $this->statetxntype = $st;
            } else {
                $this->statetxntype = null;
            }
        }



//==STATIC FUNCTIONS==================================================//

        
     /*  ============================================
        FUNCTION:   getTxn
        PARAMS:     ag      account group
                    id      transaction id
        RETURNS:    (single associative array) transaction data 
        PURPOSE:    Retrieves the requested transaction from the database
        ============================================  */
        public static function getTxn($ag, $id) {
            try {
                    $pdo = Database::connect();
                    $sql = "CALL getTxnAG(:ag, :id)";
                    $qry = $pdo->prepare($sql);
                    $qry->bindValue(":ag", $ag);
                    $qry->bindValue(":id", $id);
                    $qry->execute();
                    return $qry->fetch(\PDO::FETCH_ASSOC); 
            }
            catch (PDOException $e) {
                throw new TCASHException("Error retrieving transaction", $e);
            }		
        }
        
    }
