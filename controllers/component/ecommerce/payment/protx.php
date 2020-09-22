<?php
/** 
 * ProtX aka SagePay
 *
 * Copyright (c) 2009-2015 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/component/ecommerce/payment.php');

class Onyx_Controller_Component_Ecommerce_Payment_Protx extends Onyx_Controller_Component_Ecommerce_Payment {

    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('conf/payment/protx.php');
        $this->transactionPrepare();
        
        $payment_gateway_data = $this->paymentPrepare($this->GET['order_id']);
        
        if (!$payment_gateway_data) return false;
        
        $this->tpl->assign("PAYMENT_GATEWAY", $payment_gateway_data);
        $this->tpl->parse('content.autosubmit');
        
        return true;
        
    }
    
    /**
     * prepare data for payment gateway
     */
    
    function paymentPrepare($order_id) {
    
        require_once('models/common/common_node.php');
        $node_conf = common_node::initConfiguration();
        
        $order_data = $this->Transaction->getOrderDetail($order_id);
        
        if ($_SERVER['HTTPS']) $protocol = 'https';
        else $protocol = 'http';
        $server_url = "$protocol://{$_SERVER['HTTP_HOST']}";

        $protx = array(
            'URL' => ECOMMERCE_TRANSACTION_PROTX_URL,
            'VPSProtocol' => ECOMMERCE_TRANSACTION_PROTX_VPSPROTOCOL,
            'Vendor' => ECOMMERCE_TRANSACTION_PROTX_VENDOR,
            'TxType' => ECOMMERCE_TRANSACTION_PROTX_TXTYPE,
            'Crypt' => '',
            'VendorEmail' => ECOMMERCE_TRANSACTION_PROTX_VENDOR_EMAIL
        );
        
        require_once('models/ecommerce/ecommerce_order.php');
        $Order = new ecommerce_order();     
        $protx_amount = $Order->calculatePayableAmount($order_data);
    
        $protx['Crypt']['VendorTxCode'] = $order_data['id'] . '_' . time();
        $protx['Crypt']['Amount'] = $protx_amount;
        $protx['Crypt']['Currency'] = GLOBAL_DEFAULT_CURRENCY;
        $protx['Crypt']['Description'] = "Payment for Basket created {$order_data['basket']['created']}";
        $protx['Crypt']['SuccessURL'] = "$server_url/page/" . $node_conf['id_map-payment_protx_success'] . "?order_id={$order_data['id']}";
        $protx['Crypt']['FailureURL'] = "$server_url/page/" . $node_conf['id_map-payment_protx_success'] . "?order_id={$order_data['id']}";

        $protx['Crypt']['CustomerEMail'] = $order_data['client']['customer']['email'];
        $protx['Crypt']['VendorEMail'] = $protx['VendorEmail'];
        $protx['Crypt']['eMailMessage'] = ECOMMERCE_TRANSACTION_PROTX_MAIL_MESSAGE;

        $protx['Crypt']['BillingSurname'] = $order_data['client']['customer']['last_name'];
        $protx['Crypt']['BillingFirstNames'] = $order_data['client']['customer']['first_name'];
        $protx['Crypt']['BillingAddress1'] = $order_data['address']['invoices']['line_1'];
        $protx['Crypt']['BillingCity'] = $order_data['address']['invoices']['city'];
        $protx['Crypt']['BillingPostCode'] = $order_data['address']['invoices']['post_code'];
        $protx['Crypt']['BillingCountry'] = $order_data['address']['invoices']['country']['iso_code2'];

        $protx['Crypt']['DeliverySurname'] = $order_data['client']['customer']['last_name'];
        $protx['Crypt']['DeliveryFirstNames'] = $order_data['client']['customer']['first_name'];

        $delivery_name = explode(" ", trim($order_data['address']['delivery']['name']));
        foreach ($delivery_name as $i => $item) {
            if ($i == 0) $protx['Crypt']['DeliveryFirstNames'] = trim($item);
            if ($i == count($delivery_name) - 1) $protx['Crypt']['DeliverySurname'] = trim($item);
        }

        $protx['Crypt']['DeliveryAddress1'] = $order_data['address']['delivery']['line_1'];
        $protx['Crypt']['DeliveryCity'] = $order_data['address']['delivery']['city'];
        $protx['Crypt']['DeliveryPostCode'] = $order_data['address']['delivery']['post_code'];
        $protx['Crypt']['DeliveryCountry'] = $order_data['address']['delivery']['country']['iso_code2'];

        $protx['Crypt']['Basket'] = '';
    
        $basket = count($order_data['basket']['items']);
    
        //Number of items in basket:Item 1 Description:Quantity of item 1:Unit cost item 1 minus tax:Tax of item 1:Cost of Item 1 inc tax:Total cost of item 1 (Quantity x cost inc tax):Item 2 Description:Quantity of item 2: .... :Cost of Item n inc tax:Total cost of item n
        foreach ($order_data['basket']['items'] as $item) {
            $basket = $basket . ':' . $item['product']['variety']['sku'] . ' - ' . $item['product']['name'] . ':' . $item['quantity'] . ':' . $item['product']['variety']['price'][GLOBAL_DEFAULT_CURRENCY]['price']['common']['value'] . ':' . $item['product']['variety']['price'][GLOBAL_DEFAULT_CURRENCY]['vat'] . ':' . $item['product']['variety']['price'][GLOBAL_DEFAULT_CURRENCY]['price']['common']['value_vat'] . ':' . $item['total_inc_vat'];
        }
    
        $protx['Crypt']['Basket'] = $basket;

        foreach ($protx['Crypt'] as $key=>$val) {
            $crypt = $crypt . '&' . $key . '=' . $val; 
        }
        $crypt = ltrim($crypt, '&');

        $protx['Crypt'] = self::encryptAes($crypt, ECOMMERCE_TRANSACTION_PROTX_PASSWORD);
        
        return $protx;
        
    }
    
    /**
     * process callback
     */
    
    function paymentProcess($order_id, $crypt) {
    
        //hack for changing white space to + sign
        $crypt = str_replace(' ', '+', $crypt);

        require_once('models/ecommerce/ecommerce_order.php');
        $Order = new ecommerce_order();

        //decode crypt
        $decoded = self::decryptAes($crypt, ECOMMERCE_TRANSACTION_PROTX_PASSWORD);
        //explode protx data
        parse_str($decoded, $response);

        $this->msgProtxStatus($response['Status']);

        $order_data = $Order->getOrder($order_id);

        $transaction_data['order_id'] = $order_data['id'];
        $transaction_data['pg_data'] = serialize($response);
        $transaction_data['currency_code'] = GLOBAL_DEFAULT_CURRENCY;
        if (is_numeric($response['Amount'])) $transaction_data['amount'] = $response['Amount'];
        else $transaction_data['amount'] = 0;
        $transaction_data['created'] = date('c');
        $transaction_data['type'] = 'protx';
        if ($response['Status'] == 'OK') $transaction_data['status'] = 1;
        else $transaction_data['status'] = 0;
        
        /**
         * insert
         */
         
        if ($id = $this->Transaction->insert($transaction_data)) {
        
            // in payment_success must be everytime Status OK
            if ($response['Status'] == 'OK') {
                $Order->setStatus($order_id, 1);
                
                //send email to admin
                require_once('models/common/common_email.php');
    
                $EmailForm = new common_email();
            
                $_Onyx_Request = new Onyx_Request("component/ecommerce/order_detail~order_id={$order_data['id']}~");
                $order_data['order_detail'] = $_Onyx_Request->getContent();
                
                //this allows use customer data and company data in the mail template
                //is passed as DATA to template in common_email->_format
                $GLOBALS['common_email']['transaction'] = $transaction_data;
                $GLOBALS['common_email']['order'] = $order_data;
                
                if (!$EmailForm->sendEmail('new_order_paid', 'n/a', $order_data['client']['customer']['email'], $order_data['client']['customer']['first_name'] . " " . $order_data['client']['customer']['last_name'])) {
                    msg('ecommerce_transaction: Cant send email.', 'error', 2);
                }
                
                if ($Order->conf['mail_to_address']) {
                    if (!$EmailForm->sendEmail('new_order_paid', 'n/a', $Order->conf['mail_to_address'], $Order->conf['mail_to_name'])) {
                        msg('ecommerce_transaction: Cant send email.', 'error', 2);
                    }
                }
            
            } else {
                $Order->setStatus($order_id, 5);
            }
            
            return $id;
        } else {
        
            //to be sure...
            if ($response['Status'] == 'OK') {
                msg("Payment for order $order_id was successfully Authorised, but I cant save the transaction TxAuthNo {$pg_data['TxAuthNo']}!", 'error');
            }
            
            msg("payment/protx: cannot insert serialized pg_data: {$transaction_data['pg_data']}", 'error');
            
            return false;
        }

    }
    
    /**
     * protx status translation
     * 
     */
     
    function msgProtxStatus($status) {
    
        if ($status == 'OK') {
            msg('Process executed without error and the transaction was successfully Authorised.', 'ok', 2);
        } else if ($status == 'MALFORMED') {
            msg('Input message was malformed - normally will only occur during development and vendor integration. StatusDetail will give more information.', 'error');
        } else if ($status == 'INVALID') {
            msg('Unable to authenticate the vendor, values in the fields are illegal or incorrect, or problem occurred registering the transaction. For example, a MALFORMED Status will be sent if the Amount field is missing, but an INVALID will be sent if it contains text or is too large a number for the specified currency.', 'error');
        } else if ($status == 'NOTAUTHED') {
            msg(' The VSP could not authorise the transaction because the details provided by the Customer were incorrect, not authenticated or could not support the Transaction.', 'error');
        } else if ($status == 'ABORT') {
            msg('The Transaction could not be completed because the user clicked the Cancel button on one of the PROTX pages (or the transaction timed out).', 'error');
        } else if ($status == 'ERROR') {
            msg('A code-related error occurred which prevented the process from executing successfully. This indicates something is wrong at the PROTX server.', 'error');
        } else {
            msg("Unknown status $status", 'error');
        }
        
    }

    /**
     * PHP's mcrypt does not have built in PKCS5 Padding, so we use this.
     *
     * @param string $input The input string.
     * @return string The string with padding.
     */

    static protected function addPKCS5Padding($input)
    {
        $blockSize = 16;
        $padd = "";

        $length = $blockSize - (strlen($input) % $blockSize);
        for ($i = 1; $i <= $length; $i++) $padd .= chr($length);

        return $input . $padd;
    }

    /**
     * Remove PKCS5 Padding from a string.
     *
     * @param string $input The decrypted string.
     * @return string String without the padding.
     */

    static protected function removePKCS5Padding($input)
    {
        $blockSize = 16;
        $padChar = ord($input[strlen($input) - 1]);

        /* Check for PadChar is less then Block size */
        if ($padChar > $blockSize) die('Invalid encryption string');

        /* Check by padding by character mask */
        if (strspn($input, chr($padChar), strlen($input) - $padChar) != $padChar) die('Invalid encryption string');

        $unpadded = substr($input, 0, (-1) * $padChar);
        /* Chech result for printable characters */
        if (preg_match('/[[:^print:]]/', $unpadded)) die('Invalid encryption string');

        return $unpadded;
    }

    /**
     * Encrypt a string ready to send to SagePay using encryption key.
     *
     * @param  string  $string  The unencrypyted string.
     * @param  string  $key     The encryption key.
     * @return string The encrypted string.
     */

    static public function encryptAes($string, $key)
    {
        // AES encryption, CBC blocking with PKCS5 padding then HEX encoding.
        // Add PKCS5 padding to the text to be encypted.
        $string = self::addPKCS5Padding($string);

        // Perform encryption with PHP's MCRYPT module.
        $crypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $string, MCRYPT_MODE_CBC, $key);

        // Perform hex encoding and return.
        return "@" . strtoupper(bin2hex($crypt));
    }

    /**
     * Decode a returned string from SagePay.
     *
     * @param string $strIn         The encrypted String.
     * @param string $password      The encyption password used to encrypt the string.
     * @return string The unecrypted string.
     */

    static public function decryptAes($strIn, $password)
    {
        // HEX decoding then AES decryption, CBC blocking with PKCS5 padding.
        // Use initialization vector (IV) set from $str_encryption_password.
        $strInitVector = $password;

        // Remove the first char which is @ to flag this is AES encrypted and HEX decoding.
        $hex = substr($strIn, 1);

        // Throw exception if string is malformed
        if (!preg_match('/^[0-9a-fA-F]+$/', $hex)) die('Invalid encryption string');
        $strIn = pack('H*', $hex);

        // Perform decryption with PHP's MCRYPT module.
        $string = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $password, $strIn, MCRYPT_MODE_CBC, $strInitVector);
        return self::removePKCS5Padding($string);
    }
    
}
