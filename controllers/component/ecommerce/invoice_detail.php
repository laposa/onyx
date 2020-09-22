<?php
/**
 * Invoice Detail
 * only shows invoice who are not canceled (invoice status = 1)
 *
 * Copyright (c) 2008-2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Component_Ecommerce_Invoice_Detail extends Onxshop_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        /**
         * Input data
         */
        
        if (is_numeric($this->GET['id'])) $order_id = $this->GET['id'];
        else return false;
        
        require_once('models/ecommerce/ecommerce_order.php');
        $Order = new ecommerce_order();
        $Order->setCacheable(false);
        
        require_once('models/ecommerce/ecommerce_invoice.php');
        $Invoice = new ecommerce_invoice();
        $Invoice->setCacheable(false);
        
        if (is_numeric($order_id)) $order_data = $Order->getOrder($order_id);
        
        //security check of owner
        if ($order_data['basket']['customer_id'] !== $_SESSION['client']['customer']['id'] &&  !Onxshop_Bo_Authentication::getInstance()->isAuthenticated()) {
            msg('unauthorized access to view invoice detail', 'error');
        } else {
            
            if ($order_data['status'] != 0) {
                $invoice_detail = $Invoice->getInvoiceForOrder($order_data['id']);
                if ($invoice_detail) {
                    //$invoice_detail['created'] = strftime('%d/%m/%Y', strtotime($invoice_detail['created']));
                    $this->tpl->assign("INVOICE", $invoice_detail);
                    $this->tpl->parse('content.invoice');
                }
                $this->tpl->parse('content.print_invoice');
            } else if ($Order->conf['proforma_invoice'] == true || ONXSHOP_IN_BACKOFFICE) {
                $invoice_detail = array();
                $invoice_detail['order_id'] = $order_id;
                $this->tpl->assign("INVOICE", $invoice_detail);
                $this->tpl->parse('content.print_invoice_proforma');
            }
        }

        return true;
    }
}
