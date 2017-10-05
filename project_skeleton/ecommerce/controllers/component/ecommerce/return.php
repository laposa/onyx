<?php
/** 
 * Copyright (c) 2012 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Ecommerce_Return extends Onxshop_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        /**
         * get active customer id and continue only it's available
         */
         
        $customer_id = $this->getActiveCustomerId();

        if (!is_numeric($customer_id)) {
            msg('component/ecommerce/return: login required', 'error');
            onxshopGoTo("/");
            return false;
        }
        
        /**
         * get order id from GET or POST input
         */
         
        if (is_numeric($this->GET['return']['order_id'])) $order_id = $this->GET['return']['order_id'];
        else if (is_numeric($_POST['return']['order_id'])) $order_id = $_POST['return']['order_id'];
        else $order_id = false;
        
        if (is_array($_POST['return']['items'])) $return_data = $this->filterInput($_POST['return']);
        else $return_data = false;
        
        /**
         * get list of previous orders
         */
         
        $orders_list = $this->getOrdersList($customer_id);
        
        /**
         * display previous orders
         */
         
        $this->displayPreviousOrders($orders_list, $order_id);
        
        /**
         * if return is submitted than try to submit
         */
        
        if (is_array($return_data['items'])) {
            
            
            
            if ($this->isValidReturn($return_data)) {
            
                /**
                 * process return
                 */
                 
                if (!$this->processReturn($return_data)) {
                    
                    /**
                     * show full form if not successfull
                     */
                     
                    $this->displayFullForm($order_id, $return_data);
            
                }
                
            } else {
            
                /**
                 * show full form
                 */
                 
                $this->displayFullForm($order_id, $return_data);
            }
        
        } else if (is_numeric($order_id)) {
            
            if (!$this->displayFullForm($order_id, $return_data)) {
            
                $this->displayOrderSelectForm();
                
            }
            
        } else {
        
            //just display the form to allow to select an order
            $this->displayOrderSelectForm();
        }
        
        return true;
        
    }
    
    /**
     * filterInput
     */
     
    public function filterInput($data) {
        
        $filtered = array();
        
        //first remove empty lines with quantity field
        foreach ($data['items'] as $item) {
            
            if (array_key_exists('basket_item_id', $item)) $filtered[$item['basket_item_id']] = $item;
        
        }
        
        $data['items'] = $filtered;
        
        return $data;
    }
    
    /**
     * isValidReturn
     */
     
    public function isValidReturn($data) {
        
        if (!is_array($data)) {
            msg("Product returns: data $data is not array", 'error');
            return false;
        }
        
        if (!is_numeric($data['order_id'])) {
            msg("Product returns: order_id {$data['order_id']} is not numeric", 'error');
            return false;
        }
        
        if (!is_array($data['items'])) {
            msg("Product returns: items {$data['items']} is not array", 'error');
            return false;
        }
        
        //check it's not empty
        if (count($data['items']) == 0) {
            msg("Product returns: Please select at least one item", 'error');
            return false;
        }
            
        //than check they have numeric value
        foreach ($data['items'] as $filtered_item) {
            if (!is_numeric($filtered_item['basket_item_id'])) {
                msg("Product returns: basket_item_id is not numeric", 'error');
                return false;
            }
        }
    
        
        if (trim($data['action']) == '') {
            msg("Product returns: action is empty", 'error');
            return false;
        }
        
        /**
         * check order exists in our system
         */
         
        $order_detail = $this->getOrderDetail($data['order_id']);
        
        if ($order_detail['id'] != $data['order_id']) {
            
            msg("Product returns: submitted order_id {$data['order_id']} doesn't match our records", 'error');
            return false;
            
        }
                
        return true;
    
    }
    
    /**
     * isAllowedReturn
     */
     
    public function isAllowedItemForReturn($item) {
        
        /**
         * check if quantity was greater than zero (in same cases is possible to checkout with zero quantity ordered)
         */
         
        if ($item['quantity'] == 0) return false;
        
        /**
         * check it's a product which can be returned
         */
         
        require_once('models/ecommerce/ecommerce_product.php');
        $product_conf = ecommerce_product::initConfiguration();
        
        switch ($item['product']['id']) {
            case $product_conf['gift_wrap_product_id']:
            case $product_conf['gift_voucher_product_id']:
                return false;
            default:
                return true;
        }
        
    }
    
    /**
     * getActiveCustomerId
     */
     
    public function getActiveCustomerId() {
    
        if ($_SESSION['client']['customer']['id'] > 0) {
            $customer_id = $_SESSION['client']['customer']['id'];
        } else if (Onxshop_Bo_Authentication::getInstance()->isAuthenticated()) {
            $customer_id = $this->GET['customer_id'];
        } else {
            $customer_id = false;
        }
    
        return $customer_id;
    }
    
    /**
     * getOrdersList
     */
     
    public function getOrdersList($customer_id) {
    
        if (!is_numeric($customer_id)) return false;
        
        require_once('models/ecommerce/ecommerce_order.php');
        $Order = new ecommerce_order();
        
        $records = $Order->getOrderList($customer_id);
        
        if (is_array($records)) {
        
            $valid_orders_list = array();
            
            foreach ($records as $item) {
                
                //use only 1 New (paid), 2 Dispatched, 3 Complete
                if ($item['order_status'] == 1 || $item['order_status'] == 2 || $item['order_status'] == 3) {
                    
                    $valid_orders_list[] = $item;
                
                }
                
            }
            
            return $valid_orders_list;
            
        } else {
            
            return false;
        
        }
        
    }
    
    /**
     * getOrderDetail
     */
     
    public function getOrderDetail($order_id) {
    
        if (!is_numeric($order_id)) return false;
        
        require_once('models/ecommerce/ecommerce_order.php');
        $Order = new ecommerce_order();
        
        $order_detail = $Order->getFullDetail($order_id);
    
        return $order_detail;
    }
    
    /**
     * displayOrderSelectForm
     */
    
    public function displayOrderSelectForm() {
        
        $this->tpl->assign('SUBMIT_BUTTON_TEXT', 'Continue');
        $this->tpl->parse('content.form');
        
    }
    
    /**
     * displayFullForm
     */
     
    public function displayFullForm($order_id, $return_data = false) {
        
        if (!is_numeric($order_id)) {
            msg("Product returns: order_id {$order_id} is not numeric", 'error');
            return false;
        }
        
        /**
         * get basket order detail
         */
         
        $order_detail = $this->getOrderDetail($order_id);
        
        if (!is_array($order_detail)) {
                
            msg("Product returns: Cannot find order details for order_id {$order_id}", 'error');
            return false;
            
        }
        
        /**
         * assign previously submitted data
         */
         
        if (is_array($return_data)) $this->tpl->assign('RETURN', $return_data);
        
        /**
         * iterate through the all order basket lines
         */
        
        foreach ($order_detail['basket']['items'] as $item) {
            
            if ($this->isAllowedItemForReturn($item)) {
            
                $this->tpl->assign('ITEM', $item);
                
                /**
                 * allow to choose quantity when more than one item was ordered
                 */
                 
                if ($item['quantity'] > 1) {
                    
                    for ($i = 1; $i < ($item['quantity'] + 1); $i++) {
                        $this->tpl->assign('QUANTITY', $i);
                        $this->tpl->parse('content.form.basket_list.item.quantity.item');
                    }
                    
                    $this->tpl->parse('content.form.basket_list.item.quantity');
                }
                
                $this->tpl->parse('content.form.basket_list.item');
            }
        }
        
        /**
         * show basket list to select an item
         */
         
        $this->tpl->parse('content.form.basket_list');
        
        /**
         * get full order detail
         */
         
        $_Onxshop_Request = new Onxshop_Request("component/ecommerce/order_detail~order_id=$order_id~");
        $this->tpl->assign('FULL_ORDER_DETAIL', $_Onxshop_Request->getContent());
        
        /**
         * show full order detail
         */
         
        $this->tpl->parse('content.form.full_order_detail');
        
        /**
         * show action and message input fields
         */
        if ($return_data) $this->tpl->assign("SELECTED_ACTION_{$return_data['action']}", 'selected="selected"');
        $this->tpl->parse('content.form.action');
        if ($return_data) $this->tpl->assign("SELECTED_REASON_{$return_data['reason']}", 'selected="selected"');
        $this->tpl->parse('content.form.reason');
        $this->tpl->parse('content.form.message');
        
        $this->tpl->assign('SUBMIT_BUTTON_TEXT', I18N_SUBMIT);
        $this->tpl->parse('content.form');
        
        return true;
    
    }
    
    /**
     * displayPreviousOrders
     */
     
    public function displayPreviousOrders($orders_list, $order_id = false) {
    
        if (!is_array($orders_list)) return false;
        
        foreach ($orders_list as $item) {
                
            if ($item['order_id'] == $order_id) $this->tpl->assign('SELECTED', 'selected="selected"');
            else $this->tpl->assign('SELECTED', '');
            
            $this->tpl->assign('ITEM', $item);
            $this->tpl->parse('content.form.orders_list.item');
            
        }
        
        $this->tpl->parse('content.form.orders_list');
            
    }
    
    /**
     * displayResult
     */
     
    public function displayResult($data) {
    
        $order_detail = $this->getOrderDetail($data['order_id']);
        
        foreach ($order_detail['basket']['items'] as $item) {
        
            if (array_key_exists($item['id'], $data['items'])) {
                
                if (is_numeric($data['items'][$item['id']]['quantity'])) $quantity = $data['items'][$item['id']]['quantity'];
                else $quantity = 1;
                $this->tpl->assign('QUANTITY', $quantity);
                $this->tpl->assign('ITEM', $item);
                $this->tpl->parse('content.result.item');
                
            }
        
        }
        
        $this->tpl->assign('RESULT', $data);
        $this->tpl->parse('content.result');
    
    }
    
    /**
     * processReturn
     */
     
    public function processReturn($data) {
        
        //double check the input
        if (!$this->isValidReturn($data)) return false;
        
        //display result
        $this->displayResult($data);        
        
        //send email
        if ($this->sendEmail()) {
            msg('Return form submitted successfully');
            return true;
        } else {
            msg("Cannot process return form", 'error');
            return false;
        }
    }
    
    /**
     * sendEmail
     */
     
    public function sendEmail() {
        
        require_once('models/common/common_email.php');
        $CommonEmail = new common_email();
        
        require_once('models/ecommerce/ecommerce_order.php');
        $ecommerce_order_conf = ecommerce_order::initConfiguration();
        
        //admin_email_name
        $template = 'return';
        $content = $this->getResultHtml();
        $email_recipient = $ecommerce_order_conf['product_returns_mail_to_address'];
        $name_recipient = $ecommerce_order_conf['product_returns_mail_to_name'];
        $email_from = $_SESSION['client']['customer']['email'];
        $name_from = "{$_SESSION['client']['customer']['first_name']} {$_SESSION['client']['customer']['last_name']}";
        
        if ($CommonEmail->sendEmail($template, $content, $email_recipient, $name_recipient, $email_from, $name_from)) return true;
        else return false;
    
    }
    
    /**
     * getResultHtml
     */
     
    public function getResultHtml() {
    
        return $this->tpl->text('content.result');;
        
    }
}

