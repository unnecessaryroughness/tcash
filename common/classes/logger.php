<?php 

    namespace tcash;

    interface LogEngineInterface {
       public function add(array $data); 
    }


    class Logger {

        const LOG_FILE_LOCATION = "/tcash/logs/tcash_log.txt";
                        
    /*  ============================================
        FUNCTION:   log (STATIC)
        PARAMS:     lm - LogMessage class
        RETURNS:    boolean
        ============================================  */
        public static function log(LogMessage $lm) {
            
            $loglocation = $_SERVER["DOCUMENT_ROOT"] . Logger::LOG_FILE_LOCATION;
            
            if ($_SESSION["logging"]) {
                if (!file_put_contents($loglocation, $lm->toString(), FILE_APPEND)) {
                    throw new TCASHException("Error writing to log file", null);
                } else {
                    return true;
                }
            }
        }
        
    }




 
