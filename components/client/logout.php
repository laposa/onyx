<?php
/** 
 * Copyright (c) 2005-2016 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Component_Client_Logout extends Onyx_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        if (!empty($_SESSION['client']['customer']['id'])) {
            
            require_once('models/client/client_customer.php');
            $ClientCustomer = new client_customer();
            
            if ($ClientCustomer->logout()) {
            
                msg("Logout of {$_SESSION['client']['customer']['email']}", 'ok', 1);
                
                //$_SESSION['client']['customer']['id'] = 0;
                unset($_SESSION['client']);
                // unlink basket from customer
                unset($_SESSION['basket']);
                // clear gift parameters
                unset($_SESSION['gift']);
                unset($_SESSION['gift_message']);

            
                $this->invalidateToken();
                
            } else {
            
                msg("Customer logout failed", 'error');
            
            }
        }

        //forward to the homepage
        onyxGoTo(AFTER_CLIENT_LOGOUT_URL);

        return true;
    }
    
    /**
     * invalidate token
     */
     
    public function invalidateToken() {

        // invalidate token in database

        if (isset($_COOKIE[ONYX_TOKEN_NAME])) {
            require_once('models/client/client_customer_token.php');
            $Token = new client_customer_token();
            $Token->setCacheable(false);
            $Token->invalidateToken($_COOKIE[ONYX_TOKEN_NAME]);
        }

        // invalidate token in cookie

        setcookie(ONYX_TOKEN_NAME, "", time()-60*60*24*100, "/");
    }
    
}
