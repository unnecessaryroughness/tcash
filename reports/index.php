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

    if (isset($_POST["action"]) && $_POST["action"] == "run") {

        $dbc = t\Database::connect($_SESSION["envname"]);

        //store the search parameters in the session
        $_SESSION["searchParms"] = array("type" => $_POST["rdoReportType"],
                                         "sdate" => $_POST["startdate"],
                                         "edate" => $_POST["enddate"],
                                         "accs" => $_POST["acclist"],
                                         "cats" => $_POST["catlist"]);
        
        //remove # marks from acclist and catlist
        $acclist = str_replace("#", "", $_POST["acclist"]);
        $catlist = str_replace("#", "", $_POST["catlist"]);
        
        //convert the parameter name into a stored procedure name
        switch ($_POST["rdoReportType"]) {
            case "SpendCat":
            
/*
                t\Logger::log(new t\LogMessage("attempting to run report with parameters:", 
                                               $_SESSION["userobj"]->getAccountGroup() . "," . 
                                                  $_POST["startdate"] . "," . $_POST["enddate"] 
                                               . "," . $acclist));
*/

                //Run the report 
                try {
                    $rptResults = t\Reports::runReport_SpendCat($dbc, 
                                                  $_SESSION["userobj"]->getAccountGroup(), 
                                                  $_POST["startdate"], $_POST["enddate"], 
                                                  $acclist);
                } 
                catch (TCASHException $e) {
                    $_SESSION["errText"] = $e->displayOutput();
                    $usePanel = 'common/tcash-panel-error.html.php';
                }
            
                //Set the screenDisplay object
                if ($rptResults) {
                    $reportDisplay = new t\ReportDisplay();
                    $reportDisplay->screenTitle = "Spending by Category Report";
                    $reportDisplay->returnURL   = "/tcash/reports";
                    $reportDisplay->screenData  = $rptResults;
                    $reportDisplay->addScreenField("category", "Category")
                                  ->addScreenField("total_spend", "Total Spend", 
                                                   "report-result-rightalign");
                    $reportDisplay->addScreenTotal("total_spend", "sum");
                    $reportDisplay->addChart("bar", "category", "total_spend");
                    $usePanel = "reports/tcash-panel-reports-result.html.php";
                } else {
                    $_SESSION["errText"] = "Report results were empty";
                    unset($usePanel);
                }
                break;
            
            case "SpendPayee":
            
                //Run the report 
                try {
                    $rptResults = t\Reports::runReport_SpendPayee($dbc, 
                                                  $_SESSION["userobj"]->getAccountGroup(), 
                                                  $_POST["startdate"], $_POST["enddate"], 
                                                  $acclist);
                } 
                catch (TCASHException $e) {
                    $_SESSION["errText"] = $e->displayOutput();
                    $usePanel = 'common/tcash-panel-error.html.php';
                }
            
                //Set the screenDisplay object
                if ($rptResults) {
                    $reportDisplay = new t\ReportDisplay();
                    $reportDisplay->screenTitle = "Spending by Payee Report";
                    $reportDisplay->returnURL   = "/tcash/reports";
                    $reportDisplay->screenData  = $rptResults;
                    $reportDisplay->addScreenField("payee", "Payee")
                                  ->addScreenField("total_spend", "Total Spend", 
                                                   "report-result-rightalign");
                    $reportDisplay->addScreenTotal("total_spend", "sum");
                    $reportDisplay->addChart("bar", "payee", "total_spend");

                    $usePanel = "reports/tcash-panel-reports-result.html.php";
                } else {
                    $_SESSION["errText"] = "Report results were empty";
                    unset($usePanel);
                }
                break;
            
            case "TransByCat":
            
                //Run the report 
                try {
                    $rptResults = t\Reports::runReport_TransByCat($dbc, 
                                                  $_SESSION["userobj"]->getAccountGroup(), 
                                                  $_POST["startdate"], $_POST["enddate"], 
                                                  $acclist, $catlist);
                } 
                catch (TCASHException $e) {
                    $_SESSION["errText"] = $e->displayOutput();
                    $usePanel = 'common/tcash-panel-error.html.php';
                }

                //Set the screenDisplay object
                if ($rptResults) {
                    $reportDisplay = new t\ReportDisplay();
                    $reportDisplay->screenTitle = "Transactions By Category Report";
                    $reportDisplay->returnURL   = "/tcash/reports";
                    $reportDisplay->screenData  = $rptResults;
                    $reportDisplay->addScreenField("date", "Date")
                                  ->addScreenField("accountid", "Account Id") 
                                  ->addScreenField("payee", "Payee")
                                  ->addScreenField("category", "Category")
                                  ->addScreenField("amountcr", "Amount",                         
                                                   "report-result-rightalign");
                    $reportDisplay->addScreenTotal("amountcr", "sum");
                    $reportDisplay->addChart("bar", "category", "amountcr");

                    $usePanel = "reports/tcash-panel-reports-result.html.php";
                } else {
                    $_SESSION["errText"] = "Report results were empty";
                    unset($usePanel);
                }
            
                break;
            
            default: 
                //If we have dropped down to here without re-directing then something 
                //went wrong... show the error panel and bomb-out.
                $usePanel = 'common/tcash-panel-error.html.php';
        }
    } 

    if (!isset($usePanel)) {

        //======================================================
        //DEFAULT CODE FOR JUST DISPLAYING THE PAGE AS CALLED
        //======================================================

        //set the lookup lists for display on the screen
        $scrAccList = t\Portfolio::getAccountList($_SESSION["accountgroup"]);
        $scrCatList = t\Portfolio::getCategoryList($_SESSION["accountgroup"]);

        //retrieve the previous search parameters from session, or construct
        //a set of defaul parameters if none found in session
        if (isset($_SESSION["searchParms"])) {
            $scrParms = $_SESSION["searchParms"];
        } else {
            $scrParms = array("type" => "SpendCat",
                             "sdate" => date("Y-m-d", strtotime("-1 month")),
                             "edate" => date("Y-m-d"),
                             "accs" => "",
                             "cats" => "");
        }
                
        $usePanel = 'reports/tcash-panel-reports-menu.html.php';
    }

	//hand off to the HTML template file...
	include '../tcash-frame.html.php';

