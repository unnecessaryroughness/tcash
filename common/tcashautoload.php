<?php 

    spl_autoload_register("tcashAutoload");

/*  ============================================
    FUNCTION:   tcashautoload
    PARAMS:     classname - name of class to load
    RETURNS:    (boolean) false
    PURPOSE:    loads miscellaneous classes 
                from the /tcash/common/classes folder 
    ============================================  */
    function tcashAutoload($classname) {
        $parts = explode("\\", strtolower($classname));
        require $_SERVER['DOCUMENT_ROOT'] . 
            '/tcash/common/classes/' . end($parts) . '.php';
    }
