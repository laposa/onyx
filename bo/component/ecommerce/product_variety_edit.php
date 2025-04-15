<?php
/** 
 * Copyright (c) 2005-2012 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class Onyx_Controller_Bo_Component_Ecommerce_Product_Variety_Edit extends Onyx_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        /**
         * check variety_id input
         */
         
        if (is_numeric($this->GET['id'])) $variety_id = $this->GET['id'];
        else {
            msg("Variety ID is not numeric", 'error');
            return false;
        }
        
        /**
         * initialize
         */
         
        require_once('models/ecommerce/ecommerce_product_variety.php');
        require_once('models/ecommerce/ecommerce_product.php');
        $Product_variety = new ecommerce_product_variety();
        $Product = new ecommerce_product();
        
        $this->tpl->assign('VARIETY_CONF',$Product_variety->conf);
        
        /**
         * update variety
         */
         
        if (isset($_POST['save']) && $_POST['save'] == 'save') {

            if (!isset($_POST['product']['variety']['publish'])) $_POST['product']['variety']['publish'] = 0;
            
            if($id = $Product_variety->updateVariety($_POST['product']['variety'])) {
                msg("Product variety ID {$variety_id} updated.");
                /*onyxGoTo($_SESSION['last_diff'], 2);*/
            } else {
                msg ("Can't update the product variety, is you product code unique?", 'error');
            }
        }
        
        /**
         * get detail
         */
         
        $variety = $Product_variety->getVarietyDetail($variety_id);
        $variety['publish'] = ($variety['publish'] == 1) ? 'checked="checked" ' : '';
        
        //alert if net weight is bigger than gross weight
        if ($variety['weight'] > $variety['weight_gross']) msg("Net weight is bigger than gross weight", "error");
        
        $p = $Product->detail($variety['product_id']);
        
        $p['variety'] = $variety;
        
        $this->tpl->assign('PRODUCT', $p);

        /**
         * display confirmation if notifications are about to be sent out
         */
        require_once('models/common/common_watchdog.php');
        $Watchdog = new common_watchdog();

        $this->tpl->assign('NOTIFICATIONS', array(
            'back_in_stock_customer' => $Watchdog->checkWatchdog('back_in_stock_customer', $variety['id'], 0, 1, true)
        ));

        return true;
    }
}
