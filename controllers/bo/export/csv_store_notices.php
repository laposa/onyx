<?php
/** 
 * Copyright (c) 2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */
 
require_once('controllers/bo/export/csv.php');
require_once('models/ecommerce/ecommerce_store.php');

class Onxshop_Controller_Bo_Export_CSV_Store_Notices extends Onxshop_Controller_Bo_Export_CSV {

    /**
     * main action
     */
     
    public function mainAction() {
        
        set_time_limit(0);
        
        
        $Store = new ecommerce_store();
        
        /**
         * Get the list
         */
        
        $date_from = $this->GET['date_from'];
        $date_to = $this->GET['date_to'];

        $records = $Store->getDataForNoticesReport($date_from, $date_to);
        $this->commonCSVAction($records, 'store_notices');

        return true;
    }
}
