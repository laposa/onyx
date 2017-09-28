<?php
/**
 * Copyright (c) 2010-2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Reports_Filter extends Onxshop_Controller {

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
            
            $latest_month = $this->getLatestMonth();
            $_SESSION['bo']['reports-filter']['from'] = $latest_month['from'];
            $_SESSION['bo']['reports-filter']['to'] = $latest_month['to'];
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
     * get latest month
     */
     
    public function getLatestMonth() {
        
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
        $breakdown['from'] = "$year_previous_month-$previous_month-01";
        $breakdown['to'] = "$this_year-$this_month-01";
    
        return $breakdown;
    }
}       
