<?php
/**
 * Registration controller
 *
 * Copyright (c) 2005-2021 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Component_Client_Registration extends Onyx_Controller {
    protected $enableReCaptcha;

    /**
     * main action
     */
    public function mainAction() {
        $this->enableReCaptcha = ONYX_RECAPTCHA_PUBLIC_KEY && ONYX_RECAPTCHA_PRIVATE_KEY;

        $this->commonAction();

        if ($_POST['save'] ?? false) $this->saveForm();

        $this->generateCountryList();
        $this->prepareCheckboxes();

        $client_data = $_POST['client'];

        // format birthday only if available to avoid 01/01/1970 by default
        if ($client_data['customer']['birthday'] != '') $client_data['customer']['birthday'] = strftime('%d/%m/%Y', strtotime($client_data['customer']['birthday']));

        $this->tpl->assign('CLIENT', $client_data);

        if ($this->enableReCaptcha) {
            $this->tpl->parse("content.recaptcha_field");
        }

        return true;
    }


    /**
     * process input form
     */
    public function saveForm()
    {
        $client_customer = $_POST['client']['customer'];
        $client_address = $_POST['client']['address'];
        $client_company = $_POST['client']['company'];

        if (is_numeric($client_customer['trade'])) $client_customer['account_type'] = 1; //requested trade account
        unset($client_customer['trade']);
        unset($client_customer['password1']);
        unset($client_address['delivery']['to_company']); // custom checkbox

         // check password match for non-social account
        if (!$this->Customer->isSocialAccount($_POST['client']['customer'])) {
            // only if repeat password is actually provided
            if ($_POST['client']['customer']['password'] && $_POST['client']['customer']['password1']) $password_match_status = $this->checkPasswordMatch($_POST['client']['customer']['password'], $_POST['client']['customer']['password1']);
            else $password_match_status = true;
        } else {
            $password_match_status = true;
        }

         // check birthday field format
        if ($client_customer['birthday']) {
            // check, expected as dd/mm/yyyy
            if (!preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $client_customer['birthday'])) {
                msg('Invalid format for birthday, use dd/mm/yyyy', 'error');
                return false;
            }

            // Format to ISO
            $client_customer['birthday'] = strftime('%Y-%m-%d', strtotime(str_replace('/', '-', $client_customer['birthday'])));
        }

        // verify captcha
        if ($this->enableReCaptcha) {
            $isCaptchaValid = verifyReCaptchaToken($_POST['g-recaptcha-response']);
            if (!$isCaptchaValid) {
                msg('Invalid security code', 'error');
                return false;
            }
        }
        
        // check validation of submited fields
        $client_customer = $this->Customer->prepareToRegister($client_customer);

        if ($client_customer && $password_match_status) {
            
            if (is_array($client_address) && trim($client_address['delivery']['name']) == '') {
                $client_address['delivery']['name'] = "{$client_customer['title_before']} {$client_customer['first_name']} {$client_customer['last_name']}";
            }

            // invoice address is same as delivery
            if (isset($client_address['invoices']['same_as_delivery']) && $client_address['invoices']['same_as_delivery'] == 1) $client_address['invoices'] = NULL;

            // register
            if($id = $this->Customer->registerCustomer($client_customer, $client_address, $client_company)) {
                msg(str_replace('%s', $id, I18N_COMPONENT_CLIENT_REGISTRATION_SUCCESS));

                // login
                if ($this->Customer->isSocialAccount($client_customer) || $this->Customer->isGuestAccount($client_customer)) {
                    $this->login($client_customer['email']);
                } else {
                    $this->login($client_customer['email'], $client_customer['password']);
                }

                // auto login (TODO allow to enable/disable this behaviour)
                $this->Customer->generateAndSaveOnyxToken($id);

                 // forward
                $this->forwardAfterLogin();
            } else {
                msg(I18N_COMPONENT_CLIENT_REGISTRATION_ERROR, 'error');
            }
        } else {
            msg(I18N_COMPONENT_CLIENT_REGISTRATION_ERROR, 'error');
        }
    }

    /**
     * login
     */
    public function login($username, $password = null) {
        if (is_null($password)) {
            $customer_detail = $this->Customer->login($username);
        } else {
            $md5_password = md5($password);
            $customer_detail = $this->Customer->login($username, $md5_password);
        }

        if ($customer_detail) {
            $_SESSION['client']['customer'] = $customer_detail;
        } else {
            msg('Login from registration failed. Please try again.', 'error');
        }
    }

    /**
     * forward action
     */
    public function forwardAfterLogin() {
        // include node configuration
        require_once('models/common/common_node.php');
        $node_conf = common_node::initConfiguration();
        //$this->tpl->assign('NODE_CONF', $node_conf);

        // check
        if ($this->GET['to'] && !$_SESSION['to']) {
            if ($this->GET['to'] == 'ajax') {
                return true;
            } else {
                onyxGoTo($this->GET['to']);
            }
        } else if ($_SESSION['to']) {
            $to = $_SESSION['to'];
            $_SESSION['to'] = false;
            onyxGoTo($to);
        } else {
            onyxGoTo("page/" . $node_conf['id_map-myaccount']);
        }
    }

    /**
     * check password match
     */
    public function checkPasswordMatch($password, $password1) {
        if ($password == $password1) {
            return true;
        } else {
            msg(I18N_COMPONENT_CLIENT_REGISTRATION_PASSWORD_NOT_MATCH, 'error');
            return false;
        }
    }

    /**
     * commonAction
     */
    public function commonAction() {
         // initialize
        require_once('models/client/client_customer.php');
        $this->Customer = new client_customer();
        $this->Customer->setCacheable(false);

        // autopopulate
        if (is_array($_SESSION['r_client']) && !is_array($_POST['client'])) {
            // populate all available fields
            $_POST['client'] = $_SESSION['r_client'];

        } else if (is_array($_SESSION['r_client']) && is_array($_POST['client'])) {
            // populate only (hidden) social fields
            $_POST['client']['customer']['facebook_id'] = $_SESSION['r_client']['customer']['facebook_id'];
            $_POST['client']['customer']['twitter_id'] = $_SESSION['r_client']['customer']['twitter_id'];
            $_POST['client']['customer']['google_id'] = $_SESSION['r_client']['customer']['google_id'];
            $_POST['client']['customer']['profile_image_url'] = $_SESSION['r_client']['customer']['profile_image_url'];

        }

         // check if we have some old information for this email address
        if (is_array($_POST['client']['customer']) && ONYX_CUSTOMER_ALLOW_ACCOUNT_MERGE) {
            if ($current_customer_data = $this->Customer->getClientByEmail($_POST['client']['customer']['email'])) {
                $_POST['client']['customer'] = array_merge($current_customer_data, $_POST['client']['customer']);
            }
        }

        // show password input only to non-social auth
        if (!$this->Customer->isSocialAccount($_POST['client']['customer'])) {
            $this->tpl->parse('content.password');
        }
    }

    /**
     * generateCountryList
     */
    public function generateCountryList() {
         // country list
        require_once('models/international/international_country.php');
        $Country = new international_country();
        $countries = $Country->listing("", "name ASC");

        if (!isset($_POST['client']['address']['delivery']['country_id'])) $_POST['client']['address']['delivery']['country_id'] = $Country->conf['default_id'];
        // address will be caught through relation
        // delivery
        foreach ($countries as $c) {
            if ($c['publish'] == 1) {
                if ($c['id'] == $_POST['client']['address']['delivery']['country_id']) $c['selected'] = "selected='selected'";
                else $c['selected'] = '';
                $this->tpl->assign('COUNTRY', $c);
                $this->tpl->parse('content.country_delivery.item');
            }
        }
        $this->tpl->parse('content.country_delivery');
    }

    /**
     * prepareCheckboxes
     */
    public function prepareCheckboxes() {
         // prepare for output
        if(isset($_POST['client']['customer']['newsletter'])) {
            $_POST['client']['customer']['newsletter'] = ($_POST['client']['customer']['newsletter'] == 1) ? 'checked="checked" ' : '';
        } else {
            $_POST['client']['customer']['newsletter'] = 'checked="checked" ';
        }

        if(isset($_POST['client']['customer']['trade'])) {
            $_POST['client']['customer']['trade'] = ($_POST['client']['customer']['trade'] == 1) ? 'checked="checked" ' : '';
        } else {
            $_POST['client']['customer']['trade'] = '';
        }

        // gender
        if ($_POST['client']['customer']['gender']) {
            $this->tpl->assign('gender_checked_' . $_POST['client']['customer']['gender'], 'checked');
        }
    }
}
