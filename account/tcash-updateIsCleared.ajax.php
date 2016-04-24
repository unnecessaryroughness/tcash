<?php

	//link to all the usual include files: db, session, etc. 
    use \tcash as t;

    try {
        include_once $_SERVER['DOCUMENT_ROOT'] . '/tcash/common/tcash-config.inc.php';
    } 
    catch (t\TCASHException $e) {            
        $_SESSION["errText"] = $e->displayOutput();
        $usePanel = 'common/tcash-panel-error.html.php';
        include 'tcash-frame.html.php';
        exit();
    }

	//flip the "isCleared" flag for the requested transaction
	$rtnVal = db_updateIsCleared($_POST["txnId"], $_SESSION["accountgroup"], $_POST["isCleared"]);

	//construct the response data array
	if (isset($rtnVal) and $rtnVal) {
		$outVal = array("isCleared" => $_POST["isCleared"], 
						"txnId" => $_POST["txnId"],
						"isFutureDated" => $_POST["isFutureDated"],
                        "isPlaceholder" => $_POST["isPlaceholder"],
                        "isToday" => $_POST["isToday"]);
	} else {
		$outVal = array("isCleared" => "-1", 
						"txnId" => "-1",
						"isFutureDated" => "-1",
                        "isPlaceholder" => "-1",
                        "isToday" => "-1");
	}

	//write the array back to the standard output device
	echo json_encode($outVal);

?>

