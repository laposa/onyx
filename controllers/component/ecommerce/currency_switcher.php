<?php
/** 
 * Copyright (c) 2005-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onyx_Controller_Component_Ecommerce_Currency_Switcher extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('models/international/international_currency.php');
        $Currency = new international_currency();
        
        if ($_POST['client']['customer']['currency_code']) $_SESSION['client']['customer']['currency_code'] = $_POST['client']['customer']['currency_code'];
        else $_SESSION['client']['customer']['currency_code'] = $Currency->conf['default'];
        
        $selected = $_SESSION['client']['customer']['currency_code'];
        
        
        
        $allowed = $Currency->conf['allowed'];
        $allowed_count = count($allowed);
        
        if ($allowed[0] == 'all') {
            $where = '';
        } else {
            $where = "code=";
            for ($i=0; $i<$allowed_count; $i++) {
                if ($i == ($allowed_count-1)) {
                    $where = $where . "'{$allowed[$i]}'";
                } else {
                    $where = $where . "'{$allowed[$i]}' OR code=";
                }
            }
        }
        
        $currencies = $Currency->listing($where,'name ASC');
        
        foreach ($currencies as $c) {
            if ($c['code'] == $selected) $c['selected'] = "selected='selected'";
            else $c['selected'] = '';
            $this->tpl->assign('currency', $c);
            $this->tpl->parse("content.item");
        }

        return true;
    }
}
