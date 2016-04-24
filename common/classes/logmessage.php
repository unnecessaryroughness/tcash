<?php 

    namespace tcash;


    class LogMessage {
        
        protected $readable;
        protected $source;
        protected $datetime;
        protected $user;
        protected $url;

    /*  ============================================
        FUNCTION:   __construct
        PARAMS:     r - readable message
                    s - source message
        RETURNS:    none
        ============================================  */
        public function __construct($r="no message", $s="") {
            $this->readable = $r;
            $this->source = $s;
            $this->datetime = date("Y-m-d H:i:s");
            $this->user = (!isset($_SESSION["userobj"]) ? "[anon]" : $_SESSION["userobj"]->getUserId());
            $this->url = (!isset($_SERVER["REQUEST_URI"])? "[no url]" :$_SERVER["REQUEST_URI"]);
        }

    /*  ============================================
        FUNCTION:   toString
        PARAMS:     (none)
        RETURNS:    string
        ============================================  */
        public function toString() {
           return $this->datetime . " | " . 
                    $this->user . " | " . 
                    $this->url . " | " . 
                    "[" . $this->readable . "] | " . 
                    "[" . $this->source . "]" . PHP_EOL;
        }
        
    }

