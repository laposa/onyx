<?php
/** 
 * Copyright (c) 2010-2016 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 * TODO: fix temp variable for gift wrap product id = 238, variety id = 380
 */

require_once('controllers/component/ecommerce/checkout_gift.php');

class Onyx_Controller_Component_Ecommerce_Checkout_Gift_Wrap extends Onyx_Controller_Component_Ecommerce_Checkout_Gift {

    /**
     * public action
     */
     
    public function mainAction() {

        setlocale(LC_MONETARY, $GLOBALS['onyx_conf']['global']['locale']);
    
        /**
         * get product conf
         */
         
        require_once('models/ecommerce/ecommerce_product.php');
        $ecommerce_product_conf = ecommerce_product::initConfiguration();
        
        /**
         * check gift wrap product ID is set
         */
         
        if (!is_numeric($ecommerce_product_conf['gift_wrap_product_id']) || $ecommerce_product_conf['gift_wrap_product_id']  == 0) {
            
            msg("You need to create ecommerce_product.gift_wrap_product_id conf option to use gift wrap component", 'error');
            
            return true;
        }
        
        /**
         * get gift wrap product detail
         */
         
        $gift_wrap_product_detail = $this->getGiftWrapProductDetail($ecommerce_product_conf['gift_wrap_product_id']);
        
        /**
         * display each option
         */
        
        foreach ($gift_wrap_product_detail['variety'] as $variety) {
            
            /**
             * image
             */
             
            //$_Onyx_Request = new Onyx_Request("component/image~relation=product_variety:node_id={$variety['id']}:limit=0,1~");
            //$this->tpl->assign('IMAGE', $_Onyx_Request->getContent());
            
            $variety['image'] = $this->getImage($variety['id']);
            
            /**
             * assign to template
             */
             
            $this->tpl->assign('ITEM', $variety);
            
            /**
             * check if gift wrap is in the basket
             */
             
            $gift_selected= $this->checkGiftWrapSelected($variety['id']);
            
            /**
             * display checked gift wrap
             */
    
            if ($gift_selected) {
                $this->tpl->assign("CHECKED_gift_wrap", "checked='checked'");
            } else {
                $this->tpl->assign("CHECKED_gift_wrap", "");
            }
            
            $this->tpl->parse('content.item');
        }
        
        /**
         * display virtual product option
         */
         
        if ($this->isBasketVirtualProductOnly()) $this->tpl->parse('content.virtual_product');

        setlocale(LC_MONETARY, LOCALE);

        return true;
    }
    
    /**
     * get gift wrap product detail
     */
    
    public function getGiftWrapProductDetail($gift_wrap_product_id) {
        
        $Product = new ecommerce_product();
        
        $gift_wrap_product_detail = $Product->getProductDetail($gift_wrap_product_id);
        
        return $gift_wrap_product_detail;
                
    }
    
        
    /**
     * check if gift wrap is in basket
     */
     
    public function checkGiftWrapSelected($variety_id) {
        
        require_once('models/ecommerce/ecommerce_basket.php');
        $Basket = new ecommerce_basket();
        $Basket->setCacheable(false);
        
        $variety_id_list = $Basket->getContentItemsVarietyIdList($_SESSION['basket']['id']);
        
        if (in_array($variety_id, $variety_id_list)) return true;
        else return false;
    }
    
    /**
     * get image
     */
     
    public function getImage($variety_id) {
    
        require_once('models/ecommerce/ecommerce_product_variety_image.php');
        $Image = new ecommerce_product_variety_image();
        
        $image_list = $Image->listing("node_id=$variety_id", "priority DESC, id ASC");
        
        return $image_list[0];
                
    }
}
