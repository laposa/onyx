<?php
/**
 * Copyright (c) 2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/component/ecommerce/gift_voucher.php');

class Onxshop_Controller_Component_Ecommerce_Gift_Voucher_Specify extends Onxshop_Controller_Component_Ecommerce_Gift_Voucher {

    /**
     * custom action
     */
     
    public function customAction($data) {
        
        $this->displayVarieties($data);
        
        if (is_array($_POST['gift_voucher_specify'])) {
            
            $this->tpl->assign('GIFT_VOUCHER', $data);
            
            if ($this->validateData($data)) {
                $this->addToBasket($data);
            }
        }
        
        return true;
    }
    
    /**
     * displayVarieties
     */
     
    public function displayVarieties($data) {
    
        /**
         * display each option
         */
        
        foreach ($this->gift_voucher_product_detail['variety'] as $variety) {
            
            /**
             * image
             */
             
            //$_Onxshop_Request = new Onxshop_Request("component/image~relation=product_variety:node_id={$variety['id']}:limit=0,1~");
            //$this->tpl->assign('IMAGE', $_Onxshop_Request->getContent());
            
            //$variety['image'] = $this->getImage($variety['id']);
            
            /**
             * assign to template
             */
             
            $this->tpl->assign('ITEM', $variety);
            
            /**
             * check if gift wrap is in the basket
             */
             
            //$gift_selected= $this->checkGiftVoucherSelected($variety['id']);
            
            /**
             * display checked gift voucher
             */
    
            if ($data['variety_id'] == $variety['id']) {
                $this->tpl->assign("SELECTED", "selected='selected'");
            } else {
                $this->tpl->assign("SELECTED", "");
            }
            
            $this->tpl->parse('content.item');
        }
    
    }
    
    /**
     * addToBasket
     */
     
    public function addToBasket($data) {
    
        $_POST['add'] = $data['variety_id'];
        $_POST['quantity'] = 1;
        $_POST['other_data'] = array();
        $_POST['other_data']['recipient_name'] = $data['recipient_name'];
        $_POST['other_data']['recipient_email'] = $data['recipient_email'];
        $_POST['other_data']['message'] = $data['message'];
        $_POST['other_data']['sender_name'] = $data['sender_name'];
        if (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}/", $data['delivery_date'])) $_POST['other_data']['delivery_date'] = $data['delivery_date'];
        
        //will be handled by global basket
        $_Onxshop_Request = new Onxshop_Request('component/ecommerce/basket');
        OnxshopGoTo("/checkout/basket");
    }
}
