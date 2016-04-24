<?php

function tcashConnect()
{
	if (!isset($_SESSION['devmode']) || $_SESSION['devmode'])
	{  
		$hostname = 'localhost';
		$dbname = 'tonksdev_tcash';
		$uname = 'tonksdev_tcashu';
		$pwd = 'LA:553425';
	}
	elseif (!$_SESSION["devmode"] && $_SESSION["envname"] == "[RASPI]") 
	{
		$hostname = 'raspberrypi';
		$dbname = 'tcash';
		$uname = 'root';
		$pwd = 'LA:553425';
    }
	elseif (!$_SESSION["devmode"] && $_SESSION["envname"] == "[RASPI2]") 
	{
		$hostname = 'raspi2';
		$dbname = 'tcash';
		$uname = 'root';
		$pwd = 'LA:553425';
    }
    else 
    {
		$hostname = '10.168.1.81';
		$dbname = 'tonksdev_tcash';
		$uname = 'tonksdev_tcashu';
		$pwd = 'LA:553425';
	}

	try
	{
		$pdo = new PDO('mysql:host=' . $hostname . ';dbname=' . $dbname, $uname, $pwd);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$pdo->exec('SET NAMES "utf8"');
	}
	catch (PDOException $e)
	{
		throw new Exception ('Unable to connect to the database server (' . $hostname . '/' . $dbname . ')');
		exit();
	}

	return $pdo;
}


function db_getAllBalances($ag) {

	try {
		$pdo = tcashConnect();
		$sql = "CALL getAllBalances(:ag)"; 
		$qry = $pdo->prepare($sql);
		$qry->bindValue(":ag", $ag);
		$qry->execute();
		
		return $qry->fetchall(PDO::FETCH_ASSOC);
	} 
	catch (PDOException $e) {
		$_SESSION["errText"] = $e;		
		return null;
	} 
}


function db_getBalance($acct, $ag) {

	try {
		$pdo = tcashConnect();
		$sql = "CALL getBalance(:a, :ag)"; 
		$qry = $pdo->prepare($sql);
		$qry->bindValue(':a', $acct);
		$qry->bindValue(':ag', $ag);
		$qry->execute();
		
		return $qry->fetch(PDO::FETCH_ASSOC);
	} 
	catch (PDOException $e) {
		$_SESSION["errText"] = $e;		
		return null;
	} 
}


function db_getAllTxnForAcct($acct, $limit, $accgroup) {
	
	try {
			$pdo = tcashConnect();
			$sql = "CALL getAllTxnForAcct(:a, :l, :ag)";
			$qry = $pdo->prepare($sql);
			$qry->bindValue(":a", $acct);
			$qry->bindValue(":l", $limit);
			$qry->bindValue(":ag", $accgroup);
			$qry->execute();

			return $qry->fetchall(PDO::FETCH_ASSOC); 
	}
	catch (PDOException $e) {
		$_SESSION["errText"] = $e;
		return null;
	}

}


function db_addTxn ($dt, $ac, $py, $ca, $am, $nt, $ic, $ip, $ag) {
    
    try {
			$pdo = tcashConnect();
			$sql = "CALL addTxn(:dt, :ac, :py, :ca, :am, :nt, :ic, :ip, 0, :ag)";
			$qry = $pdo->prepare($sql);
			$qry->bindValue(":dt", $dt);
			$qry->bindValue(":ac", $ac);
			$qry->bindValue(":py", $py);
			$qry->bindValue(":ca", $ca);
			$qry->bindValue(":am", $am);
			$qry->bindValue(":nt", $nt);
			$qry->bindValue(":ic", $ic);
			$qry->bindValue(":ip", $ip);
			$qry->bindValue(":ag", $ag);
			$qry->execute();
			return true;
	}
	catch (PDOException $e) {
		$_SESSION["errText"] = $e;
		return false;
	}
}


function db_getTxn ($id) {

	try {
			$pdo = tcashConnect();
			$sql = "CALL getTxn(:a)";
			$qry = $pdo->prepare($sql);
			$qry->bindValue(":a", $id);
			$qry->execute();

			return $qry->fetch(PDO::FETCH_ASSOC); 
	}
	catch (PDOException $e) {
		$_SESSION["errText"] = $e;
		return null;
	}		
}


function db_updateTxn ($id, $dt, $ac, $py, $ca, $am, $nt, $ic, $ip, $ag) {
	
	try {
			$pdo = tcashConnect();
			$sql = "CALL updateTxn(:dt, :ac, :py, :ca, :am, :nt, :ic, :ip, :id, :ag)";
			$qry = $pdo->prepare($sql);
			$qry->bindValue(":id", $id);
			$qry->bindValue(":dt", $dt);
			$qry->bindValue(":ac", $ac);
			$qry->bindValue(":py", $py);
			$qry->bindValue(":ca", $ca);
			$qry->bindValue(":am", $am);
			$qry->bindValue(":nt", $nt);
			$qry->bindValue(":ic", $ic);
			$qry->bindValue(":ip", $ip);
			$qry->bindValue(":ag", $ag);
			$qry->execute();
			return true;
	}
	catch (PDOException $e) {
		$_SESSION["errText"] = $e;
		return false;
	}
}


function db_removeTxn ($id, $ag) {
	
	try {
			$pdo = tcashConnect();
			$sql = "CALL removeTxn(:id, :ag)";
			$qry = $pdo->prepare($sql);
			$qry->bindValue(":id", $id);
			$qry->bindValue(":ag", $ag);
			$qry->execute();
			return true;
	}
	catch (PDOException $e) {
		$_SESSION["errText"] = $e;
		return false;
	}
}


function db_getAccountList($ag, $at='%') {

	try {
		$pdo = tcashConnect();
		$sql = "CALL getAccountList(:ag, :at)"; 
		$qry = $pdo->prepare($sql);
		$qry->bindValue(":ag", $ag);
		$qry->bindValue(":at", $at);        
		$qry->execute();

		return $qry->fetchall(PDO::FETCH_ASSOC);
	} 
	catch (PDOException $e) {
		$_SESSION["errText"] = $e;		
		return null;
	} 
}


function db_getPayeeList($ag, $ty) {

	try {
		$pdo = tcashConnect();
		$sql = "CALL getPayeeList(:ag, :ty)"; 
		$qry = $pdo->prepare($sql);
		$qry->bindValue(":ag", $ag);
		$qry->bindValue(":ty", $ty);
		$qry->execute();
		
		return $qry->fetchall(PDO::FETCH_ASSOC);
	} 
	catch (PDOException $e) {
		$_SESSION["errText"] = $e;		
		return null;
	} 
}


function db_getPayee($py, $ag) {

	try {
		$pdo = tcashConnect();
		$sql = "CALL getPayee(:py, :ag)"; 
		$qry = $pdo->prepare($sql);
		$qry->bindValue(":py", $py);
		$qry->bindValue(":ag", $ag);
		$qry->execute();
		
		return $qry->fetch(PDO::FETCH_ASSOC);
	} 
	catch (PDOException $e) {
		$_SESSION["errText"] = $e;		
		return null;
	} 
}


function db_updatePayee($py, $ag, $npy) {

	try {
		$pdo = tcashConnect();
		$sql = "CALL updatePayee(:py, :ag, :npy)"; 
		$qry = $pdo->prepare($sql);
		$qry->bindValue(":py", $py);
		$qry->bindValue(":ag", $ag);
		$qry->bindValue(":npy", $npy);
		$qry->execute();
		return true;
	} 
	catch (PDOException $e) {
		$_SESSION["errText"] = $e;		
		return false;
	} 
}


function db_addPayee($npy, $ag) {

	try {
		$pdo = tcashConnect();
		$sql = "CALL addPayee(:npy, :ag)"; 
		$qry = $pdo->prepare($sql);
		$qry->bindValue(":npy", $npy);
		$qry->bindValue(":ag", $ag);
		$qry->execute();
		return true;
	} 
	catch (PDOException $e) {
		$_SESSION["errText"] = $e;		
		return false;
	} 
}


function db_getCategoryList($ag) {

	try {
		$pdo = tcashConnect();
		$sql = "CALL getCategoryList(:ag)"; 
		$qry = $pdo->prepare($sql);
		$qry->bindValue(":ag", $ag);
		$qry->execute();
		
		return $qry->fetchall(PDO::FETCH_ASSOC);
	} 
	catch (PDOException $e) {
		$_SESSION["errText"] = $e;		
		return null;
	} 
}


function db_getCategory($ca, $ag) {

	try {
		$pdo = tcashConnect();
		$sql = "CALL getCategory(:ca, :ag)"; 
		$qry = $pdo->prepare($sql);
		$qry->bindValue(":ca", $ca);
		$qry->bindValue(":ag", $ag);
		$qry->execute();
		
		return $qry->fetch(PDO::FETCH_ASSOC);
	} 
	catch (PDOException $e) {
		$_SESSION["errText"] = $e;		
		return null;
	} 
}


function db_updateCategory($ca, $ag, $nca) {

	try {
		$pdo = tcashConnect();
		$sql = "CALL updateCategory(:ca, :ag, :nca)"; 
		$qry = $pdo->prepare($sql);
		$qry->bindValue(":ca", $ca);
		$qry->bindValue(":ag", $ag);
		$qry->bindValue(":nca", $nca);
		$qry->execute();
		return true;
	} 
	catch (PDOException $e) {
		$_SESSION["errText"] = $e;		
		return false;
	} 
}


function db_addCategory($nca, $ag) {

	try {
		$pdo = tcashConnect();
		$sql = "CALL addCategory(:nca, :ag)"; 
		$qry = $pdo->prepare($sql);
		$qry->bindValue(":nca", $nca);
		$qry->bindValue(":ag", $ag);
		$qry->execute();
		return true;
	} 
	catch (PDOException $e) {
		$_SESSION["errText"] = $e;		
		return false;
	} 
}

function db_transferTxn ($dt, $ac, $py, $ca, $am, $nt, $ic, $ip, $ag) {
	
	try {
			$pdo = tcashConnect();
			$sql = "CALL transferTxn(:dt, :ac, :py, :ca, :am, :nt, :ic, :ip, :ag)";
			$qry = $pdo->prepare($sql);
			$qry->bindValue(":dt", $dt);
			$qry->bindValue(":ac", $ac);
			$qry->bindValue(":py", $py);
			$qry->bindValue(":ca", $ca);
			$qry->bindValue(":am", $am);
			$qry->bindValue(":nt", $nt);
			$qry->bindValue(":ic", $ic);
			$qry->bindValue(":ip", $ip);
			$qry->bindValue(":ag", $ag);
			$qry->execute();
			return true;
	}
	catch (PDOException $e) {
		$_SESSION["errText"] = $e;
		return false;
	}
}


function db_getRecentTxnDets($ag, $py) {
	
	try {
		$pdo = tcashConnect();
		$sql = "CALL getRecentTxnDets(:ag, :py)"; 
		$qry = $pdo->prepare($sql);
		$qry->bindValue(":ag", $ag);
		$qry->bindValue(":py", $py);
		$qry->execute();
		
		return $qry->fetch(PDO::FETCH_ASSOC);
	} 
	catch (PDOException $e) {
		$_SESSION["errText"] = $e;		
		return null;
	}
}


function db_updateIsCleared($id, $ag, $ic) {

	try {
		$pdo = tcashConnect();
		$sql = "CALL updateIsCleared(:id, :ag, :ic)"; 
		$qry = $pdo->prepare($sql);
		$qry->bindValue(":id", $id);
		$qry->bindValue(":ag", $ag);
		$qry->bindValue(":ic", $ic);
		$qry->execute();
		return true;
	} 
	catch (PDOException $e) {
		$_SESSION["errText"] = $e;		
		return false;
	} 
}


function db_getRepeating($id, $ag) {
	
	try {
		$pdo = tcashConnect();
		$sql = "CALL getRepeating(:id, :ag)"; 
		$qry = $pdo->prepare($sql);
		$qry->bindValue(":id", $id);
		$qry->bindValue(":ag", $ag);
		$qry->execute();

		return $qry->fetch(PDO::FETCH_ASSOC);
	} 
	catch (PDOException $e) {
		$_SESSION["errText"] = $e;		
		return null;
	}
}


function db_updateRepeatingNextDate($id, $nd, $pd, $ag) {
  
    try {
        $pdo = tcashConnect();
        $sql = "CALL updateRepeatingNextDate(:id, :nd, :pd, :ag)";
        $qry = $pdo->prepare($sql);
        $qry->bindValue(":id", $id);
        $qry->bindValue(":nd", $nd);
        $qry->bindValue(":pd", $pd);
        $qry->bindValue(":ag", $ag);
        $qry->execute();
        return true;
    } 
    catch (PDOException $e) {
        $_SESSION["errText"] = $e;
        return false;
    }
}



function db_getRepeatingDay($dt, $ag) {
	
	try {
		$pdo = tcashConnect();
		$sql = "CALL getRepeatingDay(:dt, :ag)"; 
		$qry = $pdo->prepare($sql);
		$qry->bindValue(":dt", $dt);
		$qry->bindValue(":ag", $ag);
		$qry->execute();
		
		return $qry->fetchall(PDO::FETCH_ASSOC);
	} 
	catch (PDOException $e) {
		$_SESSION["errText"] = $e;		
		return null;
	}
}



function db_getRepeatingToDate($dt, $ag) {
	
	try {
		$pdo = tcashConnect();
		$sql = "CALL getRepeatingToDate(:dt, :ag)"; 
		$qry = $pdo->prepare($sql);
		$qry->bindValue(":dt", $dt);
		$qry->bindValue(":ag", $ag);
		$qry->execute();
		
		return $qry->fetchall(PDO::FETCH_ASSOC);
	} 
	catch (PDOException $e) {
		$_SESSION["errText"] = $e;		
		return null;
	}
}


function db_getAllRepeating($ag) {
	
	try {
		$pdo = tcashConnect();
		$sql = "CALL getAllRepeating(:ag)"; 
		$qry = $pdo->prepare($sql);
		$qry->bindValue(":ag", $ag);
		$qry->execute();
		
		return $qry->fetchall(PDO::FETCH_ASSOC);
	} 
	catch (PDOException $e) {
		$_SESSION["errText"] = $e;		
		return null;
	}
}


function db_updateRepeating($id, $ai, $py, $ca, $am, $nt, $ip, $nd, $ed, $fc, $fi, $ag) {

    try {
        $pdo = tcashConnect();
        $sql = "CALL updateRepeating(:id, :ai, :py, :ca, :am, :nt, :ip, :nd, :ed, :fc, :fi, :ag)";
        $qry = $pdo->prepare($sql);
        $qry->bindValue(":id", $id);
        $qry->bindValue(":ai", $ai);
        $qry->bindValue(":py", $py);
        $qry->bindValue(":ca", $ca);
        $qry->bindValue(":am", $am);
        $qry->bindValue(":nt", $nt);
        $qry->bindValue(":ip", $ip);        
        $qry->bindValue(":nd", $nd);
        $qry->bindValue(":ed", $ed);
        $qry->bindValue(":fc", $fc);
        $qry->bindValue(":fi", $fi);
        $qry->bindValue(":ag", $ag);
        $qry->execute();
        return true;
    } 
    catch (PDOException $e) {
        $_SESSION["errText"] = $e;
        return false;
    }
}



function db_addRepeating($ai, $py, $ca, $am, $nt, $ip, $nd, $ed, $fc, $fi, $ag) {
  
    try {
        $pdo = tcashConnect();
        $sql = "CALL addRepeating(:ai, :py, :ca, :am, :nt, :ip, :nd, :ed, :fc, :fi, :ag)";
        $qry = $pdo->prepare($sql);
        $qry->bindValue(":ai", $ai);
        $qry->bindValue(":py", $py);
        $qry->bindValue(":ca", $ca);
        $qry->bindValue(":am", $am);
        $qry->bindValue(":nt", $nt);
        $qry->bindValue(":ip", $ip);        
        $qry->bindValue(":nd", $nd);
        $qry->bindValue(":ed", $ed);
        $qry->bindValue(":fc", $fc);
        $qry->bindValue(":fi", $fi);
        $qry->bindValue(":ag", $ag);
        $qry->execute();
        return true;
    } 
    catch (PDOException $e) {
        $_SESSION["errText"] = $e;
        return false;
    }
}


function db_removeRepeating($id, $ag) {
  
    try {
        $pdo = tcashConnect();
        $sql = "CALL removeRepeating(:id, :ag)";
        $qry = $pdo->prepare($sql);
        $qry->bindValue(":id", $id);
        $qry->bindValue(":ag", $ag);
        $qry->execute();
        return true;
    } 
    catch (PDOException $e) {
        $_SESSION["errText"] = $e;
        return false;
    }
}


function db_getAccountTypeBank($ag, $at, $ab) {

	try {
		$pdo = tcashConnect();
		$sql = "CALL getAccountTypeBank(:ag, :at, :ab)"; 
		$qry = $pdo->prepare($sql);
		$qry->bindValue(":ag", $ag);
		$qry->bindValue(":at", $at);        
		$qry->bindValue(":ab", $ab);        
		$qry->execute();

		return $qry->fetchall(PDO::FETCH_ASSOC);
	} 
	catch (PDOException $e) {
		$_SESSION["errText"] = $e;		
		return null;
	} 
}



function db_getAllPlaceholderForAcct($ag, $ac) {

	try {
		$pdo = tcashConnect();
		$sql = "CALL getAllPlaceholderForAcct(:ac, :ag)"; 
		$qry = $pdo->prepare($sql);
		$qry->bindValue(":ag", $ag);
		$qry->bindValue(":ac", $ac);        
		$qry->execute();

		return $qry->fetchall(PDO::FETCH_ASSOC);
	} 
	catch (PDOException $e) {
		$_SESSION["errText"] = $e;		
		return null;
	} 
}


function db_adjustTransactionValue($ac, $id, $am, $ag) {
  
    try {
        $pdo = tcashConnect();
        $sql = "CALL adjustTransactionValue(:ac, :id, :am, :ag)";
        $qry = $pdo->prepare($sql);
        $qry->bindValue(":ac", $ac);
        $qry->bindValue(":id", $id);
        $qry->bindValue(":am", $am);
        $qry->bindValue(":ag", $ag);
        $qry->execute();
        return true;
    } 
    catch (PDOException $e) {
        $_SESSION["errText"] = $e;
        return false;
    }
}




