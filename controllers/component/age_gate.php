<?php
/**
 * Copyright (c) 2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Age_Gate extends Onxshop_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {

        if (is_numeric($this->GET['min_age'])) $min_age = $this->GET['min_age'];
        else $min_age = 18;

        $birthday = array(
            'year' => date("Y"),
            'month' => date("m"),
            'day' => date("d")
        );

        if ($_SESSION['client']['customer']['birthday']) {
            $time = strtotime($_SESSION['client']['customer']['birthday']);
            if (is_numeric($time)) {
                $birthday = array(
                    'year' => date("Y", $time),
                    'month' => date("m", $time) - 1,
                    'day' => date("d", $time)
                );
            }
        }
        
        $this->tpl->assign('BIRTHDAY', $birthday);
        $this->tpl->assign('MIN_AGE', $min_age);

        return true;
    
    }

}

