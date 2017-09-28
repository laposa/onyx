<?php
/** 
 * Copyright (c) 2005-2016 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Component_Client_Logout extends Onxshop_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        if ($_SESSION['client']['customer']['id'] > 0) {
            
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
        onxshopGoTo(AFTER_CLIENT_LOGOUT_URL);

        return true;
    }
    
    /**
     * invalidate token
     */
     
    public function invalidateToken() {

        // invalidate token in database

        if (isset($_COOKIE['onxshop_token'])) {
            require_once('models/client/client_customer_token.php');
            $Token = new client_customer_token();
            $Token->setCacheable(false);
            $Token->invalidateToken($_COOKIE['onxshop_token']);
        }

        // invalidate token in cookie

        setcookie("onxshop_token", "", time()-60*60*24*100, "/");
    }
    
}
