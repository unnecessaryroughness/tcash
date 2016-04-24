<?php


/*
    REMAIN LOGGED IN NOTES...
    
    http://jaspan.com/improved_persistent_login_cookie_best_practice

    X. When the user successfully logs in with Remember Me checked, a login cookie is issued 
       in addition to the standard session management cookie.[2]
    X. The login cookie contains the user's username, a series identifier, and a token. The series 
       and token are unguessable random numbers from a suitably large space. All three are stored 
       together in a database table.
    X. When a non-logged-in user visits the site and presents a login cookie, the username, series,
       and token are looked up in the database.
    X. If the triplet is present, the user is considered authenticated. The used token is removed from 
       the database. A new token is generated, stored in database with the username and the same 
       series identifier, and a new login cookie containing all three is issued to the user.
    5. If the username and series are present but the token does not match, a theft is assumed. The 
       user receives a strongly worded warning and all of the user's remembered sessions are deleted.
    6. If the username and series are not present, the login cookie is ignored.

    It is critical that the series identifier be reused for each token in a series. If the series 
    identifier were instead simply another one time use random number, the system could not 
    differentiate between a series/token pair that had been stolen and one that, for example, had  
    simply expired and been erased from the database.
    
*/

    namespace tcash;

    class Security {

        const SALT = "1009700e1675853b167ea786dc4c3f35";
        
    /*  ============================================
        FUNCTION:   validateUser (STATIC)
        PARAMS:     dbc - database connection object
                    uid - user id
                    pwd - password
        RETURNS:    User object
        ============================================  */
        public static function validateUser($dbc, $uid, $pwd) {
            
            try {
                $sql = "CALL validateUser(:uid, :pwd)";
                $qry = $dbc->prepare($sql);
                $qry->bindValue(":uid", $uid);
                $qry->bindValue(":pwd", $pwd);
                $qry->execute();
                
                $userdata = $qry->fetch(\PDO::FETCH_ASSOC);

                if ($userdata) {
                    $user = new User($userdata["id"], 
                                     $userdata["fullname"], 
                                     $userdata["email"]);

                    Logger::log(new LogMessage("User " . $uid . " validated"));
                    return $user;   
                } else { 
                    Logger::log(new LogMessage("Failed to validate user " . $uid));
                    return false;
                }
            } 
            catch (\PDOException $e) {
                throw new TCASHException('Unable to validate user ' . $uid, $e);
		        exit();
            }
        }
       
    /*  ============================================
        FUNCTION:   registerUser (STATIC)
        PARAMS:     dbc - database connection object
                    uid - user id
                    fnm - full name
                    eml - email
                    pwd - password
                    acg - account group
                    agp - account group pin
        RETURNS:    User object
        ============================================  */
        public static function registerUser($dbc, $uid, $fnm, $eml, $pwd) {
        
            $gotuser = false;
            $newuser = false;
            $gotgroup = false;
            $newgroup = false;
            $gotlink = false;
            $newlink = false;
            
            //check username/password combination doesn't already exist
            try {
                $sql = "CALL checkUserExists(:uid)";
                $qry = $dbc->prepare($sql);
                $qry->bindValue(":uid", $uid);
                $qry->execute();
                $userdata = $qry->fetch(\PDO::FETCH_ASSOC);

                //Check if a user record was found
                if ($userdata) {

                    //If user was found, did the passwords match?
                    if ($userdata["password"] != $pwd) {
                        throw new TCASHException("Supplied user name already exists", null);
                        return false;
                    } else {
                        //if passwords do match, just use this user for account group operation
                        $gotuser = true;
                    }

                } else { 
                    //user didn't exist - create it
                    $sql = "CALL addUser(:uid, :fnm, :pwd, :eml)";
                    $qry = $dbc->prepare($sql);
                    $qry->bindValue(":uid", $uid);
                    $qry->bindValue(":fnm", $fnm);
                    $qry->bindValue(":pwd", $pwd);
                    $qry->bindValue(":eml", $eml);
                    $gotuser = ($qry->execute()); 
                
                    //Check if user record was created ok.
                    if ($gotuser) {
                        $newuser = true;
                    } else {
                        throw new TCASHException("Unable to create a new user.", null);
                        return false;
                    }
                }
            } 
            catch (\PDOException $e) {
                throw new TCASHException('Unable to create user ' . $uid, $e);
		        exit();                
            }
            
            //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
            //Either by retrieving, or adding, we should now have a user record
            //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
/*

            //check if an account group exists with the supplied name
            try {
                
                $groupchk = self::checkAGExists($dbc, $acg, $acgp);
                            
                if ($groupchk == -1) {
                    //group exists, but password was wrong
                    throw new TCASHException("Supplied account group already exists", null);
                } elseif ($groupchk == 1) {
                    //group exists & password was correct
                    $gotgroup = true;
                } else { 
                    //group doesn't exist, so create it
                    if (self::addAccountGroup($dbc, $acg, $acgd, $acgp, $uid)) {
                        $gotgroup = true;
                        $newgroup = true;
                    } else {
                        Logger::log(new LogMessage("accountgroup " . $acg . " creation failed"));
                        throw new TCASHException("Unable to create a new account group.", null);
                    }
                }
            } 
            catch (\PDOException $e) {
                Logger::log(new LogMessage("accountgroup creation failed"));
                throw new TCASHException('Unable to create account group ' . $acg, $e);
            }
 
            //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
            //Either by retrieving, or adding, we should now have an account group record
            //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

            $gotlink = self::addUserAGLink($dbc, $uid, $acg, $acgp, ($newuser ? 1 : 0));

            if ($gotlink == -1) {
                Logger::log(new LogMessage("Failed to create link between user & accgroup"));
                throw new TCASHException ("Unable to create a new user/group link.");
            }
                
*/

            //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
            //now have a user, a group and a link, so return new user object
            //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//            if ($gotuser && $gotgroup && $gotlink) {
            if ($gotuser) {
                return new User($uid, $fnm, $eml);
            } else {
                Logger::log(new LogMessage("Failed to create user (misc)"));
                return false;
            }
        }
        
        
    /*  ============================================
        FUNCTION:   checkAGExists (STATIC)
        PARAMS:     dbc - database connection to use
                    acg - account group to check
                    pwd - password to check
        RETURNS:    (integer) - 1 = exists && passwords matched
                                0 = does not exist
                               -1 = exists, but password was wrong
        ============================================  */
        public static function checkAGExists($dbc, $acg, $pwd=null) {
            try {
                $sql = "CALL checkAccountGroupExists(:acg)";
                $qry = $dbc->prepare($sql);
                $qry->bindValue(":acg", $acg);
                $qry->execute();
                $agdata = $qry->fetch(\PDO::FETCH_ASSOC);
            
                //Check if an account group record was found
                if ($agdata) {
                    if ($pwd) {
                        //If account group was found, did the passwords match?
                        if ($agdata["password"] != $pwd) {
                            Logger::log(new LogMessage("accountgroup pin comparison failed"));
                            return -1;
                        } else {
                            //if passwords do match, just use this account group
                            return 1;
                        }
                    } else {
                        return 1;
                    }
                } else {
                    return 0;
                }
            }
            catch (\PDOException $e) {
                Logger::log(new LogMessage("Error checking existing account group"));
                throw new TCASHException ("Error checking existing account group", $e->getMessage()); 
            }
        }
        

    /*  ============================================
        FUNCTION:   addAccountGroup (STATIC)
        PARAMS:     dbc - database connection to use
                    acg - account group to add
                    dsc - description to add
                    pwd - password to add
                    uid - group owner to add
        RETURNS:    (boolean) - indicates if group was added OK
        ============================================  */
        public static function addAccountGroup($dbc, $acg, $dsc, $pwd, $uid) {

            try {
                $sql = "CALL addAccountGroup(:acg, :acgd, :acgp, :uid)";
                $qry = $dbc->prepare($sql);
                $qry->bindValue(":uid", $uid);
                $qry->bindValue(":acg", $acg);
                $qry->bindValue(":acgp", $pwd);
                $qry->bindValue(":acgd", $dsc);
                $gotgroup = ($qry->execute()); 

                //Check if account group record was created ok.
                if ($gotgroup) {
                    return true;
                } else {
                    Logger::log(new LogMessage("accountgroup " . $acg . " creation failed"));
                    return false;
                }
            }
            catch (\PDOException $e) {
                Logger::log(new LogMessage("accountgroup " . $acg . " creation failed"));
                throw new TCASHException("Error creating new account group", $e->getMessage()); 
            }
        }

        
    /*  ============================================
        FUNCTION:   addUserAGLink (STATIC)
        PARAMS:     dbc - database connection to use
                    uid - user to link
                    acg - account group to link
                    prm - make this the primary group? true/false
        RETURNS:    (integer) - 1 = added ok
                                2 = already linked
                               -1 = could not link
        ============================================  */
        public static function addUserAGLink($dbc, $uid, $acg, $pwd, $prm) {
        
            if (self::checkAGExists($dbc, $acg, $pwd) == 1) {
                try {
                    $sql = "CALL checkUserGroupLinkExists(:uid, :acg)";
                    $qry = $dbc->prepare($sql);
                    $qry->bindValue(":uid", $uid);
                    $qry->bindValue(":acg", $acg);
                    $qry->execute();
                    $aglink = $qry->fetch(\PDO::FETCH_ASSOC);

                    if ($aglink) {
                        return 2;
                    } else {
                        //account group didn't exist - create it
                        $sql = "CALL addUserGroupLink(:uid, :acg, :prm)";
                        $qry = $dbc->prepare($sql);
                        $qry->bindValue(":uid", $uid);
                        $qry->bindValue(":acg", $acg);
                        $qry->bindValue(":prm", $prm);
                        return $qry->execute() ? 1 : -1;
                    }
                }
                catch (\PDOException $e) {
                   throw new TCASHException ("Error linking user to account group"); 
                }
            } else {
               return -1; 
            }
        }
        
        
        
        
    /*  ============================================
        FUNCTION:   tcashHash (STATIC)
        PARAMS:     data - data to be hashed
        RETURNS:    string - hashed value
        ============================================  */
        public static function tcashHash($data) {
           return hash_hmac('sha256', $data, Security::SALT); 
        }
        
        
    /*  ============================================
        FUNCTION:   generateRandomToken (STATIC)
        PARAMS:     (none)
        RETURNS:    string - hashed random value
        ============================================  */
        public static function generateRandomToken() {
           return Security::tcashHash(md5(rand())); 
        }
        
        
    /*  ============================================
        FUNCTION:   generateSessionCookie (STATIC)
        PARAMS:     uid - userid to be used
        RETURNS:    string - hashed random value
        ============================================  */
        public static function generateSessionCookie($dbc, $uid, $fpt) {
            
            //generate random values for series and token
            //fingerprint is based on USER_AGENT so identifies browser used
            $token  = Security::generateRandomToken();
            $series = Security::generateRandomToken();
            $fprint = Security::tcashHash($fpt);
            $cookie = $uid . ":" . $series . ":" . $token . ":" . $fprint;
            
            //store session data in database
            try {
                $sql = "CALL addSession(:uid, :ser, :tok, :fpt)";
                $qry = $dbc->prepare($sql);
                $qry->bindValue(":uid", $uid);
                $qry->bindValue(":ser", $series);
                $qry->bindValue(":tok", $token);
                $qry->bindValue(":fpt", $fprint);

                //Check if session record was created ok.
                if (! $qry->execute()) {
                    throw new TCASHException("Unable to create session data entry.", null);
                    return false;
                }
            }
            catch (\PDOException $e) {
                throw new TCASHException('Unable to session data entry.', $e);
		        exit();  
            }
            
            //return the generated cookie, based on session data stored in DB
            return $cookie;
        }
        
        
    /*  ============================================
        FUNCTION:   matchSessionCookie (STATIC)
        PARAMS:     dbc    - database connection object
                    cookie - cookie to be found in DB
                    fprint - the fingerprint string
        RETURNS:    string - if cookie was matched it 
                             will be replaced by a new 
                             cookie, which is returned
        ============================================  */
        public static function matchSessionCookie($dbc, $cookie, $fprint) {

            //grab the individual variables from the cookie
            list ($uid, $ser, $tok, $fpt) = explode(':', $cookie);

            //if the stored fingerprint doesn't match the current fingerprint then the 
            //cookie has been stolen from another device/browser and should not be used
            if ($fpt != Security::tcashHash($fprint)) {

                throw new TCASHException('The remembered user information was not recorded ' .
                                        'in this environment. The information may have been ' .
                                        'compromised and will not be used.', null);
                
                exit();
            }

            
            //search for session information in the database
            try {
                $sql = "CALL matchSession(:uid, :ser, :tok, :fpt)";
                $qry = $dbc->prepare($sql);
                $qry->bindValue(":uid", $uid);
                $qry->bindValue(":ser", $ser);
                $qry->bindValue(":tok", $tok);
                $qry->bindValue(":fpt", $fpt);
                $qry->execute();
                    
                $matched = $qry->fetch(\PDO::FETCH_ASSOC);    
    
                if ($matched) {
                    //if an exact match could not be found, but the series was found
                    //with a different token, the session has probably been hijacked.
                    if ($matched["token"] == "HIJACK") {
                       throw new TCASHException('Your stored session data suggests your ' .
                                                'login details have been hijacked by another ' .
                                                'user. All of your cached sessions will now ' .
                                                'be deleted. You will have to log in again. ' .
                                                'You should consider changing your password. ', null);
                        exit();
                    }
                } else {
                    return false; 
                }
            }
            catch (\PDOException $e) {
                throw new TCASHException('Unable to query session data.', $e);
		        exit();  
            }
            
            //if we got here then we matched the cookie data in the database and we are
            //still using the exact same USER_AGENT as recorded the cookie
            
            //now need to replace the token and update the database
            $tok = Security::generateRandomToken();

            try {
                $sql = "CALL replaceSessionToken(:uid, :ser, :tok, :fpt)";
                $qry = $dbc->prepare($sql);
                $qry->bindValue(":uid", $uid);
                $qry->bindValue(":ser", $ser);
                $qry->bindValue(":tok", $tok);
                $qry->bindValue(":fpt", $fpt);
                $replaced = $qry->execute();
                    
                if (!$replaced) {
                   return false; 
                }
            }
            catch (\PDOException $e) {
                throw new TCASHException('Unable to update session data.', $e);
		        exit();  
            }
            
            //if we got here, then the cookie was matched and the token replaced
            //so return the NEW cookie value to be stored by the calling program
            return $uid . ":" . $ser . ":" . $tok . ":" . $fpt;
        }
        
        
    /*  ============================================
        FUNCTION:   removeSessionCookie (STATIC)
        PARAMS:     dbc    - database connection object
                    cookie - cookie to be found in DB
                    fprint - the fingerprint string
        RETURNS:    string - if cookie was matched it 
                             will be replaced by a new 
                             cookie, which is returned
        ============================================  */
        public static function removeSessionCookie($dbc, $cookie, $fprint) {

            if (isset($cookie) && !is_null($cookie)) {
                
                //split cookie to find individual parameters
                list ($uid, $ser, $tok, $fpt) = explode(':', $cookie);
                
                //if the stored fingerprint doesn't match the current fingerprint then the 
                //cookie has been stolen from another device/browser and should not be used
                if ($fpt != Security::tcashHash($fprint)) {

                    throw new TCASHException('The remembered user information was not recorded ' .
                                            'in this environment. The information may have been ' .
                                            'compromised and will not be used.', null);

                    exit();
                }

                //call database procedure to remove session entry
                try {
                    $sql = "CALL removeSession(:uid, :ser, :tok, :fpt)";
                    $qry = $dbc->prepare($sql);
                    $qry->bindValue(":uid", $uid);
                    $qry->bindValue(":ser", $ser);
                    $qry->bindValue(":tok", $tok);
                    $qry->bindValue(":fpt", $fpt);
                    return $qry->execute();
                }
                catch (\PDOException $e) {
                    throw new TCASHException('Unable to query session data.', $e);
                    exit();  
                }
            }
        }
    }
