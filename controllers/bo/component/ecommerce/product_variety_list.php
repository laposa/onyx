<?php
/** 
 * Copyright (c) 2005-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onyx_Controller_Bo_Component_Ecommerce_Product_Variety_List extends Onyx_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        /**
         * include variety confg
         */
        require_once('models/ecommerce/ecommerce_product_variety.php');
        $variety_conf = ecommerce_product_variety::initConfiguration();
        $this->tpl->assign('VARIETY_CONF',$variety_conf);
        
        /**
         * product
         */
        require_once('models/ecommerce/ecommerce_product.php');
        $Product = new ecommerce_product();
        
        $product = $Product->getProductDetail($this->GET['id']);

        if (!$product_data) {
            return false;
        }
        
        if (is_array($product['variety'])) {
            foreach ($product['variety'] as $variety) {
                if  ($variety['publish'] == 0) $this->tpl->assign('DISABLED', 'disabled');
                else $this->tpl->assign('DISABLED', '');
                
                $Image = new Onyx_Request("component/image&relation=product_variety&node_id={$variety['id']}");
                $this->tpl->assign('IMAGE', $Image->getContent());
                $this->tpl->assign('VARIETY', $variety);
                $this->tpl->parse('content.list.variety');
            }
        } else {
            msg('This product has no variety.');
        }

        $this->tpl->parse('content.list');

        return true;
    }
}
