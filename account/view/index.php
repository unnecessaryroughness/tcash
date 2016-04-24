<?php

/*
	================================================================================
	TODO: 
    * On HTML/CSS for this page, ensure that a transfer cannot have its
		  	  transaction type changed (screws up the cross references). 

	================================================================================
*/

    use \tcash as t;

    try {
        include_once $_SERVER['DOCUMENT_ROOT'] . '/tcash/common/tcash-config.inc.php';
    } 
    catch (t\TCASHException $e) {            
        $_SESSION["errText"] = $e->displayOutput();
        $usePanel = 'common/tcash-panel-error.html.php';
        include '../../tcash-frame.html.php';
        exit();
    }

	//===========================================
	//HANDLE RESPONSE VALUES FROM THIS PAGE, 
	//INCLUDING THE MAIN MENU
	//===========================================

    if (isset($_POST["refdata"]) && $_POST["refdata"] != "") {

        //branching to add a new lookup value. 
        //Store the current URL and cache the current transaction in session.
        $_SESSION["returnURL"] = $_POST["returnURL"];
        $_SESSION["tempTxn"] = serialize(recreateScreenTxn());	       
        $redirect = ($_POST["refdata"] == "addpayee" ? "payee" : "category");        
        header("location: /tcash/lookup/" . $redirect . "/?mode=add");
        exit();
        
    } elseif (isset($_POST["action"])) {

        //a CRUD operation on a transaction is taking place; create a new account object
        $account = new tc_account($_POST["accountid"], $_SESSION["accountgroup"]);
		
		//convert the unsigned amount into a signed amount, depending on the transaction type
        switch ($_POST["txntype"]) {
            case "Deposit": 
                $amountcr = $_POST["txnamount"];
                break;
            case "Payment":
                $amountcr = -$_POST["txnamount"];
                break;
            case "Transfer":
                $amountcr = ($_POST["action"] == "add") ? -$_POST["txnamount"] :  $_POST["txnamount"];
                break;
        }
            
        //determine what to do next by which button was clicked
        switch ($_POST["action"]) {

			case "add": 

                //cache the date supplied from the form to use in the next transaction
				$_SESSION["prevdate"] = new DateTime($_POST["date"]);
		
                //add a new transfer from one account to another... 
				If ($_POST["txntype"] == "Transfer") {

					if ($account->addNewTransfer($_POST["date"], 
                                                 $_POST["accountid"], 
                                                 $_POST["txfaccount"], 
                                                 $_POST["category"], 
                                                 $amountcr, 
                                                 $_POST["notes"], 
												 (isset($_POST["iscleared"]) ? 1 : 0), 
												 (isset($_POST["isplaceholder"]) ? 1 : 0)) ) {	
	                   $addOK = true;
					} else {
                       $_SESSION["errText"] = "Error adding transaction";
	                   $addOK = false;
					}
                    
				} else {
					//add a regular transaction...
					if ($account->addNewTransaction($_POST["date"], 
                                                    $_POST["accountid"], 
                                                    $_POST["payee"], 
                                                    $_POST["category"], 
                                                    $amountcr, 
                                                    $_POST["notes"], 
													(isset($_POST["iscleared"]) ? 1 : 0), 
													(isset($_POST["isplaceholder"]) ? 1 : 0)) ) {	
	
	                   $addOK = true;
					} else {
				       $_SESSION["errText"] = "Error adding transaction: " . $_SESSION["errText"];
	                   $addOK = false;
					}				
				}

            
                //if we have to reduce a placeholder then execute that now also
                if ($addOK && isset($_POST["reduce"]) && $_POST["reduce"] != "") {
                    if ($account->adjustTransactionValue($_POST["accountid"], 
                                                         $_POST["reduce"], 
                                                         $_POST["reduceamt"],
                                                         $_SESSION["accountgroup"])) {

                        $addOK = true;
                    } else {
				        $_SESSION["errText"] = "Error reducing placeholder: " . $_SESSION["errText"];
                        $addOK = false;
                    }
                }
            
                if ($addOK) {
                    t\Logger::log(new t\LogMessage("User " . $_SESSION["userobj"]->getUserId() . 
                                                   " added transaction for " . $amountcr . 
                                                   " from IP " . $_SERVER["REMOTE_ADDR"]));

                    header("location: /tcash/account?acc=" . $account->getName());	
                    exit();
                } else {
                    t\Logger::log(new t\LogMessage("User " . $_SESSION["userobj"]->getUserId() . 
                                                   " failed to add transaction for " . $amountcr . 
                                                   " from IP " . $_SERVER["REMOTE_ADDR"]));
                }
                        
				break;
			
			case "update":	

                //payee might come from one of two drop-down boxes, depending on transaction type
                $payee = ($_POST["txntype"] == "Transfer" ? $_POST["txfaccount"] : $_POST["payee"]);

                //update transaction
                if ($account->updateTransaction($_POST["txnid"], 
                                                $_POST["date"], 
                                                $_POST["accountid"], 
                                                $payee, 
					  						    $_POST["category"], 
                                                $amountcr, 
                                                $_POST["notes"], 
											    (isset($_POST["iscleared"]) ? 1 : 0), 
												(isset($_POST["isplaceholder"]) ? 1 : 0)) ) {	
	

                    t\Logger::log(new t\LogMessage("User " . $_SESSION["userobj"]->getUserId() . 
                                                   " updated transaction " . $_POST["txnid"] . 
                                                   " from IP " . $_SERVER["REMOTE_ADDR"]));

                    header("location: /tcash/account?acc=" . $account->getName() . 
                           "#hlink-" . $_POST["txnid"]);	
					exit();
				
                } else {

                    t\Logger::log(new t\LogMessage("User " . $_SESSION["userobj"]->getUserId() . 
                                                " failed to update transaction " . $_POST["txnid"] . 
                                                " from IP " . $_SERVER["REMOTE_ADDR"]));

                    $_SESSION["errText"] = "Error updating transaction";
				}				
				break;

			case "delete":
            
                //remove transaction. confirmation of delete is handled by the client application.
				if ($account->removeTransaction($_POST["txnid"])) {	

                    t\Logger::log(new t\LogMessage("User " . $_SESSION["userobj"]->getUserId() . 
                                                   " deleted transaction " . $_POST["txnid"] . 
                                                   " from IP " . $_SERVER["REMOTE_ADDR"]));

                    header("location: /tcash/account?acc=" . $account->getName());	
					exit();
				
                } else {

                    t\Logger::log(new t\LogMessage("User " . $_SESSION["userobj"]->getUserId() . 
                                                " failed to delete transaction " . $_POST["txnid"] . 
                                                " from IP " . $_SERVER["REMOTE_ADDR"]));

                    $_SESSION["errText"] = "Error removing transaction:" . $_SESSION["errText"];
				}				
				break;

			default: 
                //if we got here then something freaky is going on.
				$_SESSION["errText"] = "Unrecognised operation";
				break;
		}

		//If we have dropped down to here without re-directing then something 
		//went wrong... show the error panel and bomb-out.
		$usePanel = 'common/tcash-panel-error.html.php';
		include '../../tcash-frame.html.php';
		exit();

    } else {
	


        //======================================================
        //DEFAULT CODE FOR JUST DISPLAYING THE PAGE AS CALLED
        //======================================================
        $selectedAcc = $_GET["acc"];
        $selectedTxn = (isset($_GET["txn"])) ? $_GET["txn"] : "new";
        $selectedMode = $_GET["mode"];
        $switchMode = (isset($_SESSION["tempTxn"]) ? "return" : $selectedMode);

        //Gather reference data for lookups
        $portfolio = new tc_portfolio($_SESSION["accountgroup"]);
        $acclist   = $portfolio->getAllAccounts();
        $payeelist = $portfolio->getPayeeList("P");
        $txflist   = db_getAccountList($_SESSION["accountgroup"], "%");
        $catlist   = $portfolio->getCategoryList();

        switch ($switchMode) {
            case "edit":
                //EDIT mode - pre-populate a transaction from database
                try {
                    $screenTxn = new tc_transaction($selectedTxn);
                    $usePanel = 'account/view/tcash-panel-account-view.html.php';
                } 
                catch (Exception $e) {
                    //call error panel
                    $usePanel = 'common/tcash-panel-error.html.php';
                }
                break;

            case "add":
                //ADD mode - create an empty transaction object for use in screen
                $screenTxn = new tc_transaction();

                //set default field values
                $screenTxn->setAccountId($selectedAcc);

                if (isset($_SESSION["prevdate"])) {
                    $screenTxn->setDate(date_format($_SESSION["prevdate"], 'Y-m-d'));
                } else {
                    $screenTxn->setDate(date("Y-m-d"));
                }

                //get list of placeholder transactions - only applies to ADD function
                $pholders = db_getAllPlaceholderForAcct($_SESSION["accountgroup"], $selectedAcc);
                            
                $usePanel = 'account/view/tcash-panel-account-view.html.php';
                break;

            case "return":
                //RETURN mode - use a copy of the tmpTxn object for the screen
                $screenTxn = unserialize($_SESSION["tempTxn"]);        
                unset($_SESSION["tempTxn"]);
                
                //get list of placeholder transactions - only applies to ADD function
                $pholders = db_getAllPlaceholderForAcct($_SESSION["accountgroup"], $selectedAcc);

                $usePanel = 'account/view/tcash-panel-account-view.html.php';
                break;			
        }


        //hand off to the HTML template file...
        include '../../tcash-frame.html.php';
    }


//======================================================
//FUNCTIONS
//======================================================

    //rebuild a transaction object based on the submitted form data
	function recreateScreenTxn() {

        //payee might come from one of two drop-down boxes, depending on transaction type
        $payee = ($_POST["txntype"] == "Transfer" ? $_POST["txfaccount"] : $_POST["payee"]);
        
        $newTrans = new tc_transaction($_POST["txnid"], 
                                  $_POST["date"], 
                                  $_POST["accountid"],
                                  $payee, 
                                  $_POST["category"], 
                                  $_POST["txnamount"],
                                  $_POST["notes"], 
                                  (isset($_POST["iscleared"]) ? 1 : 0), 
                                  (isset($_POST["isplaceholder"]) ? 1 : 0),
                                  null,
                                  $_POST["transfertxn"],
                                  true);
        
        $newTrans->setStateTxnType($_POST["txntype"]);
        
        return $newTrans;
	}
	
