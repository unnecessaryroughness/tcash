<?php

    namespace tcash;

    class Reports {
        

    /*  ============================================
        FUNCTION:   runReport_SpendCat 
        PARAMS:     dbc     - database connection
                    acg     - the account group
                    dtf     - the date FROM 
                    dtt     - the date TO
                    acl     - the account list
        RETURNS:    associative array
        ============================================  */
        public static function runReport_SpendCat(\PDO $dbc, $acg, $dtf, $dtt, $acl) {
           
            //call the stored procedure
            try {
                $sql = "CALL rpt_spendingByCat(:acg, :dtf, :dtt, :acl)";
                $qry = $dbc->prepare($sql);
                $qry->bindValue(":acg", $acg);
                $qry->bindValue(":dtf", $dtf);
                $qry->bindValue(":dtt", $dtt);
                $qry->bindValue(":acl", $acl);

                if ($qry->execute()) {            
                    return $qry->fetchall(\PDO::FETCH_ASSOC);
                } else {
                    return false;
                }
            } 
            catch (\PDOException $e) {
                throw new TCASHException('Unable to retrieve report rpt_spendingByCategory', $e);
		        exit();
            }
        }

        
    /*  ============================================
        FUNCTION:   runReport_SpendPayee 
        PARAMS:     dbc     - database connection
                    acg     - the account group
                    dtf     - the date FROM 
                    dtt     - the date TO
                    acl     - the account list
        RETURNS:    associative array
        ============================================  */
        public static function runReport_SpendPayee(\PDO $dbc, $acg, $dtf, $dtt, $acl) {
           
            //call the stored procedure
            try {
                $sql = "CALL rpt_spendingByPayee(:acg, :dtf, :dtt, :acl)";
                $qry = $dbc->prepare($sql);
                $qry->bindValue(":acg", $acg);
                $qry->bindValue(":dtf", $dtf);
                $qry->bindValue(":dtt", $dtt);
                $qry->bindValue(":acl", $acl);

                if ($qry->execute()) {            
                    return $qry->fetchall(\PDO::FETCH_ASSOC);
                } else {
                    return false;
                }
            } 
            catch (\PDOException $e) {
                throw new TCASHException('Unable to retrieve report rpt_spendingByPayee', $e);
		        exit();
            }
        }


    /*  ============================================
        FUNCTION:   runReport_TransByCat 
        PARAMS:     dbc     - database connection
                    acg     - the account group
                    dtf     - the date FROM 
                    dtt     - the date TO
                    acl     - the account list
                    ctl     - the category list
        RETURNS:    associative array
        ============================================  */
        public static function runReport_TransByCat(\PDO $dbc, $acg, $dtf, $dtt, $acl, $ctl) {
           
            //call the stored procedure
            try {
                $sql = "CALL rpt_transbycat(:acg, :dtf, :dtt, :acl, :ctl)";
                $qry = $dbc->prepare($sql);
                $qry->bindValue(":acg", $acg);
                $qry->bindValue(":dtf", $dtf);
                $qry->bindValue(":dtt", $dtt);
                $qry->bindValue(":acl", $acl);
                $qry->bindValue(":ctl", $ctl);

                if ($qry->execute()) {            
                    return $qry->fetchall(\PDO::FETCH_ASSOC);
                } else {
                    return false;
                }
            } 
            catch (\PDOException $e) {
                throw new TCASHException('Unable to retrieve report rpt_transbycat', $e);
		        exit();
            }
        }
        
    }

