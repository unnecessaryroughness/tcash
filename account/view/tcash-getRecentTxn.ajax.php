<?php

	//link to all the usual include files: db, session, etc. 
    use \tcash as t;

    include_once $_SERVER['DOCUMENT_ROOT'] . '/tcash/common/tcash-config.inc.php';

	//get the most recent transaction details for the supplied payee
	$recentDets = db_getRecentTxnDets($_SESSION["accountgroup"], $_POST["payee"]);

	//turn the response data into an array
	if (isset($recentDets)) {
		$retDets[] = array('category' => $recentDets["category"], 
								'amountcr' => $recentDets["amountcr"]);

	} else {
		$retDets[] = array('category' => '', 
								'amountcr' => '');
	}

	//write the array back to the standard output device
	echo json_encode($retDets);

?>

