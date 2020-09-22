<?php
/**
 * Subscribe to newsletter (prepopulated registration)
 *
 * Copyright (c) 2009-2015 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/component/client/registration.php');

class Onyx_Controller_Component_Client_Newsletter_Subscribe extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        /**
         * initialize
         */
         
        require_once('models/client/client_customer.php');
        
        $Customer = new client_customer();
        $Customer->setCacheable(false);
        
        /**
         * input
         */
         
        $client_data = $_POST['client'];
        
        if (is_array($client_data)) {
            $this->tpl->assign('CLIENT', $client_data);
        }
        
        /**
         * save
         */
         
        if ($_POST['save_newsletter_signup'] && $client_data['customer']['first_name'] && $client_data['customer']['last_name'] && $client_data['customer']['email']) {
                
            if($customer_id = $Customer->newsletterSubscribe($client_data['customer'])) {
                
                msg("Subscribed {$client_data['customer']['email']}");
                $this->tpl->parse('content.thank_you');

                // set status cookie
                setcookie("newsletter_status", "1", time() + 3600 * 24 * 1000, "/");
                // set customer status 
                if ($client_data['customer']['email'] == $_SESSION['client']['customer']['email']) 
                    $_SESSION['client']['customer']['newsletter'] = 1;

            } else {
                
                msg("Can't subscribe {$client_data['customer']['email']}", 'error');
                $this->tpl->parse('content.form');
            }
            
        } else {
            $this->tpl->parse('content.form');
        }

        return true;
    }
}
