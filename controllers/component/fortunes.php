<?php
/**
 * Copyright (c) 2009-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onyx_Controller_Component_Fortunes extends Onyx_Controller {
    
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

