<?php
/**
 * Invoice detail controller
 *
 * Copyright (c) 2005-2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Ecommerce_Invoice extends Onxshop_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        /**
         * check GET.id
         */
         
        if (is_numeric($this->GET['id'])) {
            
            $order_id = $this->GET['id'];
            
        } else {
        
            msg("component/ecommerce/invoice: GET.id is not numeric", 'error');
            return false;
        
        }
        
        /**
         * initialize
         */
         
        require_once('models/ecommerce/ecommerce_invoice.php');
        require_once('models/ecommerce/ecommerce_order.php');
        
        $Invoice = new ecommerce_invoice();
        $Order = new ecommerce_order();
        $Invoice->setCacheable(false);
        $Order->setCacheable(false);
        
        $this->tpl->assign('CONF', $Invoice->conf);
        
        /**
         * get order data
         */
         
        $order_data = $Order->getOrder($order_id);
        
        /** 
         * check owner
         */

        //security check of the owner
        $is_owner = $order_data['basket']['customer_id'] == $_SESSION['client']['customer']['id'];
        $is_bo_user = Onxshop_Bo_Authentication::getInstance()->isAuthenticated();
        $is_guest_user = $order_data['client']['customer']['status'] == 5;
        $is_same_session = $order_data['php_session_id'] == session_id() || $order_data['php_session_id'] == $this->GET['php_session_id'];
        $has_code = !empty($this->GET['code']) && verifyHash($order_data['id'], $this->GET['code']);

        if ($is_bo_user || $is_owner || $is_guest_user && $is_same_session || $has_code) {
        
            /**
             * check dift option
             */
             
            if ($order_data['other_data']['delivery_options']['other_data']['gift'] == 1 || $order_data['other_data']['gift'] == 1) {
            
                $this->tpl->parse('content.gift');
            
            }
        
            /**
             * display appropriate carrier logo
             */
             
            $carrier_id = $order_data['other_data']['delivery_options']['carrier_id'];
            $this->tpl->parse("content.type.carrier_id_{$carrier_id}");
            $this->tpl->parse('content.type');
            
            /**
             * get invoice details
             */
             
            $invoice_data = $Invoice->getInvoiceForOrder($this->GET['id']);
            
        
            /**
             * other data
             */
             /*
            $order_data['other_data'] = unserialize($order_data['other_data']);
            
            if (is_array($order_data['other_data'])) {
            
                foreach ($order_data['other_data'] as $key=>$value) {
                    //format
                    $key = preg_replace("/required_/","",$key);
                    $key = preg_replace("/_/"," ",$key);
                    $key = ucfirst($key);
            
                    $note['key'] = $key;
                    $note['value'] = nl2br($value);
                    if ($note['value'] != '') {
                        $this->tpl->assign('OTHER_DATA', $note);
                        $this->tpl->parse('content.other_data.item');
                        $show_other_data = 1;
                    }
                }
                
                if ($show_other_data == 1) $this->tpl->parse('content.other_data');
            }
            */
        
            //$invoice_data['created'] = strftime('%d/%m/%Y', strtotime($invoice_data['created']));
            
            if (empty($invoice_data['basket_detail_enhanced'])) $invoice_data['basket_detail_enhanced'] = $invoice_data['basket_detail'];
            $this->tpl->assign('INVOICE', $invoice_data);
            $this->tpl->assign('ORDER', $order_data);
            
            if ($Invoice->conf['company_logo'] != '') $this->tpl->parse('content.logoimage');
            else $this->tpl->parse('content.logotypo');
            
        } else {

            msg('unauthorized access to view order detail');

        }

        return true;
    }
}
