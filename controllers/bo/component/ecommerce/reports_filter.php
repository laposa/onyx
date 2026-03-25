<?php
/**
 * Copyright (c) 2010-2026 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Bo_Component_Ecommerce_Reports_Filter extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        /**
         * Store submited data to the SESSION
         */
        
        if (is_array($this->GET['reports-filter'])) $_SESSION['bo']['reports-filter'] = $this->GET['reports-filter'];
        
        /**
         * if SESSION is empty, create default values
         */
         
        if (!is_array($_SESSION['bo']['reports-filter'])) {
        
            $_SESSION['bo']['reports-filter'] = array();
            
            $date_range = $this->getDateRange();
            $_SESSION['bo']['reports-filter']['from'] = $date_range['from'];
            $_SESSION['bo']['reports-filter']['to'] = $date_range['to'];
        }
        
        
        /**
         * read from session
         */
         
        $reports_filter = $_SESSION['bo']['reports-filter'];
        
        $this->tpl->assign("SELECTED_$time_frame", "selected='selected'");
        $this->tpl->assign("TIME_FRAME", $time_frame);
        
                
        $this->tpl->assign('REPORTS_FILTER', $reports_filter);

        return true;
    }
    

    /**
     * prepare date range
     */
    static function getDateRange()
    {
        $range = [];
        
        if (is_array($_GET['reports-filter'])) {
            
            $range['from'] = $_GET['reports-filter']['from'];
            $range['to'] = $_GET['reports-filter']['to'];
            
        } else if (is_array($_SESSION['bo']['reports-filter'])) {
            $range['from'] = $_SESSION['bo']['reports-filter']['from'];
            $range['to'] = $_SESSION['bo']['reports-filter']['to'];
        } else {
            //get actual date
            $this_year = date('Y');
            $this_month = date('m');
            
            //get last month
            $previous_month = $this_month - 1;
            if ($previous_month < 1) {
                    $previous_month = "12";
                    $year_previous_month = $this_year - 1;
            } else {
                $year_previous_month = $this_year;
            }
            if ($previous_month < 10) $previous_month = "0$previous_month";
            
            //format
            $range['from'] = "$year_previous_month-$previous_month-01";
            $range['to'] = "$this_year-$this_month-01";
        }

        return $range;
    }
}       
