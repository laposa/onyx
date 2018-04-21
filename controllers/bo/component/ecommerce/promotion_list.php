<?php
/**
 * Copyright (c) 2009-2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Promotion_List extends Onxshop_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        $this->initialisePromotion();

        if  (is_numeric($this->GET['limit_from'])) $from = $this->GET['limit_from'];
        else $from = 0;
        if (is_numeric($this->GET['limit_per_page'])) $per_page = $this->GET['limit_per_page'];
        else $per_page = 25;
        
        $promotion_list = $this->Promotion->getList($from, $per_page, $_SESSION['bo']['voucher-filter']);

        if (is_array($promotion_list)) $this->parseList($promotion_list);

        /**
         * Display pagination
         */
        
        $count = $this->Promotion->getFilteredCount($_SESSION['bo']['voucher-filter']);

        if ($count > 0) {       

            $_Onxshop_Request = new Onxshop_Request("component/pagination~limit_from=$from:limit_per_page=$per_page:count=$count~");
            $this->tpl->assign('PAGINATION', $_Onxshop_Request->getContent());
        }

        return true;
    }
    
    /**
     * initialize
     */
     
    public function initialisePromotion() {
        
        require_once('models/ecommerce/ecommerce_promotion.php');
    
        $this->Promotion = new ecommerce_promotion();
        
    }
    
    /**
     * parse list
     */
    
    public function parseList($list) {
    
        if (count($list) > 0) {
        
            foreach ($list as $item) {
                
                if ($item['publish'] == 0) $this->tpl->assign('DISABLED', 'disabled');
                else $this->tpl->assign('DISABLED', '');
                if ($item['customer_invite_count'] > 0) 
                    $item['customer_invite_count'] = $item['customer_invite_count'];
                else $item['customer_invite_count'] = '';
                $this->tpl->assign('ITEM', $item);
                $this->tpl->parse('content.item');
            }
        } else {
            $this->tpl->parse('content.empty');
        }
        
    }
}

