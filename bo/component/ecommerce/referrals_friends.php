<?php
/**
 * Copyright (c) 2010-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/bo/component/ecommerce/promotion_list.php');
require_once "models/ecommerce/ecommerce_promotion.php";
require_once "models/ecommerce/ecommerce_promotion_code.php";
require_once "models/ecommerce/ecommerce_invoice.php";
require_once "models/client/client_customer.php";

class Onyx_Controller_Bo_Component_Ecommerce_Referrals_Friends extends Onyx_Controller {

    /**
     * Model instance
     */
    public $Promotion;

    /**
     * Model instance
     */
    public $Promotion_Code;


    /**
     * main action
     */
     
    public function mainAction()
    {
        /**
         * initializace models
         */
        $this->Promotion = new ecommerce_promotion();
        $this->Promotion->setCacheable(false);
        $this->Promotion_Code = new ecommerce_promotion_code();
        $this->Promotion_Code->setCacheable(false);
        $this->Customer = new client_customer();
        $this->Customer->setCacheable(false);
        $this->Order = new ecommerce_order();
        $this->Order->setCacheable(false);

        // render
        $customer_id = $this->GET['customer_id'];
        $this->parseFriendsList($customer_id);

        return true;
    }


    /**
     * Load and display all invited friends
     * @return [type] [description]
     */
    protected function parseFriendsList($customer_id)
    {
        $rew_promotions = $this->Promotion->listing("code_pattern LIKE 'REW-%' AND limit_by_customer_id  = $customer_id");
        $ref_promotions = $this->Promotion->listing("code_pattern LIKE 'REF-%' AND generated_by_customer_id = $customer_id");
        
        foreach ($ref_promotions as $ref_promotion) {

            $ref_usage = $this->Promotion_Code->getUsageOfSingleCode($ref_promotion['code_pattern']);

            if ($ref_usage) {
                foreach ($ref_usage as $item) {

                    $hasReward = false;
                    foreach ($rew_promotions as $rew_promotion) {

                        if ($rew_promotion['generated_by_order_id'] == $item['order_id']) {
                            $hasReward = true;
                            $rew_promotion['friend'] = $this->Customer->getDetail($rew_promotion['generated_by_customer_id']);
                            $count_usage = $this->Promotion->getCountUsageOfSingleCode($rew_promotion['code_pattern']);
                            $rew_promotion['used'] = $count_usage > 0 ? "Yes" : "No";
                            $rew_promotion['address'] = $this->getAddresses($rew_promotion['generated_by_order_id']);
                            $this->tpl->assign("ITEM", $rew_promotion);
                            $this->tpl->parse("content.friends_list.item");
                        }
                    }

                    if (!$hasReward) {
                        $order = $this->Order->getOrder($item['order_id']);
                        $item['friend'] = $this->Customer->getDetail($order['basket']['customer_id']);
                        $item['order'] = $order;
                        $this->tpl->assign("ITEM", $item);
                        $this->tpl->parse("content.friends_list.unfinished");
                    }
                }
            }
        }

        // parse invited friends
        if (count($ref_promotions) == 0) $this->tpl->parse("content.friends_list.none");
        $this->tpl->parse("content.friends_list");
    }

    protected function getAddresses($order_id)
    {
        $Invoice = new ecommerce_invoice();
        $Invoice->setCacheable(false);
        $invoice = $Invoice->getInvoiceForOrder($order_id);
        return array(
            "address_invoice" => $invoice['address_invoice'],
            "address_delivery" => $invoice['address_delivery']
        );
    }

}

