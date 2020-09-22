<?php
/**
 * Copyright (c) 2008-2013 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */


class Onyx_Controller_Autologin extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        if ($_SESSION['client']['customer']['id'] == 0) {

            $this->checkCookieForToken();

        }

        return true;

    }
    
    /**
     * checkCookieForToken
     */

    protected function checkCookieForToken()
    {
        if (isset($_COOKIE[ONYX_TOKEN_NAME])) {

            require_once('models/client/client_customer_token.php');
            $Token = new client_customer_token();
            $Token->setCacheable(false);
        
            $customer_detail = $Token->getCustomerDetailForToken($_COOKIE['onyx_token']);

            if ($customer_detail) {

                require_once('models/client/client_customer.php');
                $Customer = new client_customer();
                $Customer->setCacheable(false);
                $conf = $Customer::initConfiguration();

                if ($conf['login_type'] == 'username') $username = $customer_detail['username'];
                else $username = $customer_detail['email'];

                $customer_detail = $Customer->login($username);

                if ($customer_detail) {
                    $_SESSION['client']['customer'] = $customer_detail;
                    $_SESSION['use_page_cache'] = false;
                } else {
                    msg('Autologin failed', 'error', 1);
                }

            } else {

                msg('Invalid autologin token supplied', 'error', 1);
                //delete cookie
                setcookie(ONYX_TOKEN_NAME, '', time()-3600, '/');
            }

        }
    }

}
