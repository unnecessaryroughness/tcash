<?php

    namespace tcash;

    class TCASHException extends \Exception {
        
        public $tcashErrorMsg;
        public $sourceError;
        
        
    /*  ============================================
        FUNCTION:   __construct
        PARAMS:     te - tcash error string
                    se - source error string
        RETURNS:    TCASHException object
        ============================================  */
        public function __construct($te, $se=null) {
            $this->tcashErrorMsg = $te;
            $this->sourceError = $se;
        }

    /*  ============================================
        FUNCTION:   displayOutput
        PARAMS:     (none)
        RETURNS:    string - formatted output
        ============================================  */
        public function displayOutput() {
            if ($_SESSION["devmode"]) {
                $retVal = $this->tcashErrorMsg . "#@#" . $this->sourceError;
            } else {
                $retVal = $this->tcashErrorMsg;
            }
            return $retVal;
        }
    }

