<?php

/*	=======================================================================
	DRIVER PROGRAM FOR RUNNING ADHOC CODE SAMPLES. 
	NOTHING IN HERE IS PERMANENT. 
	======================================================================= */

session_start();

//autoload classes
function __autoload($classname) {
    $parts = explode("\\", strtolower($classname));

    require $_SERVER['DOCUMENT_ROOT'] . 
        'classes/' . end($parts) . '.php';
}


use \tcash as t;

$_SESSION["envname"]="[DEV]";

$pt = new t\Portfolio("MAYTEST");

print_r($pt->getAccountList("MAYTEST", "%"));


/*
try {
    $acc = new t\Account("TONKS", "HXSAM");
} catch (t\TCASHException $e) {
    echo "ERROR! " . $e->displayOutput();  
}

$acc->populateTransactions(2);
print_r ($acc->getTodaysBalance());

*/

//print_r($acc);
//printl($acc->getBalance());




/*
try {
    $txn = new t\Transaction("TONKSO", 6056);
} catch (t\TCASHException $e) {
    echo "ERROR! " . $e->displayOutput();  
}

print_r($txn);
*/


function printl($s) {
    echo $s . "\n";
}



/*
$reportDisplay = new t\ReportDisplay();
$reportDisplay->screenTitle = "Spending by Category Report";
$reportDisplay->returnURL   = "/tcash/reports";
$reportDisplay->screenData  = null;
$reportDisplay->addScreenField("category", "Category")
              ->addScreenField("total_spend", "Total Spend", 
                               "report-result-rightalign");
$reportDisplay->addScreenTotal("total_spend", "sum");


$reportDisplay->incrementTotal("total_spend", 100.00);
$reportDisplay->incrementTotal("total_spend", 50.00);

var_dump ($reportDisplay);
*/



/*
$rr = new tc_repeatRegister("TONKS");
$rr->populateTransactions();

foreach ($rr->getTransactions() as $r) {
    echo "NextDate:". $r->getNextDateFormatted() . "\t";
    echo "Payee:". $r->getPayee() . "\t";
    echo "Amount:". $r->getAmountCr() . "\t\n";
}
*/


//$txn = new tc_transaction(5836);
//var_dump($txn);
//echo $txn->getYear();


//convert results into account/transaction objects
//$account = new tc_account("FDMARK", db_getBalance("FDMARK")["balance"]);
/*
$account = new tc_account("FDMARK");
$account->populateTransactions(db_getAllTxnForAcct("FDMARK", 10));

echo "account balance: " . $account->getBalance() . "\n";

foreach ($account->getTransactions() as $tx) {
	echo $tx->getPayee() . ' : ' . $tx->getAmountCr() . ' : ' . $tx->getTxnBalance() . "\n";
}
*/


//var_dump($account);



/*
$a = new tc_account("test", 100.23);
$b = new tc_account("hello", 234.35);
$c = new tc_account("goodbye", 515.22);


var_dump ($a);
var_dump ($b);
var_dump ($c);


$p = new tc_portfolio();
$p->addAccount($a);
$p->addAccount($b);
$p->addAccount($c);
$ p->addAccount(new tc_account("inline", 999.99));


$f = $p->getAccount("goodbye");
var_dump ($f);
*/



