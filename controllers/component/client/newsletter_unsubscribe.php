<?php
/**
 * Copyright (c) 2007-2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Component_Client_Newsletter_Unsubscribe extends Onxshop_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        if ($this->GET['email']) $email = $this->GET['email'];
        else if ($_POST['client']['customer']['email']) $email = $_POST['client']['customer']['email'];
        else $email = '';
        
        $this->tpl->assign('EMAIL', $email);
        
        require_once('models/client/client_customer.php');
        $Customer = new client_customer();
        $Customer->setCacheable(false);
        
        if ($_POST['submit']) {
            if ($Customer->newsletterUnSubscribe($email)) {
                //$this->tpl->parse('content.newsletter_unsubscribed');
                $hide_form = 1;
            } else {
                //
            }
        }
        
        if ($hide_form == 0) {
            $this->tpl->parse('content.request_form');
        }

        return true;
    }
}

