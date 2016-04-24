<?php

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

	//===========================================
	//HANDLE RESPONSE VALUES FROM THIS PAGE, 
	//INCLUDING THE MAIN MENU
	//===========================================

    //check both user name & password were supplied
    //confirm login details are valid & log in

    if (isset($_POST["action"])) {

        //===========================================
        // USER IS ATTEMPTING TO LOG IN
        //===========================================

        if ($_POST["action"] == "login") {
            //==== LOGIN ====//

            $uid      = $_POST["username"];
            $pwd      = t\Security::tcashHash($_POST["password"]);
            $success  = true;

            try {
                $dbconn   = t\Database::connect($_SESSION["envname"]);
                $userdets = t\Security::validateUser($dbconn, $uid, $pwd);

                //if the user is valid, populate the account groups list
                if ($userdets) {              
                    $userdets->setAccountGroups($dbconn);
                }
            } 
            catch (t\TCASHException $e) {
                $_SESSION["errText"] = $e->displayOutput();
                $usePanel = 'common/tcash-panel-error.html.php';
                $success = false;
            }
            catch (Exception $e) {
                $_SESSION["errText"] = $e->getMessage();    
                $success = false;
            }

            if ($success) {
                if ($userdets){
                    $_SESSION["accountgroup"] = $userdets->getAccountGroup();
                    $_SESSION["userobj"] = $userdets;

                    //store the login cookie. Regenerate every time a valid login has taken place.
                    setcookie('tcrm', 
                              t\Security::generateSessionCookie($dbconn, 
                                                                $_SESSION["userobj"]->getUserId(),
                                                                $_SERVER["HTTP_USER_AGENT"]),
                             time() + 3600 * 24 * 14, 
                              '/tcash/');

                    //log the successful login
                    t\Logger::log(new t\LogMessage("User " . $userdets->getUserId() . 
                                                   " logged-in from IP " . 
                                                   $_SERVER["REMOTE_ADDR"]));
                    
                    //forget showing the panel, redirect to home page.
                    header("Location: /tcash/");

                } else {
                    unset ($_SESSION["userobj"]);
                    $_SESSION["accountgroup"] = "LOGIN";
                    
                    $errl = "Invalid email address or password";
                    header("Location: /tcash/user/?mode=login&errl=" . $errl);
                    exit();
                }
                
            } else {

                $usePanel = 'common/tcash-panel-error.html.php';
            }
        
            
        //===========================================
        // USER IS ATTEMPTING TO REGISTER
        //===========================================
                        
        } elseif ($_POST["action"] == "register") {
            //==== REGISTER USER ====//
            
            $uid      = $_POST["username"];
            $fnm      = $_POST["fullname"];
            $eml      = $_POST["email"];
            $pwd      = t\Security::tcashHash($_POST["password"]);
            $cpwd     = t\Security::tcashHash($_POST["confirmpassword"]);
            $success  = true;

            try {
                $dbconn   = t\Database::connect($_SESSION["envname"]);
                
                if ($pwd != $cpwd) {
                    $errr = "Supplied passwords don't match.";
                    header("Location: /tcash/user/?mode=login&errr=" . $errr);
                    exit();
                } else {
                    t\Logger::log(new t\LogMessage("About to register user " . $uid));

                    $userdets = t\Security::registerUser($dbconn, $uid, $fnm, $eml, $pwd);

                    //if the user is valid, populate the account groups list
                    if ($userdets) {              
                        t\Logger::log(new t\LogMessage("User " . $uid . " registered."));
                        $userdets->setAccountGroups($dbconn);
                    } else {
                        t\Logger::log(new t\LogMessage("Registration of user " . $uid
                                                       . " failed."));
                    }
                }
            }
            catch (t\TCASHException $e) {
                t\Logger::log(new t\LogMessage("Exception raised registering user " . $uid, 
                                              $e->displayOutput()));
                $_SESSION["errText"] = $e->displayOutput();
                $usePanel = 'common/tcash-panel-error.html.php';
                $success = false;
            }

            //If the user was successfully added, update the session
            if ($success) {
                $_SESSION["accountgroup"] = $userdets->getAccountGroup();
                $_SESSION["userobj"] = $userdets;
                
                t\Logger::log(new t\LogMessage("About to store cookie"));
                
                //store the login cookie. Regenerate every time a valid login has taken place.
                setcookie('tcrm', 
                          t\Security::generateSessionCookie($dbconn, 
                                                            $_SESSION["userobj"]->getUserId(),
                                                            $_SERVER["HTTP_USER_AGENT"]),
                         time() + 3600 * 24 * 14, 
                          '/tcash/');

                t\Logger::log(new t\LogMessage("Cookie set. Registration complete."));
                
                $displayResult = "USER REGISTERED";
                $usePanel = 'user/tcash-panel-user-admin.html.php';
                $useSubPanel = 'tcash-subpanel-userdets.html.php';
            } else {
                unset ($_SESSION["userobj"]);
                $_SESSION["accountgroup"] = "LOGIN";

                t\Logger::log(new t\LogMessage("Registration failed (" . $uid . ")"));
                $errl = "Unable to register user";
                header("Location: /tcash/user/?mode=login&errr=" . $errl);
                exit();
            }
        
        } elseif ($_POST["action"] == "updateuser") {
            //==== UPDATE USER DETAILS ====//

            //grab user details from the screen
            $uid = $_SESSION["userobj"]->getUserId();
            $fnm = $_POST["fullname"];
            $eml = $_POST["email"];
            $pwd = t\Security::tcashHash($_POST["password"]);
            
            //Set new passwords to NULL if they are empty
            if (strlen($_POST["new-password"]) > 0) {
                $npw = t\Security::tcashHash($_POST["new-password"]);
                $np2 = t\Security::tcashHash($_POST["new-password-confirm"]);
            } else {
                $npw = null;
                $np2 = null;
            }
            
            //attempt the update of user details
            try {
                $updsuccess = t\User::updateUser(t\Database::connect(), 
                                               $uid, $fnm, $eml, $pwd, $npw, $np2);
            } 
            catch (t\TCASHException $e) {
                t\Logger::log(new t\LogMessage("Exception raised updating user " . $uid, 
                                              $e->displayOutput()));
                $_SESSION["errText"] = $e->displayOutput();
                $usePanel = 'common/tcash-panel-error.html.php';
            }
            
            //If the update worked, amend the user details in session
            if ($updsuccess) {
                $_SESSION["userobj"]->setFullName($fnm)
                                    ->setEmail($eml);
                header("Location: /tcash/user");
                exit();
                
            } else {
                //the update failed; log out.
                unset ($_SESSION["userobj"]);
                $_SESSION["accountgroup"] = "LOGIN";
                $errl = "Error updating user. Login again.";
                header("Location: /tcash/user/?mode=login&errl=" . $errl);
                exit();
            }
        
            
        } elseif ($_POST["action"] == "cancel") {
            //==== CANCEL AND GO BACK ====//
            header("Location: /tcash/user");
            exit();
            
            
        } elseif ($_POST["action"] == "updacc") {
            //==== UPDATE ACCOUNT DETAILS ====//
            
            //Cycle all accounts sending updates
            foreach ($_POST as $name => $val) {
                if (substr($name, 0, 5) == "name_") {
                    
                    //get all values for the update
                    $id = substr($name, 5);
                    $nm = $_POST[$name];
                    $ty = $_POST["type_".$id];
                    $bk = $_POST["bankname_".$id];
                    
                    if ($id == "new") {
                        
                        //only add a new record if the account name is specified.
                        if (strlen($nm) > 0) { 
                            
                            t\Logger::log(new t\LogMessage("Attempting to add account ". 
                                           "id=".$_POST["id_new"]." | ".
                                           "nm=".$nm." | ".
                                           "ty=".$ty." | ".
                                           "ag=".$_SESSION["userobj"]->getAccountGroup()." | ".
                                           "bk=".$bk));
                            
                            try {
                                t\Account::addAccount($_POST["id_new"], $nm, 
                                           $ty, $bk, $_SESSION["userobj"]->getAccountGroup());
                            } 
                            catch (t\TCASHException $e) {
                                t\Logger::log(new t\LogMessage("Error adding new account metadata", 
                                                           $e->displayOutput()));
                                
                                $usePanel = 'common/tcash-panel-error.html.php';
                                $_SESSION["errText"] = $e->displayOutput();
                            }
                        }
                    } else {
                        //update the account metadata
                        try {
                            t\Account::updateAccount($id, $nm, $ty, $bk, 
                                                     $_SESSION["userobj"]->getAccountGroup());
                        } 
                        catch (t\TCASHException $e) {
                           throw new t\TCASHException ("Error updating account metadata", 
                                                       $e->displayOutput());
                        }
                    }
                }
            }
            
            //re-cache the account data in session
            $_SESSION["userobj"]->refreshAccountCache();

            //redirect back to the view account screen
            header("Location: /tcash/user?mode=viewacc");
            exit();
            
        } elseif ($_POST["action"] == "joingroup") {
            //==== JOIN AN EXISTING GROUP ====//
            $uid = $_SESSION["userobj"]->getUserId();
            $gid = $_POST["joingroupname"];
            $gpw = t\Security::tcashHash($_POST["joingrouppw"]);
            $pgp = sizeof($_SESSION["userobj"]->getAccountGroups()) > 0 ? 0 : 1;
            
            if (t\Security::addUserAGLink(t\Database::connect(), $uid, $gid, $gpw, $pgp) == 1) {
                t\Logger::log(new t\LogMessage("join existing group ok"));
                $_SESSION["userobj"]->refreshAccGroupCache();
                header("Location: /tcash/user?mode=viewacg");
                exit();                
            } else {
                //group does not exist, or PIN was wrong 
                //STOP... can't join group if it doesn't exist
                $usePanel = 'common/tcash-panel-error.html.php';
                $_SESSION["errText"] = "Error joining existing group";
            }
            
            
        } elseif ($_POST["action"] == "addgroup") {
            //==== ADD A NEW GROUP ====//
            $uid = $_SESSION["userobj"]->getUserId();
            $gid = $_POST["addgroupname"];
            $gpw = t\Security::tcashHash($_POST["addgrouppw"]);
            $gde = $_POST["addgroupdesc"];
            $pgp = sizeof($_SESSION["userobj"]->getAccountGroups()) > 0 ? 0 : 1;
            
            if (t\Security::checkAGExists(t\Database::connect(), $gid) == 1) {
                //group already exists. Don't care about the PIN in this instance.
                //STOP... can't create another group with same name
                t\Logger::log(new t\LogMessage("add new group bad"));
            } else {
                //group does not exist. Don't care about the PIN. CONTINUE.
                if (t\Security::addAccountGroup(t\Database::connect(), $gid, $gde, $gpw, $uid)) {
                
                    if (t\Security::addUserAGLink(t\Database::connect(), $uid, $gid, $gpw, $pgp) == 1)
                    {
                        t\Logger::log(new t\LogMessage("add new group ok"));
                        $_SESSION["userobj"]->refreshAccGroupCache();
                        header("Location: /tcash/user?mode=viewacg");
                        exit();
                    }
                }
            }

            //If we have dropped down to here then something went awry. Error out.
            $usePanel = 'common/tcash-panel-error.html.php';
            $_SESSION["errText"] = "Error adding new account group";

            
        } elseif ($_POST["action"] == "leavegrp") {
            //==== LEAVE ACCOUNT GROUP ====//
            $uid = $_SESSION["userobj"]->getUserId();
            $gid = $_POST["leavegroup"];
            
            //call leave group method on User object in Session
            try {
                if ($_SESSION["userobj"]->leaveAccountGroup(t\Database::connect(), $gid)) {
                    t\Logger::Log(new t\LogMessage("removed user ".$uid." from group ".$gid));
                    header("Location: /tcash/user?mode=viewacg");
                    exit();
                } else {
                    //Could not remove link between user and accountgroup
                    t\Logger::Log(new t\LogMessage("error removing user ".$uid." from group ".$gid));
                    $usePanel = 'common/tcash-panel-error.html.php';
                    $_SESSION["errText"] = "Error removing link to account group";
                }
            } 
            catch (t\TCASHException $e) {
                //Could not remove link between user and accountgroup
                t\Logger::Log(new t\LogMessage("error removing user ".$uid." from group ".$gid));
                $usePanel = 'common/tcash-panel-error.html.php';
                $_SESSION["errText"] = "Error removing link to account group";
            }
            
        } else {
            //==== UNHANDLED ACTION - BOMB OUT ====//
            t\Logger::log(new t\LogMessage("action = " . $_POST["action"] . " was unhandled."));
        }
    } else {

//==============================================================================================//
        
        //======================================================
        //DEFAULT CODE FOR JUST DISPLAYING THE PAGE AS CALLED
        //======================================================

        $mode = isset($_GET["mode"]) ? $_GET["mode"] : "login";
        $errr = isset($_GET["errr"]) ? $_GET["errr"] : "";
        $errl = isset($_GET["errl"]) ? $_GET["errl"] : "";
        
        try {
            switch ($mode) {

                case "login":
                    //if user is already logged in, go straight to the "complete" screen
                    if (isset($_SESSION["userobj"]) && $_SESSION["userobj"] != null) {
                        $displayResult = "Logged In";
                        $userdets = $_SESSION["userobj"];
                        $usePanel = 'user/tcash-panel-user-admin.html.php';
                        $useSubPanel = 'tcash-subpanel-userdets.html.php';
                    } else {
                        $usePanel = 'user/tcash-panel-login.html.php';
                    }
                    break;
                
                case "logout":
                    //remove session variables for user
                    $uid = $_SESSION["userobj"]->getUserId();
                    unset($_SESSION["userobj"]);
                    unset($_SESSION["accountgroup"]);
                
                    //remove cookie data from database
                    t\Security::removeSessionCookie(t\Database::connect($_SESSION["envname"]), 
                        (isset($_COOKIE["tcrm"]) ? $_COOKIE["tcrm"] : null),
                        $_SERVER["HTTP_USER_AGENT"]);
                
                    //remove cookie from browser
                    setcookie("tcrm", "", time()-3600, "/tcash/");
                    
                    //log the successful logoff
                    t\Logger::log(new t\LogMessage("User " . $uid . " logged-off from IP " . 
                                                   $_SERVER["REMOTE_ADDR"]));
                
                    //redirect back to login page
                    header("Location: /tcash/user/?mode=login");
                    exit();
                    break;

                case "edit":
                    //edit the user details
                    if (isset($_SESSION["userobj"]) && !is_null($_SESSION["userobj"])) {
                        $usePanel = 'user/tcash-panel-user-admin.html.php';
                        $useSubPanel = 'tcash-subpanel-useredit.html.php';
                    } else {
                        $_SESSION["errText"] = "Error retrieving user details";
                        $usePanel = 'common/tcash-panel-error.html.php';
                    }
                    break;

                case "delete":
                    //remove the current user and logout
                    break;
                
                case "viewacc":
                    //display accounts for this account group
                
                    if ($_SESSION["userobj"]->getAccounts()) {
                        $accdets = $_SESSION["userobj"]->getAccounts(); 
                    } else {
                        $accdets = null; 
                    }
                
                    $usePanel = 'user/tcash-panel-user-admin.html.php';
                    $useSubPanel = 'tcash-subpanel-accountdets.html.php';
                    break;
                                
                case "viewacg":
                    //display details about account group
                    $usePanel = 'user/tcash-panel-user-admin.html.php';
                    $useSubPanel = 'tcash-subpanel-accgroupdets.html.php';
                    break;
                
                case "leaveacg":
                    //display leave group confirmation screen
                    $usePanel = 'user/tcash-panel-user-admin.html.php';
                    $useSubPanel = 'tcash-subpanel-accgroupleave.html.php';
                    $leavegroup = $_GET["acg"];
                    break;
                
                default:
                    $_SESSION["errText"] = "Could not recognise requested mode";
                    $usePanel = 'common/tcash-panel-error.html.php';
            }
        } catch (Exception $e) {
            //call error panel
            $_SESSION["errText"] = $e;
            $usePanel = 'common/tcash-panel-error.html.php';
        }


    }

    //hand off to the HTML template file...
    include '../tcash-frame.html.php';

