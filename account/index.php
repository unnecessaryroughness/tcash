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


	


	//======================================================
	//DEFAULT CODE FOR JUST DISPLAYING THE PAGE AS CALLED
	//======================================================
	$selectedAcc = $_GET["acc"];

    //Gather reference data for lookups
    $portfolio = new tc_portfolio($_SESSION["accountgroup"]);
    $acclist   = $portfolio->getAllAccounts();


	//convert results into account/transaction objects
	try {
		$account = new tc_account($selectedAcc, $_SESSION["accountgroup"]);
		$account->populateTransactions();
        $todaydata = $account->getTodaysBalance(); 
        $todaybal = $todaydata["balance"];
        $todaytxn = $todaydata["id"];
        $usePanel = 'account/tcash-panel-account.html.php';
	} 
	catch (Exception $e) {
		//call error panel
		$usePanel = 'common/tcash-panel-error.html.php';
	}

	//hand off to the HTML template file...
	include '../tcash-frame.html.php';

	
