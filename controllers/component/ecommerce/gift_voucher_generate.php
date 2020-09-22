<?php
/**
 * Copyright (c) 2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/component/ecommerce/gift_voucher.php');

class Onxshop_Controller_Component_Ecommerce_Gift_Voucher_Generate extends Onxshop_Controller_Component_Ecommerce_Gift_Voucher {

    /**
     * main action
     */
     
    public function mainAction() {
        
        if (!is_numeric($this->GET['order_id'])) {
            msg("Onxshop_Controller_Component_Ecommerce_Gift_Voucher_Generate: order_id isn't numeric");
            return false;
        }
        
        $order_id = $this->GET['order_id'];
        
        if ($gift_voucher_product_id = $this->getGiftVoucherProductId()) {
        
            /**
             * get order detail
             */
             
            require_once('models/ecommerce/ecommerce_order.php');
            $EcommerceOrder = new ecommerce_order();
            $EcommerceOrder->setCacheable(false);
            
            $order_detail = $EcommerceOrder->getFullDetail($order_id);

            /**
             * find if the order contains gift
             */
            
            if ($voucher_basket_items = $this->getVoucherBasketItems($order_detail, $gift_voucher_product_id)) {
            
                return $this->generateVouchers($voucher_basket_items);
                
            }
            
        }
        
        return true;
    }
    
    /**
     * getVoucherBasketItems
     */
     
    public function getVoucherBasketItems($order_detail, $gift_voucher_product_id) {
        
        if (!is_array($order_detail)) return false;
        if (!is_numeric($gift_voucher_product_id)) return false;
        
        $voucher_basket_items = array();
        
        foreach ($order_detail['basket']['items'] as $basket_item) {
            
            if ($basket_item['product']['id'] == $gift_voucher_product_id) {
                $voucher_basket_items[] = $basket_item;
            }
            
        }
        
        if (count($voucher_basket_items) > 0) return $voucher_basket_items;
        else return false;
        
    }
    
    /**
     * generateVouchers
     */
     
    public function generateVouchers($voucher_basket_items) {
    
        if (!is_array($voucher_basket_items)) return false;
        
        foreach ($voucher_basket_items as $item) {
            
            $this->generateSingleVoucher($item);
            
        }
        
        return true;
    }
    
    /**
     * generateSingleVoucher
     */
    
    public function generateSingleVoucher($voucher_basket_item) {
        
        if (!is_array($voucher_basket_item)) return false;
        
        $voucher_data = array();
        $voucher_data['variety_id'] = $voucher_basket_item['product_variety_id'];
        $voucher_data['recipient_name'] = $voucher_basket_item['other_data']['recipient_name'];
        $voucher_data['recipient_email'] = $voucher_basket_item['other_data']['recipient_email'];
        $voucher_data['message'] = $voucher_basket_item['other_data']['message'];
        $voucher_data['sender_name'] = $voucher_basket_item['other_data']['sender_name'];
        if ($voucher_basket_item['other_data']['delivery_date']) $voucher_data['delivery_date'] = $voucher_basket_item['other_data']['delivery_date'];
        
        if (!$this->validateData($voucher_data)) {
        
            msg("Voucher data are not valid", 'error');
            return false;
            
        }
        
        /**
         * create discount code
         */
        $code_pattern_base = "GIFT-{$voucher_basket_item['id']}" . '-';
        $promotion_data = array();
        $promotion_data['type'] = 4; // Gift Voucher
        $promotion_data['code_pattern'] = $code_pattern_base . $this->randomCode();
        $promotion_data['title'] = $promotion_data['code_pattern'];
        $promotion_data['discount_percentage_value'] = 0;
        $promotion_data['discount_fixed_value'] = $voucher_basket_item['total'];
        $promotion_data['uses_per_coupon'] = $voucher_basket_item['quantity'];
        $promotion_data['other_data'] = $voucher_basket_item['other_data'];
        $promotion_data['publish'] = 1;
        $promotion_data['generated_by_order_id'] = $this->GET['order_id'];
        
        require_once('models/ecommerce/ecommerce_promotion.php');
        $Promotion = new ecommerce_promotion();
        $Promotion->setCacheable(false);
        
        //TODO: check if the code wasn't generated before for the same order
        if ($Promotion->checkCodeMatchPartially($code_pattern_base)) {
            msg("Code {$code_pattern_base}* was previously generated", 'error');
            return false;
        }
        //preg_match("/GIFT-{$voucher_basket_item['id']}/", $all_patterns_list)
        
        if ($promotion_id = $Promotion->addPromotion($promotion_data)) {
            msg("Promotion code {$promotion_data['code_pattern']} generated as promotion ID $promotion_id", 'ok', 1);
        } else {
            msg('Promotion code generation failed', 'error');
            //return false;
        }

        /**
         * create the voucher file
         */
         
        $url = "http://{$_SERVER['SERVER_NAME']}/request/sys/html5.node/site/print.component/ecommerce/gift_voucher~voucher_code={$promotion_data['code_pattern']}~";
        $gift_voucher_directory = ONXSHOP_PROJECT_DIR . "var/vouchers/";
        $gift_voucher_filename = "{$promotion_data['code_pattern']}.png";
        $gift_voucher_filename_fullpath = $gift_voucher_directory . $gift_voucher_filename;
        
        //check directory exits
        if (!is_dir($gift_voucher_directory)) mkdir($gift_voucher_directory);
        
        $shell_command = "wkhtmltoimage $url $gift_voucher_filename_fullpath";
        
        if ($result = local_exec($shell_command)) {
            msg("File $gift_voucher_filename_fullpath generated by wkhtmltoimage", 'ok', 1);
        }
        
        /**
         * send email
         * postpone if delivery_date is set
         */
        
        if (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}/", $voucher_data['delivery_date'])) 
            $this->postponeDelivery($promotion_id, $voucher_data['delivery_date']);
        else 
            $this->sendEmail($promotion_data, $voucher_data, $gift_voucher_filename);
        
        return true;
    }
    
    /**
     * generate random code
     */

    public function randomCode($size = 4) {

        //omit "o" letter to avoid mistakes
        $hash= array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","p","q","r","s","t","u","v","w","x","y","z");

        $code="";

        for ($i=0 ;$i<=$size-1 ;$i++) {
            $random=rand(0, count($hash)-1);
            $code.=$hash[$random];
        }
        
        $code = strtoupper($code);
        
        return $code;
    }
    
    /**
     * postpone delivery
     */
    
    public function postponeDelivery($promotion_id, $date)
    {
        $date = explode("/", $date);
        $time = strtotime("{$date[2]}-{$date[1]}-{$date[0]}");

        $time += 7 * 3600; // 7am

        $data = array(
            'parameters' => "promotion_id=$promotion_id",
            'controller' => "gift_voucher_send",
            'scheduled_time' => $time,
        );

        require_once('models/common/common_scheduler.php');
        $Scheduler = new common_scheduler();
        $Scheduler->scheduleNewJob($data);

        return true;
    }
    
}
