<?php

/*
    ======================================================================
    tcash-repeating-txns.inc.php
    ======================================================================

    PARAMETERS: fn = function to call
                     inc 	- increment to next due date
                     asng	- apply a single transaction 
                     aday	- apply a single day of transactions
                     a2d	- apply all transactions up to a given date
                tx = transaction to operate upon
                dt = date-to

    ======================================================================
*/


function applyRepeating($fn, $tx, $dt="unset") {

    $fn = (isset($fn) ? $fn : "unset");
    $tx = (isset($tx) ? $tx : "unset");
    $dt = (isset($dt) ? $dt : "unset");

    $scrOUT = "";
    $retVAL = false;

    switch ($fn) {

        //increment next date function
        case "inc":			

            //Get repeating transaction requested
            $rec = getRepeatingTxn($tx);

            if ($rec) {
                //Get next incremented date
                $ndt = incrementDate($rec, $dt);

                $scrOUT .= "\nrecord: " . $rec["payee"] . "\t" . $rec["amountcr"] . "\t" . 
                     $rec["nextdate"] . "\t" . $rec["nextdate"] . "\n";	

                $scrOUT .= "next date: " . $ndt . "\n\n";	
                $retVAL = true;
            } else {
                $scrOUT .= "\nfailed to retrieve repeating transaction from database.\n\n";
                $retVAL = false;
            }

            break;


        //apply a single transaction
        case "asng":

            //Get repeating transaction requested
            $accountgroup = getAccountGroup();
            $rec = db_getRepeating($tx, $accountgroup);
            $scrOUT .= "id: " . $rec["id"];

            if (isset($rec)){

                $scrOUT .= "\n\napplying single transaction " . $rec["payee"] . " " . 
                            $rec["amountcr"] . "\n\n";

                //apply transaction
                if (applySingle($rec, $dt)) {

                    //Update database with next incremented date    
                    $nd = updateNextDate($rec, $dt); 
                    if ($nd) {
                        $retVAL = true; 
                    } else {
                        $retVAL = false; 
                    }
                }
            } else {
               $scrOUT .= "\n\nfailed to retrieve repeating transaction\n\n";
            }
            break;


        //apply a whole day of transactions
        case "aday":

            //Get all repeating transactions for a particular day
            $accountgroup = getAccountGroup();
            $repeatsForDay = db_getRepeatingDay($dt, $accountgroup);  

            $scrOUT .= "\n\nRepeats found [" . $accountgroup . "]: " . sizeof($repeatsForDay) . "\n\n";

            //Cycle the transactions applying them all in turn
            foreach ($repeatsForDay as $repeat) {
                if (applySingle($repeat)) {
                    if (updateNextDate($repeat)) {
                        $retVAL = true;
                    } else {
                        $retVAL = false;
                        break;
                    }
                } else {
                    $retVAL = false;
                    break;
                }
            }

            break;

        //apply to a date
        case "a2d":

            //Get all repeating transactions up to a particular date
            //need to call this in a loop until the number returned is 0
            //because we might have daily or weekly txns that reset to a
            //date less than the target date after they are applied.
            $accountgroup = getAccountGroup();

            do {
                //get repeats up to target date
                $repeatsList = db_getRepeatingToDate($dt, $accountgroup);  
                $scrOUT .= "\n\nRepeats found to " . $dt . " [" . $accountgroup . "]: " . 
                    sizeof($repeatsList) . "\n\n";

                
                //Cycle the found transactions applying them all in turn
                foreach ($repeatsList as $repeat) {
                    if (applySingle($repeat)) {
                        if (updateNextDate($repeat)) {
                            $retVAL = true;
                        } else {
                            $retVAL = false;
                            break 2;
                        }
                    } else {
                        $retVAL = false;
                        break 2;
                    }
                }        
            } while (sizeof($repeatsList) > 0);

            break;
    }

    //Only write output to the screen if we are running from the command line
    if (isset($argv)) {
        echo $scrOUT;
    }

    return $retVAL;
}



/*
    ======================================================================
    FUNCTIONS
    ======================================================================
*/


function getAccountGroup() {
    return (isset($_SESSION["accountgroup"]) ? $_SESSION["accountgroup"] : "TONKS");
}


function incrementDate($rec, $t_dt = null) {
  
    if (isset($t_dt) && $t_dt != "unset") {
        $pdt = new DateTime($t_dt);
    } else {
        $pdt = new DateTime($rec["prevdate"]);
    }
    
    //if record was found, determine the increment basis
    if (isset($rec)) {
        $ndt = $pdt->add(new DateInterval("P" . $rec["frequencyincrement"] . $rec["frequencycode"]));         
        return $ndt->format('Y-m-d');
    } else {
       return null; 
    }				
}


function applySingle($t_rec, $t_dt =null) {
    
    //t_dt is an optional parameter - if null, use the next date from the record
    $dateToApply = (isset($t_dt) && $t_dt != "unset" ? $t_dt : $t_rec["nextdate"]);
  
    //Need to determine if this is a transfer transaction, so we can call a different DB-SP
    if (substr($t_rec["payee"], 0, 1) == "<") {
        $txRtn = db_transferTxn($dateToApply, 
                  $t_rec["accountid"],
                  $t_rec["payee"],
                  $t_rec["category"],
                  $t_rec["amountcr"],
                  $t_rec["notes"],    
                  false,
                  $t_rec["isplaceholder"],
                  getAccountGroup());
    } else {
        $txRtn = db_addTxn($dateToApply, 
                  $t_rec["accountid"],
                  $t_rec["payee"],
                  $t_rec["category"],
                  $t_rec["amountcr"],
                  $t_rec["notes"],    
                  false,
                  $t_rec["isplaceholder"],
                  getAccountGroup());
    }
    
    //Use db_addTxn to add the new transaction based on the repeating details
    //($dt, $ac, $py, $ca, $am, $nt, $ic, $ip)
    //if successfully added, return TRUE, otherwise return FALSE
    return $txRtn;
}


function updateNextDate($t_rec, $t_dt =null) {
      
    //t_dt is an optional parameter - if null, use the next date from the record
    $prevdateToApply = (isset($t_dt) && $t_dt != "unset" ? $t_dt : $t_rec["nextdate"]);
  
    //Get next incremented date
    $dateToApply = incrementDate($t_rec, $prevdateToApply);
  
    //Get the account group name
    $accountgroup = getAccountGroup();

    if (db_updateRepeatingNextDate($t_rec["id"], $dateToApply, $prevdateToApply, $accountgroup)){
        return $dateToApply;
    } else {
        return false;
    }
}


