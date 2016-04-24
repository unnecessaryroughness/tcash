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
	
				//handle updating a category
				if (db_updateCategory($_POST["oldcategory"], $_SESSION["accountgroup"], $_POST["newcategory"])) {

					//unserialize temp transaction from session, change category, reserialize into session
					$txn = unserialize($_SESSION["tempTxn"]);
					$txn->setCategory($_POST["newcategory"]);
					$_SESSION["tempTxn"] = serialize($txn);

					//redirect back to where we came from			
					header("location: " . $_SESSION["returnURL"]);
					unset($_SESSION["returnURL"]);				
					exit();
				}

				break;

			case "create":
	
				//handle updating a category
				//handle updating a category
				if (db_addCategory($_POST["newcategory"], $_SESSION["accountgroup"])) {

					//unserialize temp transaction from session, change category, reserialize into session
					$txn = unserialize($_SESSION["tempTxn"]);
					$txn->setCategory($_POST["newcategory"]);
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
		$screenCategory = db_getCategory($_GET["ca"], $_SESSION["accountgroup"]);
	} else {
		$screenCategory = array("id" => "");
	}

	if (isset($screenCategory)) {
		$usePanel = 'lookup/category/tcash-panel-view-category.html.php';		
	} else {
		$usePanel = 'common/tcash-panel-error.html.php';
	}


	//hand off to the HTML template file...
	include '../../tcash-frame.html.php';

	
