<?php
/**
 * Copyright (c) 2006-2017 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onyx_Controller_Component_Client_Password_Reset extends Onyx_Controller {
    
    /**
     * main action
     */
    
    public function mainAction() {
        
        /**
         * initialise client_customer object
         */
        
        require_once('models/client/client_customer.php');
        $Customer = new client_customer();
        $Customer->setCacheable(false);
        
        require_once('models/common/common_node.php');
        $node_conf = common_node::initConfiguration();

        $hide_form = 0;

        /**
         * Prefill from GET
         */
        if (strlen($this->GET['email'] ?? '') > 0) {
            $client = array('customer' => array('email' => $this->GET['email']));
            $this->tpl->assign('CLIENT', $client);
        }
        
        /**
         * process when submited
         */
         
        if (isset($_POST['submit']) && $_POST['submit'] && $_POST['action'] == 'email') {
        
            /**
             * assign first
             */
             
            if (is_array($_POST['client'])) {
                $this->tpl->assign('CLIENT', $_POST['client']);
            }
        
            /**
             * get detail
             */
             
            $customer_data = $Customer->getClientByEmail($_POST['client']['customer']['email']);
            
            /**
             * when real client, get key
             */
             
            if (is_array($customer_data)) {
                $current_key = $Customer->getPasswordKey($_POST['client']['customer']['email']);
                $customer_data['password_key'] = $current_key;
            } else {
                msg(I18N_EMAIL_IS_NOT_REGISTERED, 'error');
            }

            /**
             * if key was generated successfully, than send it by email
             */

            if ($current_key) {

                require_once('models/common/common_email.php');
                $EmailForm = new common_email();

                //this allows use customer data and company data in the mail template
                //is passed as DATA to template in common_email->_format
                $GLOBALS['common_email']['customer'] = $customer_data;

                if (!$EmailForm->sendEmail('password_change_request', 'n/a', $customer_data['email'], $customer_data['first_name'] . " " . $customer_data['last_name'])) {
                    msg("Can't send email with request for password reset", 'error');
                }
                
                $this->tpl->parse('content.request_sent');
                $hide_form = 1;
            }
        }
        
        /**
         * allow set new password when valid email and key is provided
         */
         
        if  (isset($this->GET['email']) && $this->GET['email'] && isset($this->GET['key']) && $this->GET['key']) {
        
            $client = $Customer->getClientByEmail($this->GET['email']);

            if (is_array($client)) {
                $current_key = $Customer->getPasswordKey($this->GET['email']);
                if ($current_key == $this->GET['key'] && strlen($current_key) == 32) {


                    if ($_POST['submit'] && $_POST['action'] == 'reset') {

                        $customer_data = $_POST['client']['customer'];

                        if (strlen($customer_data['password']) > 0) {

                            $client_current_data = $Customer->getClientByEmail($this->GET['email']);

                            if ($Customer->updatePassword($client_current_data['password'], $customer_data['password'], $customer_data['password1'], $client_current_data)) {
                                
                                $this->tpl->parse('content.new_password_set');
                                $hide_form = 1;
                                
                                /**
                                 * use different login path when password reset was requested from backoffice
                                 */
                                 
                                if ($this->GET['backoffice'] == 1) $login_path = '/edit';
                                else $login_path = '/page/' . $node_conf['id_map-login'];
                                
                                /**
                                 * send email
                                 */
                                 
                                require_once('models/common/common_email.php');
                                $EmailForm = new common_email();
                            
                                //this allows use customer data and company data in the mail template
                                //is passed as DATA to template in common_email->_format
                                $GLOBALS['common_email']['customer'] = $client_current_data;
                                $GLOBALS['common_email']['login_path'] = $login_path;
                                
                                if (!$EmailForm->sendEmail('password_changed', 'n/a', $client_current_data['email'], $client_current_data['first_name'] . " " . $client_current_data['last_name'])) {
                                    msg('Password reset email sending failed.', 'error');
                                }

                                /**
                                 * backoffice users forward to homepage, for other users execute login and redirect to My Account page
                                 */
                                 
                                if ($this->GET['backoffice'] == 1) {
                                    
                                    onyxGoTo("/edit");
                                
                                } else {
                                    
                                    if ($customer_detail = $Customer->login($this->GET['email'], md5($customer_data['password']))) {
                                        $_SESSION['client']['customer'] = $customer_detail;
                                        if ($_SESSION['to']) {
                                            $to = $_SESSION['to'];
                                            $_SESSION['to'] = false;
                                            onyxGoTo($to);
                                        } else {
                                            onyxGoTo("page/" . $node_conf['id_map-myaccount']);
                                        }
                                    }
                                    
                                }

                            }

                        } else {
                            msg("No password provided.", 'error');
                        }
                    }

                    if ($hide_form == 0) $this->tpl->parse('content.new_password_form');
                    $hide_form = 1;

                } else {
                    msg(I18N_WRONG_KEY, 'error');
                }
            } else {
                    msg(I18N_WRONG_EMAIL_ADDRESS, 'error');
            }
        }

        /**
         * conditional display form
         */
        
        if ($hide_form == 0) {
            $this->tpl->parse('content.request_form');
        }

        return true;
    }
}
