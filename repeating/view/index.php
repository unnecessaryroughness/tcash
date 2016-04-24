<?php

/*
    ======================================================================
    index.php  (repeating/view)
    ======================================================================

    PARAMETERS: mode = mode to use
                     add 	- add a new repeating transaction
                     edit	- edit an existing repeating transaction
                tx = transaction to operate upon

    ======================================================================
*/


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
        include '../../tcash-frame.html.php';
        exit();
    }

	//===========================================
	//HANDLE RESPONSE VALUES FROM THIS PAGE, 
	//INCLUDING THE MAIN MENU
	//===========================================

    if (isset($_POST["refdata"]) && $_POST["refdata"] != "") {

        //convert the unsigned amount into a signed amount, depending on the transaction type
        if ($_POST["txntype"] == "Deposit") { $amountcr = $_POST["txnamount"]; }
        if ($_POST["txntype"] == "Payment") { $amountcr = -$_POST["txnamount"]; }
        if ($_POST["txntype"] == "Transfer") { $amountcr = -$_POST["txnamount"]; }

        switch ($_POST["refdata"]) {
            case "addpayee":
                $_SESSION["returnURL"] = $_POST["returnURL"];
                $_SESSION["tempTxn"] = serialize(recreateScreenTxn($amountcr));	
                header("location: /tcash/lookup/payee/?mode=add");
                exit();
                break;

            case "addcategory":
                $_SESSION["returnURL"] = $_POST["returnURL"];
                $_SESSION["tempTxn"] = serialize(recreateScreenTxn($amountcr));

                header("location: /tcash/lookup/category/?mode=add");
                exit();
                break;

            default:
                break;
        }

    } elseif (isset($_POST["action"])) {

		//convert the unsigned amount into a signed amount, depending on the transaction type
		if ($_POST["txntype"] == "Deposit") { $amountcr = $_POST["txnamount"]; }
		if ($_POST["txntype"] == "Payment") { $amountcr = -$_POST["txnamount"]; }
		if ($_POST["txntype"] == "Transfer") { $amountcr = -$_POST["txnamount"]; }

        //grab the appropriate payee
        $payee = ($_POST["txntype"] == "Transfer" ? $_POST["txfaccount"] : $_POST["payee"]);

		switch ($_POST["action"]) {
        
			case "add": 
                if (db_addRepeating($_POST["accountid"],
                                    $payee,
                                    $_POST["category"],
                                    $amountcr,
                                    $_POST["notes"],
                                    (isset($_POST["isplaceholder"]) ? 1 : 0),
                                    $_POST["nextdate"],
                                    $_POST["endondate"],
                                    $_POST["frequency"],
                                    $_POST["every"],
                                    $_SESSION["accountgroup"]
                                    )) 
                {	
                    header("location: /tcash/repeating/");	
                    exit();
                } else {
                    $_SESSION["errText"] = "Error adding repeating transaction ";
                }				
                
				break;
			
			case "update":	
                if (db_updateRepeating( $_POST["txnid"],
                                        $_POST["accountid"],
                                        $payee,
                                        $_POST["category"],
                                        $amountcr,
                                        $_POST["notes"],
                                        (isset($_POST["isplaceholder"]) ? 1 : 0),
                                        $_POST["nextdate"],
                                        $_POST["endondate"],
                                        $_POST["frequency"],
                                        $_POST["every"],
                                        $_SESSION["accountgroup"]
                                        )) 
                {	
                    header("location: /tcash/repeating/");	
                    exit();
                } else {
                    $_SESSION["errText"] = "Error updating repeating transaction ";
                }				

				break;

			case "delete":
                if (db_removeRepeating($_POST["txnid"], $_SESSION["accountgroup"])) {	
                    header("location: /tcash/repeating/");	
                    exit();
                } else {
                    $_SESSION["errText"] = "Error deleting repeating transaction";
                }				
				break;

			case "apply":
                include $_SERVER["DOCUMENT_ROOT"] . "/tcash/repeating/tcash-repeating-txns.inc.php";
                
                if (applyRepeating("asng", $_POST["txnid"])) {	
                    header("location: /tcash/repeating/");	
                    exit();
                } else {
                    $_SESSION["errText"] = "Error deleting repeating transaction";
                }				
				break;
            
			default: 
				$_SESSION["errText"] = "Unrecognised operation";
				break;
		}

        //If we have dropped down to here without re-directing then something 
		//went wrong... show the error panel and bomb-out.
		$usePanel = 'common/tcash-panel-error.html.php';
		include '../../tcash-frame.html.php';
		exit();    
    }

	//======================================================
	//DEFAULT CODE FOR JUST DISPLAYING THE PAGE AS CALLED
	//======================================================

    //get parameters passed to the screen
    $screenMode = $_GET["mode"];
    $screenTxId = (isset($_GET["tx"]) ? $_GET["tx"] : "-1");

	$switchMode = (isset($_SESSION["tempTxn"]) ? "return" : $screenMode);

    //Get all lookup data
    $portfolio = new tc_portfolio($_SESSION["accountgroup"]);
	$acclist = $portfolio->getAllAccounts();
	$payeelist = $portfolio->getPayeeList("P");
    $txflist = $portfolio->getPayeeList("A");
	$catlist = $portfolio->getCategoryList();


	//convert results into account/transaction objects
	switch ($switchMode) {
		case "edit":
            try {
                $screenTxn = new tc_repeat($screenTxId, $_SESSION["accountgroup"]);
                $usePanel = 'repeating/view/tcash-panel-repeating-view.html.php';
            } 
            catch (Exception $e) {
                //call error panel
                $usePanel = 'common/tcash-panel-error.html.php';
            }
        break;

		case "add":
            try {
                $screenTxn = new tc_repeat(-1, $_SESSION["accountgroup"]);
                $usePanel = 'repeating/view/tcash-panel-repeating-view.html.php';
            } 
            catch (Exception $e) {
                //call error panel
                $usePanel = 'common/tcash-panel-error.html.php';
            }
        break;

		case "return":
			//RETURN mode - use a copy of the tmpTxn object for the screen
			$screenTxn = unserialize($_SESSION["tempTxn"]);
            unset($_SESSION["tempTxn"]);
            $usePanel = 'repeating/view/tcash-panel-repeating-view.html.php';
        break;			
	}

	//hand off to the HTML template file...
	include '../../tcash-frame.html.php';

	function recreateScreenTxn($amountcr) {
		return new tc_repeat($_POST["txnid"], $_SESSION["accountgroup"], $_POST["accountid"],
					$_POST["payee"], $_POST["category"], $amountcr,
					$_POST["notes"], (isset($_POST["isplaceholder"]) ? 1 : 0),
                    $_POST["nextdate"], null, $_POST["endondate"], 
                    $_POST["frequency"], $_POST["every"], 
                    true);
	}
