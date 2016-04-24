<?php

	//link to all the usual include files: db, session, etc. 
    use \tcash as t;

    include_once $_SERVER['DOCUMENT_ROOT'] . '/tcash/common/tcash-config.inc.php';

	//get the most recent transaction details for the supplied payee
	$pholders = db_getAllPlaceholderForAcct($_SESSION["accountgroup"], $_POST["account"]);

	//turn the response data into an array
	if (isset($pholders)) {
		$pH[] = array('id' => 'opt-none', 'value' => '', 'text' => 'None');
        
        foreach ($pholders as $pholder) {
            $pH[] = array('id' => 'opt-' . $pholder["id"], 
                          'value' => $pholder["id"],
                          'text' => $pholder["payee"]);
        }

	} else {
		$pH[] = array('id' => 'opt-none', 'value' => '', 'text' => 'None');
	}

	//write the array back to the standard output device
	echo json_encode($pH);

?>

