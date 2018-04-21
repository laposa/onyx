<?php
/**
 * Copyright (c) 2009-2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Component_Fortunes extends Onxshop_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        $result = local_exec('fortune');
        
        $quote = array();
        $quote['text'] = $result;
        
        $this->tpl->assign("QUOTE", $quote);

        return true;
    }
}

