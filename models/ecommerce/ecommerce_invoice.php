<?php
/**
 * class ecommerce_invoice
 * printed invoice
 *
 * Copyright (c) 2009-2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class ecommerce_invoice extends Onxshop_Model {

    /**
     * @access private
     */
    var $id;
    /**
     * @access private
     */
    var $order_id;
    /**
     * @access private
     */
    var $goods_net;
    /**
     * @access private
     */
    var $goods_vat_sr;
    /**
     * @access private
     */
    var $goods_vat_rr;
    /**
     * @access private
     */
    var $delivery_net;
    /**
     * @access private
     */
    var $delivery_vat;
    /**
     * @access private
     */
    var $payment_amount;
    /**
     * @access private
     */
    var $payment_type;
    /**
     * @access private
     */
    var $created;
    /**
     * @access private
     */
    var $modified;
    /**
     *  Same as in ecommerce_order
     *  1 - normal
     *  4 - canceled
     * @access private
     */
    var $status;
    /**
     * serialized
     * @access private
     */
    var $other_data;
    
    var $basket_detail;

    var $basket_detail_enhanced;
    
    var $customer_name;
    
    var $customer_email;
    
    var $address_invoice;
    
    var $address_delivery;
    
    //voucher discount (face value voucher)
    var $face_value_voucher;
    
    var $_metaData = array(
        'id'=>array('label' => 'ID', 'validation'=>'int', 'required'=>true), 
        'order_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
        'goods_net'=>array('label' => '', 'validation'=>'decimal', 'required'=>true),
        'goods_vat_sr'=>array('label' => 'VAT Standard Rate', 'validation'=>'decimal', 'required'=>true),
        'goods_vat_rr'=>array('label' => 'VAT Reduced Rate', 'validation'=>'decimal', 'required'=>true),
        'delivery_net'=>array('label' => '', 'validation'=>'decimal', 'required'=>true),
        'delivery_vat'=>array('label' => '', 'validation'=>'decimal', 'required'=>true),
        'payment_amount'=>array('label' => '', 'validation'=>'decimal', 'required'=>true),
        'payment_type'=>array('label' => '', 'validation'=>'string', 'required'=>true),
        'created'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
        'modified'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
        'status'=>array('label' => '', 'validation'=>'int', 'required'=>true),
        'other_data'=>array('label' => '', 'validation'=>'serialized', 'required'=>false),
        'basket_detail'=>array('label' => '', 'validation'=>'xhtml', 'required'=>false),
        'basket_detail_enhanced'=>array('label' => '', 'validation'=>'xhtml', 'required'=>false),
        'customer_name'=>array('label' => '', 'validation'=>'string', 'required'=>true),
        'customer_email'=>array('label' => '', 'validation'=>'email', 'required'=>true),
        'address_invoice'=>array('label' => '', 'validation'=>'string', 'required'=>true),
        'address_delivery'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'face_value_voucher'=>array('label' => '', 'validation'=>'decimal', 'required'=>false)
    );
    
    /**
     * create table sql
     */
     
    private function getCreateTableSql() {
    
        $sql = "
CREATE TABLE ecommerce_invoice ( 
    id serial NOT NULL PRIMARY KEY,
    order_id integer REFERENCES ecommerce_order ON UPDATE RESTRICT ON DELETE RESTRICT ,
    goods_net decimal(12,5) ,
    goods_vat_sr decimal(12,5) ,
    goods_vat_rr decimal(12,5) ,
    delivery_net decimal(12,5) ,
    delivery_vat decimal(12,5) ,
    payment_amount decimal(12,5) ,
    payment_type character varying(255) ,
    created timestamp(0) without time zone NOT NULL DEFAULT NOW(),
    modified timestamp(0) without time zone NOT NULL DEFAULT NOW(),
    status smallint ,
    other_data text,
    basket_detail text,
    basket_detail_enhanced text,
    customer_name character varying(255) ,
    customer_email character varying(255) ,
    address_invoice text,
    address_delivery text,
    face_value_voucher decimal(12,5)
);
        ";
        
        return $sql;
    }
    
    /**
     * init configuration
     */
     
    static function initConfiguration() {
        if (array_key_exists('ecommerce_invoice', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['ecommerce_invoice'];
        else $conf = array();
        
        if (!$conf['company_name']) $conf['company_name'] = 'EXAMPLE COMPANY LTD';
        //logo file src
        if (!$conf['company_logo']) $conf['company_logo'] = '';
        if (!$conf['delivery_logo']) $conf['delivery_logo'] = '';
        
        if (!$conf['footer']) $conf['footer'] = '
        TEL 011 1234 1234 FAX 011 1234 1234<br />
        EXAMPLE TESTEMONIAL WWW.EXAMPLE.COM<br />
        REGISTERED OFFICE  EXAMPLE CITY SO11 1AA<br />
        EXAMPLE COMPANY LTD. VAT 123456789 REGISTERED IN ENGLAND NO 1234567
        ';
        
        if (!$conf['return_address']) $conf['return_address'] = '
        RETURN ADDRESS: EXAMPLE COMPANY WAREHOUSE LTD, EXAMPLE STREET, EXAMPLE CITY POST CODE
        ';

        return $conf;
    }

    /**
     * invoice detail
     */
     
    function invoiceDetail($id) {
        $invoice_data = $this->detail($id);
        return $invoice_data;
    }
    
    /**
     * invoice detail for an order
     */
    
    function getInvoiceForOrder($order_id) {
        $invoices = $this->checkInvoiceExists($order_id) ;
        return $invoices;
    }
    
    /**
     * generate invoice data
     */

    function generateInvoiceData($order_id) {
    
        require_once('models/ecommerce/ecommerce_order.php');
        $Order = new ecommerce_order();
        $Order->setCacheable(false);
        $order_data = $Order->getOrder($order_id);
        
        $invoice['order_id'] = $order_id;
        $invoice['goods_net'] = $order_data['basket']['sub_total']['net'];
        $invoice['goods_vat'] = $order_data['basket']['sub_total']['vat'];
        $invoice['delivery_net'] = $order_data['basket']['delivery']['value_net'];
        $invoice['delivery_vat'] = $order_data['basket']['delivery']['vat'];

        $invoice['payment_amount'] = $order_data['basket']['total'];
        
        if ($order_data['payment_type'] != '') $invoice['payment_type'] = $order_data['payment_type'];
        else $invoice['payment_type'] = 'n/a';
        $invoice['created'] = date('c');
        $invoice['modified'] = date('c');
        $invoice['status'] = 1;
        //usefull for debug $invoice['other_data'] = serialize($order_data);
        
        //customer detail
        $invoice['customer_name'] = "{$order_data['client']['customer']['title_before']} {$order_data['client']['customer']['first_name']} {$order_data['client']['customer']['last_name']}";
        $invoice['customer_email'] = "{$order_data['client']['customer']['email']}";
        
        /**
         * FIXME
         * shouldn't call controllers from model
         * this should be moved into the invoice controller
         *
         */
        //get HTML content
        //basket_detail
        $_Onxshop_Request = new Onxshop_Request("component/ecommerce/basket_detail~id={$order_data['basket_id']}:order_id={$order_id}:delivery_address_id={$order_data['delivery_address_id']}:delivery_options[carrier_id]={$order_data['other_data']['delivery_options']['carrier_id']}~");
        $invoice['basket_detail'] =  $_Onxshop_Request->getContent();
        $_Onxshop_Request = new Onxshop_Request("component/ecommerce/basket_detail_enhanced~id={$order_data['basket_id']}:order_id={$order_id}:delivery_address_id={$order_data['delivery_address_id']}:delivery_options[carrier_id]={$order_data['other_data']['delivery_options']['carrier_id']}~");
        $invoice['basket_detail_enhanced'] =  $_Onxshop_Request->getContent();
        
        //address_invoice
        $_Onxshop_Request = new Onxshop_Request("component/client/address~invoices_address_id={$order_data['invoices_address_id']}:hide_button=1~");
        $invoice['address_invoice'] = $_Onxshop_Request->getContent();
        //address_delivery
        $_Onxshop_Request = new Onxshop_Request("component/client/address~delivery_address_id={$order_data['delivery_address_id']}:hide_button=1~");
        $invoice['address_delivery'] = $_Onxshop_Request->getContent();
        
        //get the text version
        $invoice['address_invoice'] = html2text($invoice['address_invoice']);
        $invoice['address_delivery'] = html2text($invoice['address_delivery']);
        $invoice['face_value_voucher'] = $order_data['basket']['face_value_voucher'];

        return $invoice;
    }
    
    /**
     * Enter description here...
     *
     * @param unknown_type $order_id
     */
     
    function createNormalInvoice($order_id) {
        
        $invoice = $this->generateInvoiceData($order_id);

        if (!$this->checkInvoiceExists($order_id)) {
            if  ($id = $this->insert($invoice)) {
                return $id;
            } else {
                msg("Can't create invoice", 'error');
                return false;
            }
        } else {
            msg("Invoice for order $order_id already exists!", "error");
        }
    }

    
    /**
     * Enter description here...
     *
     * @param unknown_type $order_id
     */
     
    function createProformaInvoice($order_id) {
        
    }
    
    /**
     * Enter description here...
     *
     * @param unknown_type $order_id
     */
    
    function cancelInvoice($order_id) {
        
        if ($invoice = $this->checkInvoiceExists($order_id)) {
        
            $invoice_detail = $this->detail($invoice['id']);
            $invoice_detail['status'] = 4;
            $invoice_detail['modified'] = date('c');
            
            if ($this->update($invoice_detail)) {
                msg("Invoice {$invoice['id']} for order $order_id has been cancelled", 'ok');
                return true;
            } else {
                msg("Can't cancel invoice {$invoice['id']} for order $order_id", 'error');
                return false;
            }
        }
    }
    
    /**
     * check
     */
    
    function checkInvoiceExists($order_id) {
        $invoices = $this->listing("order_id = $order_id");
        
        if (count($invoices) > 0 && is_array($invoices)) {
            //check all, if there is one in valid status
            foreach ($invoices as $invoice) {
                if ($invoice['status'] == 1) {
                    msg("Invoice for order $order_id exists and is in valid status", "ok", 2);
                    return $invoice;
                }
            }
        } else {
            msg("Invoice for order $order_id does not exists", "ok", 1);
            return false;
        }
    }
}
