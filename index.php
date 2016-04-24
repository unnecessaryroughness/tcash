<?php

/*
	================================================================================
	TODO: 
            * admin functions   
                - Remove an account (with group password confirm)
                - Update an account (with group password confirm)
                - Show / Hide an account
                - Change a group password
                - administer lookup data lists 
            * mass update of transactions to an alternative date
            * compare this month's spending to last month's
            * budgets
            * show "days until payday" and "weekends until payday" on front screen
                - record payday day of the month against each user
                - include simple calculation & display in balance panel
            * refactoring
			     - tidy up which data functions are methods of class & which are called direct
                 - ACCOUNTGROUP isn't used consistently everywhere:
                    - stored procedures
                        * db_getTxn
                 - Some security issues around changing transactions from other accgroups
                 - "remember me" box, to allow single sessions
                 - tidy up CSS structure
                    - border-box sizing everywhere
                    
    TO UPLOAD TO PRODUCTION MYSQL:
            
    KNOWN BUGS:
            * (none)
    ================================================================================
*/

    use \tcash as t;

    try {
        include_once $_SERVER['DOCUMENT_ROOT'] . '/tcash/common/tcash-config.inc.php';
    } 
    catch (t\TCASHException $e) {            
        $_SESSION["errText"] = $e->displayOutput() . $e . "Startup Error";
        $usePanel = 'common/tcash-panel-error.html.php';
        include 'tcash-frame.html.php';
        exit();
    }


	//===========================================
	//HANDLE RESPONSE VALUES FROM THIS PAGE, 
	//INCLUDING THE MAIN MENU
	//===========================================

    //Has user requested to switch account group?
    if (isset($_POST["main_user_acgroup_select"]) && $_POST["main_user_acgroup_select"] != "") {

        //get the userobj from session and switch account group
        try {
            $_SESSION["userobj"]->switchAccountGroup($_POST["main_user_acgroup_select"]); 
            $_SESSION["accountgroup"] = $_SESSION["userobj"]->getAccountGroup(); 
        } 
        catch (t\TCASHException $e) {            
            $_SESSION["accountgroup"] = "ERROR"; 
            $_SESSION["errText"] = $e->displayOutput();
            $usePanel = 'common/tcash-panel-error.html.php';
            include 'tcash-frame.html.php';
            exit();
        }
        
    } 

    //======================================================
    //DEFAULT CODE FOR JUST DISPLAYING THE PAGE AS CALLED
    //======================================================

    //this is the home page; get balance data and include the "balances" panel
    $portfolio = new tc_portfolio($_SESSION["accountgroup"]);

    if (isset($portfolio)) {
        $usePanel = 'balances/tcash-panel-balances.html.php';
    } else {
        //call error panel
        $_SESSION["errText"] = "Could not set portfolio";
        $usePanel = 'common/tcash-panel-error.html.php';
        exit();
    }


	//hand off to the HTML template file...
	include 'tcash-frame.html.php';

  
