<?php

/*	
	=======================================================================
	CLASS NAME: 	tc_transaction
	DESCRIPTION:	An individual transaction record, holding all key 
					fields to describe the transaction 
	======================================================================= 
*/
	class tc_transaction {
		
		protected $id;
		protected $date;
		protected $accountid;
		protected $payee;
		protected $category;
		protected $amountcr;
		protected $notes;
		protected $iscleared;
		protected $isplaceholder;
		protected $isdirty;
		protected $txnbalance;
		protected $displaycr;
		protected $displaydr;
		protected $txntype;
		protected $transfertxn;
		protected $datetime;
        protected $statetxntype = null;

		//CONSTRUCTOR methods
		public function __construct($id=null, $dt=null, $ac=null, $py=null, $ca=null, 
									$am=null, $nt=null, $ic=null, $ip=null, $tb=null, 
                                    $tx=null, $sf=null) {

			if (isset($id)) { $this->id = $id; }
			if (isset($dt)) { $this->date = $dt; }
			if (isset($ac)) { $this->accountid = $ac; }
			if (isset($py)) { $this->payee = $py; }
			if (isset($ca)) { $this->category = $ca; }
			if (isset($am)) { $this->amountcr = $am; }
			if (isset($nt)) { $this->notes = $nt; }
			if (isset($ic)) { $this->iscleared = $ic; }
			if (isset($ip)) { $this->isplaceholder = $ip; }
			if (isset($tb)) { $this->txnbalance = $tb; }
            if (isset($tx)) { $this->transfertxn = $tx; }
            if (isset($sf)) { $this->suppressfetch = $sf; }
			$this->isdirty = false;

			//if only the $id was provided, then auto-load from the database
			if (isset($id) && $id != "" && !$sf) {

				$tmp_txn = db_getTxn($id);
                
				if (isset($tmp_txn)) {
					$this->date = $tmp_txn["date"];
					$this->accountid = $tmp_txn["accountid"];
					$this->payee = $tmp_txn["payee"];
					$this->category = $tmp_txn["category"];
					$this->amountcr = $tmp_txn["amountcr"];
					$this->notes = $tmp_txn["notes"];
					$this->iscleared = $tmp_txn["iscleared"];
					$this->isplaceholder = $tmp_txn["isplaceholder"];
					$this->transfertxn = $tmp_txn["transfertxn"];
				} else {
					$_SESSION["errText"] = $e;
				}

				$this->isdirty = true;
			}
		}
		
		//GET methods
		public function getId() {return $this->id;}
		public function getDate() {return $this->date;}
		public function getDateTime() {$dt = new DateTime($this->date); return $dt;}
		public function isFutureDated() {return ( strtotime($this->date) > strtotime('now') ); } 

		public function getYear() {return $this->getDateTime()->format('Y');}
		public function getMonth() {return $this->getDateTime()->format('m');}
		public function getDay() {return $this->getDateTime()->format('d');}
		public function getDateFormatted() {return $this->getDateTime()->format('d/m/Y');}		

		public function getAccountId() {return $this->accountid;}
		public function getPayee() {return $this->payee;}
		public function getCategory() {return $this->category;}
		public function getAmountCr() {return $this->amountcr;}
		public function getNotes() {return $this->notes;}
		public function getIsCleared() {return $this->iscleared;}
		public function getIsPlaceholder() {return $this->isplaceholder;}
		public function getIsDirty() {return $this->isdirty;}
		public function getTxnBalance() {return number_format((float)$this->txnbalance, 2, ".", ",");}
		public function getTransferTxn() {return $this->transfertxn;}

		public function getDisplayDr() {
			if ($this->amountcr <= 0) {
				return number_format((float)$this->amountcr * -1, 2, '.', '');
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

		//SET methods
		public function setDate($dt) {$this->date = $dt;}
		public function setAccountId($ac) {$this->accountid = $ac;}
		public function setPayee($py) {$this->payee = $py;}
		public function setCategory($ca) {$this->category = $ca;}

        public function setStateTxnType($st) {
            if (in_array($st, array("Payment", "Deposit", "Transfer"))) { 
                $this->statetxntype = $st;
            } else {
                $this->statetxntype = null;
            }
        }

		//OTHER methods
		public function updateTrans($id, $dt, $ac, $py, $ca, $am, $nt, $ic, $ip, $di, $tb) {
			$this->id = $id;
			$this->date = $dt;
			$this->accountid = $ac;
			$this->payee = $py;
			$this->category = $ca;
			$this->amountcr = $am;
			$this->notes = $nt;
			$this->iscleared = $ic;
			$this->isplaceholder = $ip;
			$this->isdirty = $di;
			$this->txnbalance = $tb;
		}		
	}


/*	
	=======================================================================
	CLASS NAME: 	tc_account
	DESCRIPTION:	a single account that has a name, a balance, and 
					contains an array of transactions
	======================================================================= 
*/
	class tc_account {
		
		protected $accname;
		protected $fullname;
		protected $accbalance;
		protected $accountgroup;
		protected $accounttype;
		protected $transaction = array();

		//CONSTRUCTOR methods
		public function __construct($n=null, $ag=null, $fn=null, $b=null, $t=null) {
			if (isset($n)) { $this->accname = $n; }
			if (isset($ag)) { $this->accountgroup = $ag; }		
			if (isset($fn)) { $this->fullname = $fn; }	
			if (isset($b)) { 
                $this->accbalance = $b; 
            } else { 
                $bal = db_getBalance($n, $this->accountgroup);
                $this->accbalance = $bal["balance"]; 
            }
			if (isset($t)) { $this->accounttype = $t; }
		}


		//GET methods
		public function getName() { return $this->accname; }
		public function getFullName() { return $this->fullname; }
		public function getTransactions() { return $this->transaction; }
		public function getAccountType() { return $this->accounttype; }
		public function getBalance($nofmt = false) { 
            if ($nofmt) {
                return number_format((float)$this->accbalance, 2, ".", ""); 
            } else {
                return number_format((float)$this->accbalance, 2, ".", ","); 
            }
        }

        public function getTodaysBalance() {
           foreach ($this->transaction as $txn) {
               if (strtotime($txn->getDate()) <= strtotime('today')) {
                   return array ("balance" => $txn->getTxnBalance(),
                                 "id" => $txn->getId());   
                   exit();
               }
           }
        }
                
        
		//SET methods
		public function setBalance($b) { 
			$this->accbalance = $b; 
		}

		//OTHER methods
		public function populateTransactions() {

			$ta = db_getAllTxnForAcct($this->accname, 100, $this->accountgroup);
			$decbalance = $this->accbalance;

			foreach ($ta as $t) {	
				
				//add the transaction record, using the "decbalance" property as 
                //the "txnbalance" of the transaction
				$this->transaction[] = new tc_transaction($t["id"], $t["date"], $t["accountid"], 
                                                        $t["payee"], $t["category"], $t["amountcr"], 
                                                        $t["notes"], $t["iscleared"], 
                                                        $t["isplaceholder"], $decbalance);

				//decrease the minbalance variable. Round to 2dp to ensure we 
                //don't get random overflows.
				$decbalance = round($decbalance - $t["amountcr"], 2);
			}
		}

		public function getTransaction($tid) {
			foreach ($this->transaction as $txn) {
				if ($txn->getId() == $tid) {
					return $txn;
				}
			}
		}

		public function addNewTransfer($dt, $ac, $py, $ca, $am, $nt, $ic, $ip) {
			return db_transferTxn($dt, $ac, $py, $ca, $am, $nt, $ic, $ip, $this->accountgroup);	
		}

		public function addNewTransaction($dt, $ac, $py, $ca, $am, $nt, $ic, $ip) {
			return db_addTxn($dt, $ac, $py, $ca, $am, $nt, $ic, $ip, $this->accountgroup);	
		}

		public function updateTransaction($id, $dt, $ac, $py, $ca, $am, $nt, $ic, $ip) {
			return db_updateTxn($id, $dt, $ac, $py, $ca, $am, $nt, $ic, $ip, $this->accountgroup);
		}
        
        public function adjustTransactionValue($ac, $id, $am, $ag) {
            return db_adjustTransactionValue($ac, $id, $am, $this->accountgroup);
        }

		public function removeTransaction($id) {
			return db_removeTxn($id, $this->accountgroup);
		}
	}


/*	
	=======================================================================
	CLASS NAME: 	tc_portfolio
	DESCRIPTION:	a collection of accounts, each of which has its own
					name, balance, and collection of transactions
	======================================================================= 
*/
	class tc_portfolio {

		protected $account = array();
		protected $accountgroup;

		public function __construct($ag) {

			$this->accountgroup = $ag;

			//populate portfolio based on supplied account group
			$balancesData = db_getAllBalances($ag);
			if (isset($balancesData)) {
				foreach ($balancesData as $accbal) {
					$this->addAccount(new tc_account($accbal["id"], 
														$ag, 
														$accbal["name"], 
														$accbal["balance"],
														$accbal["type"]
													)
									);
				}
			}	
		}

		public function addAccount($a) {
			if (is_a($a, 'tc_account')) {
				$this->account[] = $a;
				return true;
			} else {
				return false;
			}
		}

		public function getAccount($n) {
			foreach ($this->account as $ac) {
				if ($ac->getName() == $n) {
					return $ac;
				}
			}
		}

		public function getAllAccounts() {
			return $this->account;
		}

		public function getPayeeList($payeetype) {
			return db_getPayeeList($this->accountgroup, $payeetype);
		}

		public function getCategoryList() {
			return db_getCategoryList($_SESSION["accountgroup"]);
		}

	}



/*	
	=======================================================================
	CLASS NAME: 	tc_repeat
	DESCRIPTION:	a repeating transaction that can be applied to the register
	======================================================================= 
*/

    class tc_repeat {
		
		protected $id;
		protected $accountid;
		protected $payee;
		protected $category;
		protected $amountcr;
		protected $notes;
		protected $isplaceholder;
		protected $nextdate;
		protected $prevdate;
        protected $endondate;
        protected $frequency;
        protected $frequencyincrement;
        protected $accountgroup;

		//CONSTRUCTOR methods
		public function __construct($id=null,  $ag=null, $ac=null, $py=null, $ca=null, 
                                $am=null, $nt=null, $ip=null, $nd=null, $pd=null, $ed=null, 
                                $fr=null, $fi=null, $sf=false) {

			if (isset($id)) { $this->id = $id; }
            if (isset($ag)) { $this->accountgroup = $ag; }
			if (isset($ac)) { $this->accountid = $ac; }
			if (isset($py)) { $this->payee = $py; }
			if (isset($ca)) { $this->category = $ca; }
			if (isset($am)) { $this->amountcr = $am; }
			if (isset($nt)) { $this->notes = $nt; }
			if (isset($ip)) { $this->isplaceholder = $ip; }
			if (isset($nd)) { $this->nextdate = $nd; }
			if (isset($pd)) { $this->prevdate = $pd; }
			if (isset($ed)) { $this->endondate = $ed; }
			if (isset($fr)) { $this->frequency = $fr; }
			if (isset($fi)) { $this->frequencyincrement = $fi; }
			if (isset($sf)) { $this->suppressfetch = $sf; }
            
			//if only the $id was provided, then auto-load from the database
			if (isset($id) && $id != -1 && isset($ag) && $ag != "") {
                
                if (!$sf) {
                    $tmp_txn = db_getRepeating($id, $this->accountgroup);

                    if (isset($tmp_txn)) {
                        //$this->id = $tmp_txn["id"];
                        $this->accountid = $tmp_txn["accountid"];
                        $this->payee = $tmp_txn["payee"];
                        $this->category = $tmp_txn["category"];
                        $this->amountcr = $tmp_txn["amountcr"];
                        $this->notes = $tmp_txn["notes"];
                        $this->isplaceholder = $tmp_txn["isplaceholder"];
                        $this->nextdate = $tmp_txn["nextdate"];
                        $this->prevdate = $tmp_txn["prevdate"];
                        $this->endondate = $tmp_txn["endondate"];
                        $this->frequency = $tmp_txn["frequencycode"];
                        $this->frequencyincrement = $tmp_txn["frequencyincrement"];
                    } else {
                        //$_SESSION["errText"] = $e;
                    }
                }
            } else {
                //id = -1 OR accountgroup is empty... set default values
                $this->frequency = "M";
                $this->frequencyincrement = 1;
            }
        }
        
		//GET methods
		public function getId() {return $this->id;}

		public function getNextDateTime() {$dt = new DateTime($this->nextdate); return $dt;}
		public function getNextDate() {return $this->getNextDateTime()->format('Y-m-d');}
        public function getNextDateYear() {return $this->getNextDateTime()->format('Y');}
		public function getNextDateMonth() {return $this->getNextDateTime()->format('m');}
		public function getNextDateDay() {return $this->getNextDateTime()->format('d');}
		public function getNextDateFormatted() {return $this->getNextDateTime()->format('d/m/Y');}	

		public function getPrevDateTime() {$dt = new DateTime($this->prevdate); return $dt;}
		public function getPrevDate() {return $this->getPrevDateTime()->format('Y-m-d');}
        public function getPrevDateYear() {return $this->getPrevDateTime()->format('Y');}
		public function getPrevDateMonth() {return $this->getPrevDateTime()->format('m');}
		public function getPrevDateDay() {return $this->getPrevDateTime()->format('d');}
		public function getPrevDateFormatted() {return $this->getPrevDateTime()->format('d/m/Y');}	

		public function getEndOnDateTime() {$dt = new DateTime($this->endondate); return $dt;}
		public function getEndOnDate() {return $this->getEndOnDateTime()->format('Y-m-d');}
        public function getEndOnDateYear() {return $this->getEndOnDateTime()->format('Y');}
		public function getEndOnDateMonth() {return $this->getEndOnDateTime()->format('m');}
		public function getEndOnDateDay() {return $this->getEndOnDateTime()->format('d');}
		public function getEndOnDateFormatted() {return $this->getEndOnDateTime()->format('d/m/Y');}
        
        public function getAccountId() {return $this->accountid;}
		public function getPayee() {return $this->payee;}
		public function getCategory() {return $this->category;}
		public function getAmountCr() {return $this->amountcr;}
		public function getNotes() {return $this->notes;}
		public function getIsPlaceholder() {return $this->isplaceholder;}

		public function getFrequency() {return $this->frequency;}
		public function getFrequencyIncrement() {return $this->frequencyincrement;}

        public function getFrequencyFormatted($t_tense="f") {
            switch ($this->frequency) {
                case "Y": 
                    return ($t_tense == "f" ? "Yearly" : "Years");
                    break;
                case "M":
                    return ($t_tense == "f" ? "Monthly" : "Months");
                    break;
                case "W":
                    return ($t_tense == "f" ? "Weekly" : "Weeks");
                    break;
                case "D":
                    return ($t_tense == "f" ? "Daily" : "Days");
                    break;
                default: 
                    return $this->frequency;
            } 
        }
        
		public function getFrequencyIncrementFormatted() {
            switch ($this->frequency) {
                case "Y": 
                    return "Every " . $this->frequencyincrement . " Years";
                    break;
                case "M":
                    return "Every " . $this->frequencyincrement . " Months";
                    break;
                case "W":
                    return "Every " . $this->frequencyincrement . " Weeks";
                    break;
                case "D":
                    return "Every " . $this->frequencyincrement . " Days";
                    break;
                default: 
                    return $this->frequencyincrement;
            } 
        }

        
		public function getTxnType() {
            return ($this->amountcr <= 0 ? "Payment" : "Deposit");
		}
        
		public function getDisplayDr() {
			if ($this->amountcr <= 0) {
				return number_format((float)$this->amountcr * -1, 2, '.', '');
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

        //SET methods
		public function setPayee($py) {$this->payee = $py;}
		public function setCategory($ca) {$this->category = $ca;}
    }
        

/*	
	=======================================================================
	CLASS NAME: 	tc_repeatRegister
	DESCRIPTION:	a repeating transaction register, that holds many 
                    repeating transactions
	======================================================================= 
*/
    class tc_repeatRegister {
       
		protected $accountgroup;
        protected $transaction = array();
        
        public function __construct($ag) {
            $this->accountgroup = $ag;
        }
        
        public function getTransactions() { return $this->transaction; }
        
		//OTHER methods
		public function populateTransactions() {

			$ta = db_getAllRepeating($this->accountgroup);

			foreach ($ta as $t) {	
				
				$this->transaction[] = new tc_repeat($t["id"], $this->accountgroup);
			}
		}
    }

