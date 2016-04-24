<?php


/*
	================================================================================
	TODO: 

	================================================================================
*/

    use \tcash as t;

    try {
        include_once $_SERVER['DOCUMENT_ROOT'] . '/tcash/common/tcash-config.inc.php';
    } 
    catch (t\TCASHException $e) {            
        $_SESSION["errText"] = $e->displayOutput();
        $usePanel = 'common/tcash-panel-error.html.php';
        include '../tcash-frame.html.php';
        exit();
    }

	//===========================================
	//HANDLE RESPONSE VALUES FROM THIS PAGE, 
	//INCLUDING THE MAIN MENU
	//===========================================

    if (isset($_POST["action"]) && $_POST["action"] == "add") {

        $account = new tc_account($_POST["accountid"], $_SESSION["accountgroup"]);
        $amountcr = -$_POST["txnamount"];
        
        if ($account->addNewTransaction($_POST["date"], $_POST["accountid"], $_POST["payee"], 
                                        $_POST["category"], $amountcr, "", 0, 0)) {	

            if (isset($_POST["reduce"]) && $_POST["reduce"] != "") {

                if ($account->adjustTransactionValue($_POST["accountid"], 
                                                     $_POST["reduce"], 
                                                     $_POST["reduceamt"],
                                                     $_SESSION["accountgroup"])) {

                    header("location: /tcash/account?acc=" . $_POST["accountid"]);	
                    exit();
                } else {
                    $_SESSION["errText"] = "Error reducing placeholder: " . $_SESSION["errText"];
                }
            } else {
                header("location: /tcash/account?acc=" . $_POST["accountid"]);	
                exit();
            }
        } else {
            $_SESSION["errText"] = "Error adding transaction";
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
        $currentaccs = db_getAccountList($_SESSION["accountgroup"], "CURRENTACC");
        $acclist = [];
        
        foreach ($currentaccs as $currentacc) {
            $acclist[] = new tc_account($currentacc["id"], 
                                        $currentacc["accountgroup"], 
                                        $currentacc["name"], 
                                        null,
                                        $currentacc["type"]);
        }
        
        $portfolio = new tc_portfolio($_SESSION["accountgroup"]);
        $payeelist = $portfolio->getPayeeList("P");
        $catlist = $portfolio->getCategoryList();

        //create an empty transaction object for use in screen
        $screenTxn = new tc_transaction();
        $tomoz = new DateTime('tomorrow');
        $tomozStr = $tomoz->format("Y-m-d");
        $screenTxn ->setDate($tomozStr);
        
        $usePanel = 'receipt/tcash-panel-receipt-view.html.php';
	} 
	catch (Exception $e) {
		//call error panel
		$usePanel = 'common/tcash-panel-error.html.php';
	}


	//hand off to the HTML template file...
	include '../tcash-frame.html.php';

