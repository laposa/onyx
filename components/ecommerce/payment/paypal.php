<?php
/** 
 * Copyright (c) 2009-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 *
 */

/*TODO: check invoice was payed in full with Instant Payment Notification  (IPN) 
Verify that the payment amount actually matches what you intend to charge. Although not technically an IPN issue, if you do not encrypt buttons, it is possible for someone to capture the original transmission and change the price. Without this check, you could accept a lesser payment than what you expected.

PayPal will send:
mc_gross=19.95&protection_eligibility=Eligible&address_status=confirmed&payer_id=LPLWNMTBWMFAY&tax=0.00&address_street=1+Main+St&payment_date=20%3A12%3A59+Jan+13%2C+2009+PST&payment_status=Completed&charset=windows-1252&address_zip=95131&first_name=Test&mc_fee=0.88&address_country_code=US&address_name=Test+User&notify_version=2.6&custom=&payer_status=verified&address_country=United+States&address_city=San+Jose&quantity=1&verify_sign=AtkOfCXbDm2hu0ZELryHFjY-Vb7PAUvS6nMXgysbElEn9v-1XcmSoGtf&payer_email=gpmac_1231902590_per%40paypal.com&txn_id=61E67681CH3238416&payment_type=instant&last_name=User&address_state=CA&receiver_email=gpmac_1231902686_biz%40paypal.com&payment_fee=0.88&receiver_id=S8XGHLYDW9T3S&txn_type=express_checkout&item_name=&mc_currency=USD&item_number=&residence_country=US&test_ipn=1&handling_amount=0.00&transaction_subject=&payment_gross=19.95&shipping=0.00

We send back:
https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_notify-validate&mc_gross=19.95&protection_eligibility=Eligible&address_status=confirmed&payer_id=LPLWNMTBWMFAY&tax=0.00&...&payment_gross=19.95&shipping=0.00

PayPal returns:
PayPal will then send one single-word message, VERIFIED, if the message is valid; otherwise, it will send another single-word message, INVALID.

IMPORTANT:After you receive the VERIFIED message, there are several important checks you must perform before you can assume that the message is legitimate and not already processed:Confirm that the payment status is Completed.Use the transaction ID to verify that the transaction has not already been processed, which prevents duplicate transactions from being processed.Validate that the receiver’s email address is registered to you.Verify that the price, item description, and so on, match the transaction on your website.

Some more documentation on Express Checkout: http://zdrojak.root.cz/clanky/implementujeme-platby-pres-paypal-za-30-minut/
Zend: http://framework.zend.com/wiki/display/ZFPROP/Zend_Service_PayPal+-+A.J.+Brown

http://www.mattwillo.co.uk/blog/2010-04-13/integrating-paypal-with-php-and-ipn/
 
https://cms.paypal.com/uk/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_html_formbasics
*/

require_once('controllers/component/ecommerce/payment.php');

class Onyx_Controller_Component_Ecommerce_Payment_Paypal extends Onyx_Controller_Component_Ecommerce_Payment {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        if (is_numeric($this->GET['autosubmit'])) $autosubmit = $this->GET['autosubmit'];
        else $autosubmit = 1;
        
        require_once('conf/payment/paypal.php');
        
        $this->transactionPrepare();
        
        $payment_gateway_data = $this->paymentPrepare($this->GET['order_id']);
        
        if (!$payment_gateway_data) return false;
        
        $this->tpl->assign("PAYMENT_GATEWAY", $payment_gateway_data);
        if ($autosubmit) $this->tpl->parse('content.autosubmit');
    
        return true;
    }
    
    /**
     * prepare data for payment gateway
     */
    
    function paymentPrepare($order_id) {
        
        if (!is_numeric($order_id)) return false;
        
        require_once('models/common/common_node.php');
        $node_conf = common_node::initConfiguration();
        //$this->tpl->assign('NODE_CONF', $node_conf);
        
        $order_data = $this->Transaction->getOrderDetail($order_id);
        
        /**
         * process payment method only if status = 0 unpaid or 5 failed payment
         * 
         */
         
        if (!$this->checkOrderStatusValidForPayment($order_data['status'])) return false;
        
        /**
         * check if SSL is enabled
         */
         
        if ($_SERVER['HTTPS']) $protocol = 'https';
        else $protocol = 'http';
        $server_url = "$protocol://{$_SERVER['HTTP_HOST']}";
        
        /**
         * prepare data
         */
         
        require_once('models/ecommerce/ecommerce_order.php');
        $Order = new ecommerce_order();     
        $total_amount = $Order->calculatePayableAmount($order_data);
        
        $payment_gateway_data = array();
        $payment_gateway_data['order_data'] = $order_data;
        $payment_gateway_data['total_amount'] = $total_amount;
        $payment_gateway_data['server_url'] = $server_url; 
        
        return $payment_gateway_data;
    }
}