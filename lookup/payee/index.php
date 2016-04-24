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
        include '../../tcash-frame.html.php';
        exit();
    }

	//===========================================
	//HANDLE RESPONSE VALUES FROM THIS PAGE, 
	//INCLUDING THE MAIN MENU
	//===========================================

	if (isset($_POST["action"])) {
		
		switch ($_POST["action"]) {
		
			case "update": 
	
				//handle updating a payee
				if (db_updatePayee($_POST["oldpayee"], $_SESSION["accountgroup"], $_POST["newpayee"])) {

					//unserialize temp transaction from session, change payee, reserialize into session
					$txn = unserialize($_SESSION["tempTxn"]);
					$txn->setPayee($_POST["newpayee"]);
					$_SESSION["tempTxn"] = serialize($txn);

					//redirect back to where we came from			
					header("location: " . $_SESSION["returnURL"]);
					unset($_SESSION["returnURL"]);				
					exit();
				}

				break;

			case "create":
	
				//handle updating a payee
				//handle updating a payee
				if (db_addPayee($_POST["newpayee"], $_SESSION["accountgroup"])) {

					//unserialize temp transaction from session, change payee, reserialize into session
					$txn = unserialize($_SESSION["tempTxn"]);
					$txn->setPayee($_POST["newpayee"]);
					$_SESSION["tempTxn"] = serialize($txn);

					//redirect back to where we came from			
					header("location: " . $_SESSION["returnURL"]);
					unset($_SESSION["returnURL"]);				
					exit();
				}

				break;

			default: 
				$_SESSION["errText"] = "Unrecognised operation";
				break;
		}

		//If we have dropped down to here without re-directing then something 
		//went wrong... show the error panel and bomb-out.
		unset($_SESSION["returnURL"]);				
		unset($_SESSION["tempTxn"]);				
		$usePanel = 'common/tcash-panel-error.html.php';
		include '../../tcash-frame.html.php';
		exit();
	}
	

	

	//======================================================
	//DEFAULT CODE FOR JUST DISPLAYING THE PAGE AS CALLED
	//======================================================
	$screenMode = $_GET["mode"];
	
	if ($screenMode == "edit") {
		$screenPayee = db_getPayee($_GET["py"], $_SESSION["accountgroup"]);
	} else {
		$screenPayee = array("payee" => "");
	}

	if (isset($screenPayee)) {
		$usePanel = 'lookup/payee/tcash-panel-view-payee.html.php';		
	} else {
		$usePanel = 'common/tcash-panel-error.html.php';
	}


	//hand off to the HTML template file...
	include '../../tcash-frame.html.php';

	
