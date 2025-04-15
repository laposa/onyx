<?php
/** 
 * Copyright (c) 2010-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/component/ecommerce/checkout.php');

class Onyx_Controller_Component_Ecommerce_Checkout_Gift extends Onyx_Controller_Component_Ecommerce_Checkout {

    /**
     * public action
     */
     
    public function mainAction() {
    
        /**
         * input data submitted by user or read from SESSION
         */
        
        if ($_POST['gift_submit']) {
            
            if ($_POST['gift'] == 'on' || $_POST['gift'] == 1) $gift = 1;
            else $gift = 0;
            
            $gift_message = trim($_POST['gift_message']);
            
        } else {
            
            if (is_numeric($_SESSION['gift'])) $gift = $_SESSION['gift'];
            else $gift = 0;
            
            if ($_SESSION['gift_message'] != '') $gift_message = $_SESSION['gift_message'];
            else $gift_message = '';
            
        }
                
        /**
         * save gift option and message
         */
        
        $_SESSION['gift'] = $gift;
        $_SESSION['gift_message'] = $gift_message;
        
        /**
         * display checked gift option
         */

        if ($gift == 1) {
            $this->tpl->assign("CHECKED_gift", "checked='checked'");
        } else {
            $this->tpl->assign("CHECKED_gift", "");
        }
        
        /**
         * display gift message
         */
        
        $this->tpl->assign('GIFT_MESSAGE', $gift_message);
        
        /**
         * display virtual product option
         */
         
        if ($this->isBasketVirtualProductOnly()) $this->tpl->parse('content.virtual_product');


        return true;
    }
    
    
}
