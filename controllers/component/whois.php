<?php
/**
 * Copyright (c) 2009-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Component_Whois extends Onxshop_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        $str = $this->GET['str'];
        
        if (preg_match("/[^a-zA-Z0-9\.]/", $str)) {
            msg("whois: invalid string");
        } else {
            $result = local_exec("whois {$str}");
            $this->tpl->assign("RESULT", $result);
        }

        return true;
    }
}
