<?php

/*
	================================================================================
	TODO: 

    ================================================================================
*/

    namespace tcash;

    class ReportDisplay {
       
        public $screenTitle;
        public $screenData;
        public $screenFields;
        public $screenTotals;
        public $chart;
        public $returnURL;
        
     /*  ============================================
        FUNCTION:   __construct
        PARAMS:     st      - screen title
                    sd      - screen data
                    rURL    - return URL
        RETURNS:    (none)
        ============================================  */
       public function __construct($st="", $sd=array(), $rURL="/tcash/reports/") {
            $this->screenTitle = $st;
            $this->screenData = $sd;
            $this->returnURL = $rURL;
        }
       
     /*  ============================================
        FUNCTION:   addScreenField
        PARAMS:     fn      - the field name to show
                    fc      - the field caption to show
                    cl      - the CSS class to use
        RETURNS:    (object) ReportDisplay class
        ============================================  */
        public function addScreenField($fn, $fc, $cl="") {
            $this->screenFields[] = array("field" => $fn, "caption" => $fc, "class" => $cl); 
            return $this;
        }
        

     /*  ============================================
        FUNCTION:   addScreenTotal
        PARAMS:     fn      - the name of the field to total
                    tt      - the type of running total (sum, cnt, avg)
        RETURNS:    (object) ReportDisplay class
        ============================================  */
        public function addScreenTotal($fn, $tt) {
            $this->screenTotals[] = array("field" => $fn, 
                                          "totaltype" => $tt, 
                                          "sum" => 0,
                                          "cnt" => 0); 
            return $this;
        }
        
        
     /*  ============================================
        FUNCTION:   addChart
        PARAMS:     ct      - chart type (pie, bar, area)
                    xaxis   - the name of the x axis field
                    yaxis   - the name of the y axis field
        RETURNS:    (object) ReportDisplay class
        ============================================  */
        public function addChart($ct, $xaxis, $yaxis=null) {
            $this->chart = array("type" => $ct, 
                                      "xaxis" => $xaxis, 
                                      "yaxis" => $yaxis); 
            return $this;
        }
        

    /*  ============================================
        FUNCTION:   getChartFieldHeadingsJS
        PARAMS:     (none)
        RETURNS:    (string) javascript string for defining chart fields
        ============================================  */
        public function getChartFieldHeadingsJS() {
            $rtn = "data.addColumn(";
            
            if ($this->chart["xaxis"]) {

                if (is_numeric($this->screenData[1][$this->chart["xaxis"]])) {
                   $rtn .= "'number', ";
                } else {
                   $rtn .= "'string', ";
                }
               
                foreach ($this->screenFields as $fld) {
                    if ($fld["field"] == $this->chart["xaxis"]) {
                        $rtn .= "'" . $fld["caption"] . "'";
                    }
                }
            }
            
            $rtn .= "); ";
            
            if ($this->chart["yaxis"]) {
                
                $rtn .= "data.addColumn(";
                
                if (is_numeric($this->screenData[1][$this->chart["yaxis"]])) {
                    $rtn .= "'number', ";
                } else {
                    $rtn .= "'string', ";
                }
               
                foreach ($this->screenFields as $fld) {
                    if ($fld["field"] == $this->chart["yaxis"]) {
                        $rtn .= "'" . $fld["caption"] . "'";
                    }
                }
            
               $rtn .= "); ";
            }
            
            return $rtn;
        }
        
        
     /*  ============================================
        FUNCTION:   getChartFieldsJS
        PARAMS:     (none)
        RETURNS:    (string) javascript string for defining chart fields
        ============================================  */
        public function getChartFieldsJS() {
            $rtn = "[";
            $reccnt = 0;
            foreach ($this->screenData as $row) {
                if ($reccnt > 0) {
                    $rtn .= ",";
                }

                $rtn .= "[";

                if ($this->chart["xaxis"]) {
                    if (is_numeric($row[$this->chart["xaxis"]])) {
                        $val = $row[$this->chart["xaxis"]];
                        $rtn .= ($val <0 ? -$val : $val);
                    } else {
                        $rtn .= "'" . $row[$this->chart["xaxis"]] . "'";
                    }
                }
            
                if ($this->chart["yaxis"]) {
                    $rtn .= ", ";
                    if (is_numeric($row[$this->chart["yaxis"]])) {
                        $val = $row[$this->chart["yaxis"]];
                        $rtn .= ($val <0 ? -$val : $val);
                    } else {
                        $rtn .= "'" . $row[$this->chart["yaxis"]] . "'";
                    }
                }
                
                $rtn .= "]";
                $reccnt += 1;
            }
            
            $rtn .= "]";
            
            return $rtn;
        }
        
        
     /*  ============================================
        FUNCTION:   getTotal
        PARAMS:     fn      - return the screenTotals array entry where 
                              the 'field' parameter matches $fn
        RETURNS:    associative array
        PURPOSE:    Retrieves the total field data from the underlying array
        ============================================  */
        public function getTotal($fn) {
            if ($this->screenTotals) {
                foreach ($this->screenTotals as &$total) {
                   if ($total["field"] == $fn) {
                       return $total;
                   }
                }
            }
            return false;
        }
        
        
     /*  ============================================
        FUNCTION:   updateTotal
        PARAMS:     fn      - total field name to update
                    tt      - total field type to update
                    val     - value to ADD TO the total
        RETURNS:    boolean
        PURPOSE:    adds the supplied amount to the existing total 
        ============================================  */
        public function updateTotal($fn, $tt, $val) {
            foreach ($this->screenTotals as &$total) {
               if ($total["field"] == $fn) {
                   $total[$tt] += $val;
               }
            }
            unset($total);
            return false;
        }
        

     /*  ============================================
        FUNCTION:   getTotalVal
        PARAMS:     fn      - the field for which to retrieve the total value
        RETURNS:    float(n,2) 
        PURPOSE:    Returns the current total value for the field specified
        ============================================  */
        public function getTotalVal($fn) {
            
            //check a total for this field is being tracked
            $total = $this->getTotal($fn);
            
            if ($total) {
                if ($total["totaltype"]=="sum") {
                    return number_format(
                            (float)$total["sum"], 2, ".", ",");
                } elseif ($total["totaltype"]=="cnt") {
                    return $total["cnt"];
                } elseif ($total["totaltype"]=="avg") {
                    return number_format(
                            (float)$total["sum"] / $total["cnt"], 2, ".", ",");
                } else {
                    return 0;
                }
            }
        }
                
        
     /*  ============================================
        FUNCTION:   incrementTotal
        PARAMS:     fn      - the field for which to increment the total value
                    val     - the amount to ADD TO the supplied total
        RETURNS:    (none)
        PURPOSE:    adds the supplied value to the supplied field name
        ============================================  */
        public function incrementTotal($fn, $val) {
            
            //check a total for this field is being tracked
            $total = $this->getTotal($fn);
            
            if ($total) {
                if ($total["totaltype"]=="sum") {
                    $this->updateTotal($fn, "sum", $val);
                } elseif ($total["totaltype"]=="cnt") {
                    $this->updateTotal($fn, "cnt", 1);
                } elseif ($total["totaltype"]=="avg") {
                    $this->updateTotal($fn, "sum", $val);
                    $this->updateTotal($fn, "cnt", 1);
                } else {
                    //do nothing
                }
            }
        }
    }