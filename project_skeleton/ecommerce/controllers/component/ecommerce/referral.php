<?php
/** 
 * Copyright (c) 2012-2013 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once "models/ecommerce/ecommerce_promotion.php";
require_once "models/ecommerce/ecommerce_promotion_code.php";
require_once "models/client/client_customer.php";
require_once 'models/common/common_email.php';

class Onxshop_Controller_Component_Ecommerce_Referral extends Onxshop_Controller {

    /**
     * Configuration
     */
    public $conf;

    /**
     * Model instance
     */
    public $Promotion;

    /**
     * Model instance
     */
    public $Promotion_Code;

    /**
     * Model instance
     */
    public $Customer;


    /**
     * initialisation
     */
    public function init()
    {
        $this->initModels();
        $this->initCustomer();
        $this->loadPromotion();

        $this->tpl->assign("AVAILABLE_REFERRALS_PER_PERSON", $this->conf['available_referrals_per_person']);
        $this->tpl->assign("DISCOUNT_VALUE", $this->conf['discount_value']);
        $this->tpl->assign("MINIMUM_ORDER_AMOUNT", $this->conf['minimum_order_amount']);
        $this->tpl->assign("REFERRAL_PAGE_ID", $this->conf['referral_page_id']);
    }

    /**
     * main action
     */
     
    public function mainAction() {

        $this->init();
        if (!$this->securityCheck($this->customer_id)) return false;

        if ($this->promotion) {

            $this->parsePromotion();

            if ($this->promotion['available_uses'] == 0) {
                $this->tpl->parse("content.my_referrals.no_referrals_available");
            } 

        } else {

            // no promotion - display generate button
            $this->tpl->parse("content.referral_generator");
        }

        return true;

    }



    /**
     * initialise models
     */
    protected function initModels()
    {
        $this->conf = ecommerce_promotion::initConfiguration();
        $this->Promotion = new ecommerce_promotion();
        $this->Promotion->setCacheable(false);
        $this->Promotion_Code = new ecommerce_promotion_code();
        $this->Promotion_Code->setCacheable(false);
        $this->Customer = new client_customer();
        $this->Customer->setCacheable(false);

    }



    /**
     * set active customer id
     */
    protected function initCustomer()
    {
        $this->customer_id = $this->getActiveCustomerId();
    }



    /**
     * security check, login should be forced from CMS require_login option
     */
    protected function securityCheck($customer_id)
    {
        if (!is_numeric($customer_id)) {
            msg('component/ecommerce/referral: login required', 'error');
            onxshopGoTo("/");
            return false;
        }

        return true;
    }



    protected function loadPromotion()
    {
        $promotions = $this->Promotion->listing("code_pattern LIKE 'REF-%' " . 
            "AND generated_by_customer_id = {$this->customer_id}");

        $this->promotion_id = (int) $promotions[0]['id'];
        if ($this->promotion_id == 0) return;

        $p = $this->Promotion->getDetail($this->promotion_id);
        if ($p['generated_by_customer_id'] != $this->customer_id) return;

        if ($p) {
            $p['num_uses'] = $this->Promotion->getCountUsageOfSingleCode($p['code_pattern']);
            $p['available_uses'] = max(0, $p['uses_per_coupon'] - $p['num_uses']);
            $this->promotion = $p;
        }

    }



    /**
     * Get active customer Id
     */
    protected function getActiveCustomerId() {
    
        if ($_SESSION['client']['customer']['id'] > 0) {
            $customer_id = $_SESSION['client']['customer']['id'];
        } else if (Onxshop_Bo_Authentication::getInstance()->isAuthenticated()) {
            $customer_id = $this->GET['customer_id'];
        } else {
            $customer_id = false;
        }
    
        return $customer_id;
    }



    /**
     * Display promotion code
     */
    protected function parsePromotion()
    {
        $this->promotion['used_class'] = $this->promotion['available_uses'] > 0 ? "unused" : "used";
        $this->tpl->assign("PROMOTION", $this->promotion);
        $this->tpl->parse("content.my_referrals.promotion");
        $this->tpl->parse("content.my_referrals");
    }


}
