<?php
/** 
 * Copyright (c) 2008-2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Bo_Component_Ecommerce_Stock_Edit extends Onyx_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('models/ecommerce/ecommerce_product_variety.php');
        require_once('models/ecommerce/ecommerce_product.php');
        $Product_variety = new ecommerce_product_variety();
        $Product = new ecommerce_product();
        
        $this->tpl->assign('VARIETY_CONF',$Product_variety->conf);
        
        $Product_variety->set('id', $this->GET['id']);
        
        if (isset($_POST['save']) && $_POST['save'] == 'save') {
            
            if($id = $Product_variety->updateVariety($_POST['product']['variety'])) {
                msg("Product variety updated.");
                /*onyxGoTo($_SESSION['last_diff'], 2);*/
            } else {
                msg ("Can't add the product variety, is you product SKU unique?");
            }
        }
        
        $variety = $Product_variety->getVarietyDetail($this->GET['id']);
        $variety['publish'] = ($variety['publish'] == 1) ? 'checked="checked" ' : '';
        
        $p = $Product->detail($variety['product_id']);
        
        $p['variety'] = $variety;
        
        $this->tpl->assign('PRODUCT', $p);

        /**
         * display confirmation if notifications are about to be sent out
         */
        require_once('models/common/common_watchdog.php');
        $Watchdog = new common_watchdog();

        $this->tpl->assign('NOTIFICATIONS', array(
            'back_in_stock_customer' => $Watchdog->checkWatchdog('back_in_stock_customer', $variety_id, 0, 1, true)
        ));

        return true;
    }
}
