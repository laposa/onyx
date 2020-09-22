<?php
/**
 * Checkout progress
 *
 * Copyright (c) 2010-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/component/ecommerce/checkout.php');

class Onxshop_Controller_Component_Ecommerce_Checkout_Progress extends Onxshop_Controller_Component_Ecommerce_Checkout {

    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('models/common/common_node.php');
        $Node = new common_node;
        
        $parent_page_id = $_SESSION['active_pages'][0];

        $checkout_pages_id_map = $this->getCheckoutPagesIdMap();
        //print_r($checkout_pages_id_map);
        
        foreach ($checkout_pages_id_map as $key=>$id) {
            //msg("$id == $parent_page_id");
            if ($id == $parent_page_id) $selected[$key] = 'active';
            else $selected[$key] = '';
        }
        
        $this->tpl->assign('SELECTED', $selected);

        //conditional menu title
        if (is_numeric($_SESSION['client']['customer']['id'])) $this->tpl->assign('CHECKOUT_LOGIN', 'Customer Details');
        else $this->tpl->assign('CHECKOUT_LOGIN', 'Customer Login');
        
        $this->parseItems($parent_page_id, $checkout_pages_id_map);
        
                
                
        return true;
    }
    
    /**
     * get checkout pages ids
     */
     
    public function getCheckoutPagesIdMap() {
    
        require_once('models/common/common_node.php');
        $node_conf = common_node::initConfiguration();
        
        $this->tpl->assign('NODE_CONF', $node_conf);
        
        $conf['id_map-checkout_basket'] = $node_conf['id_map-checkout_basket'];
        $conf['id_map-checkout_login'] = $node_conf['id_map-checkout_login'];
        $conf['id_map-checkout_delivery_options'] = $node_conf['id_map-checkout_delivery_options'] ;
        $conf['id_map-checkout_gift'] = $node_conf['id_map-checkout_gift'];
        $conf['id_map-checkout_summary'] = $node_conf['id_map-checkout_summary'];
        $conf['id_map-checkout_payment'] = $node_conf['id_map-checkout_payment'];
        $conf['id_map-checkout_payment_success'] = $node_conf['id_map-checkout_payment_success'];
        $conf['id_map-checkout_payment_failure'] = $node_conf['id_map-checkout_payment_failure'];
                
        return $conf;
    }
    
    
    /**
     * parseItems
     */
    
    public function parseItems($parent_page_id, $checkout_pages_id_map) {
    
        switch ($parent_page_id) {
        
            case $checkout_pages_id_map['id_map-checkout_basket']:
                $this->tpl->assign("ACTIVE", 'active');
                $this->tpl->parse('content.basket_nolink');
                $this->tpl->assign("ACTIVE", '');
                $this->tpl->parse('content.login_nolink');
                $this->tpl->parse('content.delivery_options_nolink');
                $this->tpl->parse('content.gift_nolink');
                $this->tpl->parse('content.summary_nolink');
                $this->tpl->parse('content.payment_nolink');
            break;
            
            case $checkout_pages_id_map['id_map-checkout_login']:
                $this->tpl->parse('content.basket_link');
                $this->tpl->assign("ACTIVE", 'active');
                $this->tpl->parse('content.login_nolink');
                $this->tpl->assign("ACTIVE", '');
                $this->tpl->parse('content.delivery_options_nolink');
                $this->tpl->parse('content.gift_nolink');
                $this->tpl->parse('content.summary_nolink');
                $this->tpl->parse('content.payment_nolink');
            break;
            
            case $checkout_pages_id_map['id_map-checkout_delivery_options']:
                $this->tpl->parse('content.basket_link');
                $this->tpl->parse('content.login_link');
                $this->tpl->assign("ACTIVE", 'active');
                $this->tpl->parse('content.delivery_options_nolink');
                $this->tpl->assign("ACTIVE", '');
                $this->tpl->parse('content.gift_nolink');
                $this->tpl->parse('content.summary_nolink');
                $this->tpl->parse('content.payment_nolink');
            break;

            case $checkout_pages_id_map['id_map-checkout_gift']:
                $this->tpl->parse('content.basket_link');
                $this->tpl->parse('content.login_link');
                $this->tpl->parse('content.delivery_options_link');
                $this->tpl->assign("ACTIVE", 'active');
                $this->tpl->parse('content.gift_nolink');
                $this->tpl->assign("ACTIVE", '');
                $this->tpl->parse('content.summary_nolink');
                $this->tpl->parse('content.payment_nolink');
            break;

            case $checkout_pages_id_map['id_map-checkout_summary']:
                $this->tpl->parse('content.basket_link');
                $this->tpl->parse('content.login_link');
                $this->tpl->parse('content.delivery_options_link');
                $this->tpl->parse('content.gift_link');
                $this->tpl->assign("ACTIVE", 'active');
                $this->tpl->parse('content.summary_nolink');
                $this->tpl->assign("ACTIVE", '');
                $this->tpl->parse('content.payment_nolink');
            break;

            case $checkout_pages_id_map['id_map-checkout_payment']:
            case $checkout_pages_id_map['id_map-checkout_payment_success']:
            case $checkout_pages_id_map['id_map-checkout_payment_failure']:
                $this->tpl->parse('content.basket_nolink');
                $this->tpl->parse('content.login_nolink');
                $this->tpl->parse('content.delivery_options_nolink');
                $this->tpl->parse('content.gift_nolink');
                $this->tpl->parse('content.summary_nolink');
                $this->tpl->assign("ACTIVE", 'active');
                $this->tpl->parse('content.payment_nolink');
                $this->tpl->assign("ACTIVE", '');
            break;


            
        }

    }
    
}
