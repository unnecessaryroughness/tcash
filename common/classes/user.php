<?php

    namespace tcash;

    class User {
        
        protected $id;
        protected $fullname;
        protected $email;
        protected $dbconn;
        protected $accountgroups;
        protected $primaryaccgroup;
        protected $accountgroup;
        protected $accounts;
        
    /*  ============================================
        FUNCTION:   __construct 
        PARAMS:     uid - user id
                    fnm - fullname
                    eml - email address
        RETURNS:    boolean
        ============================================  */
        public function __construct($uid="", $fnm="", $eml="") {
            $this->id = $uid;
            $this->fullname = $fnm;
            $this->email = $eml;
        }

        
    /*  Simple GET methods */
        
        public function getUserId()   { return $this->id; }
        public function getFullName() { return $this->fullname; }
        public function getEmail()    { return $this->email; }
        public function getAccounts() { return $this->accounts; }
        
    /*  Simple SET methods */
        
        public function setFullName($fnm) {
            $this->fullname = $fnm;
            return $this;
        }
        
        public function setEmail($eml) {
            $this->email = $eml;
            return $this;
        }
        
        protected function setAccountGroup($nag) {
            $this->accountgroup = $nag;
            $this->accounts = Portfolio::getAccountList($this->accountgroup);
        }
        
        public function refreshAccountCache() {
            $this->accounts = Portfolio::getAccountList($this->accountgroup);
        }
        
        public function refreshAccGroupCache() {
            $this->accountgroups = null;
            $this->setAccountGroups(Database::connect());
        }
        
        public function getDBConn() {
            return is_null($this->dbconn) ? Database::connect() : $this->dbconn;
        }
        
        
    /*  ============================================
        FUNCTION:   isAccGroupOwner 
        PARAMS:     dbc - database connection object
        RETURNS:    (boolean) indicates if current user is owner of current account group
        ============================================  */
        public function isAccGroupOwner($dbc=null) {
            try {
                $dbc = is_null($dbc) ? $this->getDBConn() : $dbc;                    
                $sql = "CALL getAccGroupOwner(:acg)";
                $qry = $dbc->prepare($sql);
                $qry->bindValue(":acg", $this->accountgroup);
                $qry->execute();
                $groupdata = $qry->fetch(\PDO::FETCH_ASSOC);
                
                if ($groupdata) {
                    return $groupdata["owner"] == $this->id;   
                } else { 
                    return false;
                }
            } 
            catch (\PDOException $e) {
                throw new TCASHException('Unable to account group owner ' . $this->accountgroup, $e);
		        exit();
            }            
        }
        
        
        
    /*  ============================================
        FUNCTION:   getUserFromId (STATIC)
        PARAMS:     dbc - database connection object
                    uid - user id
        RETURNS:    User object
        ============================================  */
        public static function getUserFromId($dbc, $uid) {
            try {
                $sql = "CALL getUserFromId(:uid)";
                $qry = $dbc->prepare($sql);
                $qry->bindValue(":uid", $uid);
                $qry->execute();
                
                $userdata = $qry->fetch(\PDO::FETCH_ASSOC);

                if ($userdata) {
                    $user = new User($userdata["id"], 
                                     $userdata["fullname"], 
                                     $userdata["email"]);
                    return $user;   
                } else { 
                    return false;
                }
            } 
            catch (\PDOException $e) {
                throw new TCASHException('Unable to retrieve user ' . $uid, $e);
		        exit();
            }
        }
        
    /*  ============================================
        FUNCTION:   setDBConn
        PARAMS:     dbc - database connection (PDO object)
        RETURNS:    boolean
        ============================================  */
        public function setDBConn(\PDO $dbc) {
            if ($dbc instanceof \PDO) {
                $this->dbconn = $dbc;
                return true;
            } else {
                throw new TCASHException ("Supplied parameter was not a database connection ("
                                        . gettype($dbc) . ")", null);
                return false;
            }
        }
        
    /*  ============================================
        FUNCTION:   disconnectDB
        PARAMS:     none
        RETURNS:    boolean
        ============================================  */
        public function disconnectDB() {
            $this->dbconn = null; 
            return true;
        }
        

    /*  ============================================
        FUNCTION:   setAccountGroups
        PARAMS:     none
        RETURNS:    boolean
        ============================================  */
        public function setAccountGroups($dbc) {
            
           //database connection must be set before attempting this method
           if (!isset($dbc) || $dbc == null) {
               throw new TCASHException ("Did not pass an " . 
                                         "active database connection", null);
               exit();
           } else {

               //call database to get valid accountgroups for this user
               try {
                    $sql = "CALL getGroupsForUser(:uid)";
                    $qry = $dbc ->prepare($sql);
                    $qry->bindValue(":uid", $this->id);
                    $qry->execute();
                    $groupdata = $qry->fetchall(\PDO::FETCH_ASSOC);
               } 
               catch (\PDOException $e) {
                   throw new TCASHException ("Could not retrieve user groups", $e->getMessage());
                   exit();
               }

               //build accountgroups array based on database results
               foreach ($groupdata as $gp) {
                    $this->accountgroups[] = array("groupid" => $gp["groupid"], 
                                                   "primary" => $gp["primarygroup"]);

                    if ($gp["primarygroup"] == 1) {
                       $this->primaryaccgroup = $gp["groupid"]; 
                    }
                }

               //select primary account group by default
               $this->setAccountGroup($this->primaryaccgroup);
               return true;
           }
        }
        
    /*  ============================================
        FUNCTION:   getAccountGroups
        PARAMS:     none
        RETURNS:    string
        ============================================  */
        public function getAccountGroup() {
           return $this->accountgroup; 
        }
        
    /*  ============================================
        FUNCTION:   getAccountGroups
        PARAMS:     none
        RETURNS:    array (associative)
        ============================================  */
        public function getAccountGroups() {
           return $this->accountgroups; 
        }
        
    /*  ============================================
        FUNCTION:   switchAccountGroup
        PARAMS:     ag  - accountgroup to switch to
        RETURNS:    boolean
        ============================================  */
        public function switchAccountGroup($ag) {
            
            //check the requested account group is in the allowed list for this user
            foreach ($this->accountgroups as $ags) {
                if ($ags["groupid"] == $ag) {
                    $nag = $ag; 
                }
            }
            
            //if allowed, switch the accountgroup
            if (isset($nag)) {
                $this->setAccountGroup($nag);
                return true;
            } else {
                throw new TCASHException ("Invalid account group selected", null); 
                return false;
            }
        }

        
    /*  ============================================
        FUNCTION:   leaveAccountGroup
        PARAMS:     ag  - accountgroup to leave
        RETURNS:    boolean
        ============================================  */
        public function leaveAccountGroup($dbc, $ag) {
            
            //is user in account group requested?
            if ($this->inAccountGroup($ag)) {
                
                //if so, try to remove from group in SQL
                try {
                    $sql = "CALL removeUserGroupLink(:uid, :gid)";
                    $qry = $dbc->prepare($sql);
                    $qry->bindValue(":uid", $_SESSION["userobj"]->getUserId());
                    $qry->bindValue(":gid", $ag);
                    $leftOK = $qry->execute();
                } 
                catch (\PDOException $e) {
                    throw new TCASHException('Unable to remove user from accountgroup ' . $ag, $e);
                }
                
                //if successful, re-cache the groups
                $this->refreshAccGroupCache();
                return $leftOK;
                
            } else {
               throw new TCASHException ("Unable to remove user from accountgroup ".$ag, $e); 
            }
        }

        
    /*  ============================================
        FUNCTION:   inAccountGroup
        PARAMS:     ag  - accountgroup to check
        RETURNS:    boolean
        PURPOSE:    checks if the current user is in the named account group
        ============================================  */
        public function inAccountGroup($ag) {
            
            foreach ($this->accountgroups as $a) {
               if ($a["groupid"] == $ag) {
                   return true;
               }
            }
            
            return false;
        }
        
        
        
    /*  ============================================
        FUNCTION:   updateUser (STATIC)
        PARAMS:     dbc - database connection object
                    uid - user id
                    fnm - full name
                    eml - email
                    pwd - old password
                    nwd - new password
                    np2 - new password check
        RETURNS:    (boolean) indicates whether the update worked or not
        ============================================  */
        public static function updateUser($dbc, $uid, $fnm, $eml, $pwd, $npw, $np2) {
        
            //check passwords match
            if ($npw != $np2) {
               throw new TCASHException ("Supplied passwords did not match"); 
            } else {

                if (is_null($npw)) {
                    $npw = $pwd;
                }
                
                //update user details
                try {
                    $sql = "CALL updateUser(:uid, :fnm, :pwd, :eml, :npw)";
                    $qry = $dbc->prepare($sql);
                    $qry->bindValue(":uid", $uid);
                    $qry->bindValue(":fnm", $fnm);
                    $qry->bindValue(":pwd", $pwd);
                    $qry->bindValue(":eml", $eml);
                    $qry->bindValue(":npw", $npw);

                    $rc = $qry->execute(); 
                    
                    if ($qry->rowCount() > 0) {
                        Logger::log(new LogMessage("Updated user details for " . $uid));
                        return true;   
                    } else { 
                        Logger::log(new LogMessage("Failed to update user details for " . $uid));
                        return false;
                    }
                } 
                catch (\PDOException $e) {
                    throw new TCASHException('Unable to update user ' . $uid, $e);
                    exit();
                }
            }
        }

    }