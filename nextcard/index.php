<?php


/*
	================================================================================
	TODO: 

	================================================================================
*/

	include_once $_SERVER['DOCUMENT_ROOT'] . '/tcash/common/tcash-config.inc.php';

	//===========================================
	//HANDLE RESPONSE VALUES FROM THIS PAGE, 
	//INCLUDING THE MAIN MENU
	//===========================================

    if (isset($_POST["action"])) {
        
        if ($_POST["action"] == "receipt") {

            $account = new tc_account($_POST["accountid"], $_SESSION["accountgroup"]);
            $amountcr = -$_POST["arrivedamount"];

            if ($account->addNewTransaction($_POST["date"], $_POST["accountid"], $_POST["payee"], 
                                            $_POST["category"], $amountcr, $_POST["receiptnotes"],
                                            0, 0)) {	

                header("location: /tcash/account?acc=" . $_POST["accountid"]);	
                exit();
            } else {
                $_SESSION["errText"] = "Error adding transaction";
            }				

        } elseif ($_POST["action"] == "return") {

            $account = new tc_account($_POST["accountid"], $_SESSION["accountgroup"]);
            $amountcr = $_POST["returnedamount"];
            
            if ($account->addNewTransaction($_POST["date"], $_POST["rtnaccountid"], $_POST["payee"], 
                                            $_POST["category"], $amountcr, $_POST["returnnotes"],
                                            0, 0)) {	

                header("location: /tcash/account?acc=" . $_POST["rtnaccountid"]);	
                exit();
            } else {
                $_SESSION["errText"] = "Error adding transaction";
            }				
            
        }

		//If we have dropped down to here without re-directing then something 
		//went wrong... show the error panel and bomb-out.
		$usePanel = 'common/tcash-panel-error.html.php';
		include '../tcash-frame.html.php';
		exit();        
    }  



	//======================================================
	//DEFAULT CODE FOR JUST DISPLAYING THE PAGE AS CALLED
	//======================================================

	
	//convert results into account/transaction objects
	try {

        //Gather reference data for lookups
        //only use current accounts for quick receipts
        $nextaccs = db_getAccountTypeBank($_SESSION["accountgroup"], "STORECARD", "NEXT");
        //$acclist = [];
        
        foreach ($nextaccs as $nextacc) {
            $acclist[] = new tc_account($nextacc["id"], 
                                        $nextacc["accountgroup"], 
                                        $nextacc["name"], 
                                        null,
                                        $nextacc["type"]);
        }
        
        //create an empty transaction object for use in screen
        $screenTxn = new tc_transaction();
        $todaydt = new DateTime('today');
        $todaydtStr = $todaydt->format("Y-m-d");
        $screenTxn->setDate($todaydtStr);
        
        //set other default values
        $screenTxn->setPayee('Next');
        $screenTxn->setCategory('Clothing');
        $screenTxn->setAccountId((isset($acclist)) ? $acclist[0] : null);
        
        $usePanel = 'nextcard/tcash-panel-next-view.html.php';
	} 
	catch (Exception $e) {
		//call error panel
		$usePanel = 'common/tcash-panel-error.html.php';
	}


	//hand off to the HTML template file...
	include '../tcash-frame.html.php';

