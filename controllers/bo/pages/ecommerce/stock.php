<?php
/**
 *
 * Copyright (c) 2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Bo_Pages_Ecommerce_Stock extends Onxshop_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
    

        $this->parseNotificationsExport();
                
        return true;
    }

    public function parseNotificationsExport()
    {
        $month = date("m");
        $year = date("Y");

        for ($i = 0; $i < 24; $i++) {

            $time = strtotime("$year-$month-01");

            $date = array(
                "from" => date("Y-m-01", $time),
                "to" => date("Y-m-t", $time),
                "label" => date("m/Y", $time),
            );

            $this->tpl->assign("DATE", $date);
            $this->tpl->parse("content.stock_period");

            $month--;
            if ($month == 0) {
                $year--;
                $month = 12;
            }

        }
    }

}
