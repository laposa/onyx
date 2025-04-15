<?php
/** 
 * Copyright (c) 2010-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 * IPN handler
 *
 
https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_admin_IPNImplementation

1. Wait for an HTTP post from PayPal.
2. Create a request that contains exactly the same IPN variables and values in the same order, preceded with cmd=_notify-validate.
3. Post the request to www.paypal.com or www.sandbox.paypal.com, depending on whether you are going live or testing your listener in the Sandbox.
4. Wait for a response from PayPal, which is either VERIFIED or INVALID.
5. Verify that the response status is 200.
6. If the response is VERIFIED, perform the following checks:
    - Confirm that the payment status is Completed.
        PayPal sends IPN messages for pending and denied payments as well; do not ship until the payment has cleared.
    - Use the transaction ID to verify that the transaction has not already been processed, which prevents duplicate transactions from being processed.
        Typically, you store transaction IDs in a database so that you know you are only processing unique transactions.
    - Validate that the receiver’s email address is registered to you.
        This check provides additional protection against fraud.
    - Verify that the price, item description, and so on, match the transaction on your website.
        This check provides additional protection against fraud.
7. If the verified response passes the checks, take action based on the value of the txn_type variable if it exists; otherwise, take action based on the value of the reason_code variable.
8. If the response is INVALID or the response code is not 200, save the message for further investigation.

 */

require_once('controllers/component/ecommerce/payment/paypal.php');

class Onyx_Controller_Component_Ecommerce_Payment_PayPal_Callback extends Onyx_Controller_Component_Ecommerce_Payment_PayPal {

    /**
     * main action
     */
     
    public function mainAction() {
        
        //not implemented
        
    }
    
    /**
     * prepare data for payment gateway
     */
    
    function prepare($order_id) {
    
        $order_data = $this->Transaction->getOrderDetail($order_id);
    
    }
    
    /*
    The following listener sends email to the address specified in the ipn_email variable, as in https://your_host/live_ipn_mail.php?ipn_email=email_address . You can use this listener as a starting point for your own listener; rather than send email, your listener could take action based on the type of transaction.
    */
    function example() {
        
        error_reporting(E_ALL ^ E_NOTICE); 
        $email = $_GET['ipn_email']; 
        $header = ""; 
        $emailtext = ""; 
        
        // Read the post from PayPal and add 'cmd' 
        $req = 'cmd=_notify-validate'; 
        
        if(function_exists('get_magic_quotes_gpc')) {  
            $get_magic_quotes_exits = true; 
        } 
        
        // Handle escape characters, which depends on setting of magic quotes
        foreach ($_POST as $key => $value) {  
        
            if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {  
                $value = urlencode(stripslashes($value)); 
            } else { 
                $value = urlencode($value); 
            } 
        
            $req .= "&$key=$value"; 
        }
        
        // Post back to PayPal to validate 
        $header .= "POST /cgi-bin/webscr HTTP/1.0\r\n"; 
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n"; 
        $header .= "Content-Length: " . strlen($req) . "\r\n\r\n"; 
        $fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30); 
         
         
        // Process validation from PayPal 
        // TODO: This sample does not test the HTTP response code. All 
        // HTTP response codes must be handles or you should use an HTTP 
        // library, such as cUrl 
         
        if (!$fp) { 
        
            // HTTP ERROR 
        
        } else { 
            
            // NO HTTP ERROR 
            fputs ($fp, $header . $req); 
            
            while (!feof($fp)) { 
                
                $res = fgets ($fp, 1024); 
                
                if (strcmp ($res, "VERIFIED") == 0) { 
                    // TODO: 
                    // Check the payment_status is Completed 
                    // Check that txn_id has not been previously processed 
                    // Check that receiver_email is your Primary PayPal email 
                    // Check that payment_amount/payment_currency are correct 
                    // Process payment 
                    // If 'VERIFIED', send an email of IPN variables and values to the 
                    // specified email address 
                    foreach ($_POST as $key => $value){ 
                    $emailtext .= $key . " = " .$value ."\n\n"; 
                    } 
                    mail($email, "Live-VERIFIED IPN", $emailtext . "\n\n" . $req); 
                } else if (strcmp ($res, "INVALID") == 0) { 
                    // If 'INVALID', send an email. TODO: Log for manual investigation. 
                    foreach ($_POST as $key => $value){ 
                    $emailtext .= $key . " = " .$value ."\n\n"; 
                    } 
                    mail($email, "Live-INVALID IPN", $emailtext . "\n\n" . $req); 
                }    
            } 
        fclose ($fp); 
        }
    }
}