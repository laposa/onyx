<?php
/**
 * Copyright (c) 2013 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/component/client/login.php');

class Onyx_Controller_Component_Client_Login_Or_Register extends Onyx_Controller_Component_Client_Login {

    /**
     * main action
     */
     
    public function mainAction() {
    
        /**
         * include node configuration
         */
        
        require_once('models/common/common_node.php');
        $node_conf = common_node::initConfiguration();
        
        /**
         * customer detail
         */
         
        require_once('models/client/client_customer.php');
        
        $Customer = new client_customer();
        $Customer->setCacheable(false);
        
        if ($_POST['register'] || $_POST['login']) {
        
            //check validation of submited fields
            if ($Customer->checkLoginId($_POST['client']['customer'])) {
            
                $_SESSION['r_client'] = $_POST['client'];
                $this->dispatchToRegistration($node_conf);
                
            } else  {
                
                $this->tpl->assign('CLIENT', $_POST['client']);
                $this->tpl->parse('content.login');
            
            }
            
        } else {
            
            $this->tpl->parse('content.form');
        
        }
        
        /**
         * check status
         */
        
        if ($_SESSION['client']['customer']['id'] > 0 && is_numeric($_SESSION['client']['customer']['id'])) {
            
            $this->actionAfterLogin();
            
        }

        return true;
    }
    
    /**
     * send to registration
     */
     
    function dispatchToRegistration($node_conf) {
        if ($this->GET['to']) $_SESSION['to'] = $this->GET['to'];
        onyxGoTo('/page/'.$node_conf['id_map-registration']);
    }
}
