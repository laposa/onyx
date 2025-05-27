<?php
/** 
 * Copyright (c) 2005-2021 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */
 
require_once('controllers/component/ecommerce/basket.php');

class Onyx_Controller_Component_Ecommerce_Basket_Edit extends Onyx_Controller_Component_Ecommerce_Basket {

    /**
     * main action
     */
     
    public function mainAction() {
    
        if ($this->GET['node_id'] && isset($this->GET['code']) && $this->GET['code']) {
        
            // if basket was shared associate helper CSS class
            $this->tpl->assign('CSS_CLASS_SHARED', 'shared');
        
        }

        $this->tpl->assign('BASKET_CONTINUE_SHOPPING_URL', '/');
                
        return parent::mainAction();
        
    }
        
}
