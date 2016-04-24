<?php

    use tcash as t;

    //setup autoloaders for TCASH and CHLOG because they share the same website
    //and lingering objects in session might cause issues if both autoloaders are
    //not present all of the time.
    //include_once $_SERVER["DOCUMENT_ROOT"] . '/chlog/common/chlogautoload.php';
    include_once $_SERVER["DOCUMENT_ROOT"] . '/tcash/common/tcashautoload.php';
    
    // set the environment variables in SESSION...
	include_once $_SERVER['DOCUMENT_ROOT'] . '/tcash/common/tcash-helpers.inc.php';
	//Start the session
	safesessionstart();
	setdevmodeparams();

	//build the database connection $PDO object...
	include_once $_SERVER['DOCUMENT_ROOT'] . '/tcash/common/tcash-db.inc.php';

	//link to the login handling code... 
	include_once $_SERVER['DOCUMENT_ROOT'] . '/tcash/common/tcash-access.inc.php';

	//link to the class definitions...
	include_once $_SERVER['DOCUMENT_ROOT'] . '/tcash/common/tcash-classes.inc.php';


    //Set logging flag
    $_SESSION["logging"] = true;


    //If the accountgroup is not set, 
    //or the accountgroup is "LOGIN" but this is not the login page, 
    //redirect to the login page
    if (!isset($_SESSION["accountgroup"]) || 
        ($_SESSION["accountgroup"]=="LOGIN" && 
            substr($_SERVER["REQUEST_URI"], 0, 12) !="/tcash/user/")) {
        
        $loggedin = false;
                
        //if there is a "remember me" cookie then try to use it to log on
        if (isset($_COOKIE["tcrm"])) {
            
            //is the exact cookie data in the database?
            try {
                $dbconn = t\Database::connect($_SESSION["envname"]);
                 
                $newcookie = t\Security::matchSessionCookie($dbconn,
                                                            $_COOKIE["tcrm"], 
                                                            $_SERVER["HTTP_USER_AGENT"]);
                
                //if the session is matched in the DB a new cookie is returned
                if ($newcookie) {
                    
                    //store the new cookie in the browser. Cache for 14 days from now.
                    setcookie('tcrm', $newcookie, time() + 3600 * 24 * 14, '/tcash/');
                    
                    //set up session
                    $cookie_arr = explode(':', $newcookie);
                    $userdets = t\User::getUserFromId($dbconn, $cookie_arr[0]);
                    
                    //if the user is valid, populate the account groups list
                    if ($userdets) {              
                        $userdets->setAccountGroups($dbconn); 
                        $_SESSION["accountgroup"] = $userdets->getAccountGroup();
                        $_SESSION["userobj"] = $userdets;

                        $loggedin = true;
                        t\Logger::log(new t\LogMessage("User " . $userdets->getUserId() . 
                                                       " session restored from IP " . 
                                                       $_SERVER["REMOTE_ADDR"]));
                    } else {
                        unset($_SESSION["userobj"]);
                        unset($_SESSION["accountgroup"]);
                        t\Logger::log(new t\LogMessage("Error restoring session", $newcookie));
                        setcookie("tcrm", "", time()-3600, "/tcash/");
                        throw new t\TCASHException ("Error restoring session", null);
                    }
                }
            }
            catch (t\TCASHException $e) {
                unset($_SESSION["userobj"]);
                unset($_SESSION["accountgroup"]);
                setcookie("tcrm", "", time()-3600, "/tcash/");
                t\Logger::log(new t\LogMessage("Error restoring session", $e->displayOutput()));
                throw new t\TCASHException ("Error restoring session", $e->displayOutput()); 
            }
            catch (Exception $e) {
                unset($_SESSION["userobj"]);
                unset($_SESSION["userobj"]);
                setcookie("tcrm", "", time()-3600, "/tcash/");
                t\Logger::log(new t\LogMessage("Error restoring session", $e->getMessage()));
                throw new t\TCASHException ("Error restoring session", $e->getMessage());
            }
        }

        //if the user is still not logged in, redirect to the login page
        if (!$loggedin) {
            $_SESSION["accountgroup"] = "LOGIN";
            header("location: /tcash/user/?mode=login");
            exit();
        }
    }



	//Magic Quotes code
	if (get_magic_quotes_gpc())
	{
	  $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
	  while (list($key, $val) = each ($process))
	  {
		foreach ($val as $k => $v) 
		{
		  unset($process[$key][$k]);
		  if (is_array($v))
		  {
			$process[$key][stripslashes($k)] = $v;
			$process[] = &$process[$key][stripslashes($k)];
		  }
		  else
		  {
			$process[$key][stripslashes($k)] = stripslashes($v);
		  }
		}
	  }
	  unset($process);
	}

	
