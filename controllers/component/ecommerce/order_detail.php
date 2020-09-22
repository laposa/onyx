<?php
/**
 * Order detail controller
 *
 * Copyright (c) 2005-2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onyx_Controller_Component_Ecommerce_Order_Detail extends Onyx_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('models/ecommerce/ecommerce_order.php');
        $Order = new ecommerce_order();
        $Order->setCacheable(false);
        
        if (is_numeric($this->GET['order_id'])) {
            $order_id = $this->GET['order_id'];
        } else {
            msg('Order Detail: Missing order_id', 'error');
            return false;
        }

        /**
         * security code to allow unlogged users to pay for the order and view their invoice
         */
        $this->tpl->assign('ORDER_CODE', makeHash($this->GET['order_id']));

        /**
         * include node configuration
         */
        
        require_once('models/common/common_node.php');
        $node_conf = common_node::initConfiguration();
        $this->tpl->assign('NODE_CONF', $node_conf);
        
        /**
         * get detail
         */
         
        $order_data = $Order->getOrder($order_id);
        
        //security check of the owner
        $is_owner = $order_data['basket']['customer_id'] == $_SESSION['client']['customer']['id'];
        $is_bo_user = Onyx_Bo_Authentication::getInstance()->isAuthenticated();
        $is_guest_user = $order_data['client']['customer']['status'] == 5;
        $is_same_session = $order_data['php_session_id'] == session_id() || $order_data['php_session_id'] == $this->GET['php_session_id'];
        $has_code = !empty($this->GET['code']) && verifyHash($order_data['id'], $this->GET['code']);

        if ($is_bo_user || $is_owner || $is_guest_user && $is_same_session || $has_code) {
                
            /**
             * display Make Payment if appropriate
             */
            if ($Order->checkOrderStatusValidForPayment($order_data['status'])) $this->tpl->parse('content.make_payment');
            
            /**
             * get address detail
             */
             
            $_Onyx_Request = new Onyx_Request("component/client/address~invoices_address_id={$order_data['invoices_address_id']}:hide_button=1~");
            $this->tpl->assign("ADDRESS_INVOICES", $_Onyx_Request->getContent());
            
            $_Onyx_Request = new Onyx_Request("component/client/address~delivery_address_id={$order_data['delivery_address_id']}:hide_button=1~");
            $this->tpl->assign("ADDRESS_DELIVERY", $_Onyx_Request->getContent());
        
            /**
             * basket detail
             * if the order is payed, display HTML basket from the invoice, otherwise generate on the fly
             */
            
            require_once('models/ecommerce/ecommerce_invoice.php');
            $Invoice = new ecommerce_invoice();
            $Invoice->setCacheable(false);
            $invoice_data = $Invoice->getInvoiceForOrder($order_data['id']);

            if ($invoice_data) { 
                $this->tpl->assign("BASKET_DETAIL", $invoice_data['basket_detail']);
                $this->tpl->parse("content.print_invoice");
            } else {
                $_Onyx_Request = new Onyx_Request("component/ecommerce/basket_detail~id={$order_data['basket_id']}:order_id={$order_id}:delivery_address_id={$order_data['delivery_address_id']}:delivery_options[carrier_id]={$order_data['other_data']['delivery_options']['carrier_id']}~");
                $this->tpl->assign("BASKET_DETAIL", $_Onyx_Request->getContent());
            }
        
            //other data
            /* don't show
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
            $order_data['created'] = strftime('%d/%m/%Y', strtotime($order_data['basket']['created']));
            
            $this->tpl->assign('ORDER', $order_data);

        } else {
            msg('unauthorised access to view order detail', 'error');
        }

        return true;
    }
}

