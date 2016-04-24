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

    if (isset($_GET["aptd"]) && $_GET["aptd"] != "") {
       
        //apply to date
        include $_SERVER["DOCUMENT_ROOT"] . "/tcash/repeating/tcash-repeating-txns.inc.php";

        if (applyRepeating("a2d", 0, $_GET["aptd"])) {	
            header("location: /tcash/repeating/");	
            exit();
        } else {
            $_SESSION["errText"] = "Error deleting repeating transaction";
        }				
        
    }


	//======================================================
	//DEFAULT CODE FOR JUST DISPLAYING THE PAGE AS CALLED
	//======================================================

	
	//convert results into account/transaction objects
	try {

        $screenRegister = new tc_repeatRegister($_SESSION["accountgroup"]);
        $screenRegister->populateTransactions();
            
        $usePanel = 'repeating/tcash-panel-repeating-display.html.php';
	} 
	catch (Exception $e) {
		//call error panel
		$usePanel = 'common/tcash-panel-error.html.php';
	}


	//hand off to the HTML template file...
	include '../tcash-frame.html.php';

