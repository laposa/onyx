<?php
/**
 * Copyright (c) 2010-2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/bo/component/ecommerce/promotion_list.php');

class Onyx_Controller_Bo_Component_Ecommerce_Promotion_List_Report extends Onyx_Controller_Bo_Component_Ecommerce_Promotion_List {

    /**
     * main action
     */
     
    public function mainAction() {
    
        $this->initialisePromotion();
        
        $promotion_list = $this->getList();

        if (is_array($promotion_list) && count($promotion_list) > 0) {
            $this->parseList($promotion_list);
            $this->tpl->parse('content.foot');
        }
        else $this->tpl->parse('content.empty');

        return true;
    }

    /**
     * get list
     */
     
    public function getList() {
        
        $breakdown_period = $this->getBreakdownPeriod();
        
        $filter = array();
        $filter['created_from'] = $breakdown_period['from'];
        $filter['created_to'] = $breakdown_period['to'];
        
        $promotion_list = $this->Promotion->getAdvanceList($filter);
    
        $this->countTotal($promotion_list);
        
        return $promotion_list;
        
    }
    
    /**
     * count total
     */
     
    public function countTotal($promotion_list) {
    
        if (!is_array($promotion_list)) return false;
        
        $total_count = 0;
        $total_goods_net = 0;
        $total_face_value_voucher = 0;
        $total_discount = 0;
        
        foreach ($promotion_list as $item) {
            $total_count += $item['count'];
            $total_goods_net += $item['sum_goods_net'];
            $total_face_value_voucher += $item['sum_face_value_voucher'];
            $total_discount += $item['sum_discount'];
        }
        
        $this->tpl->assign('TOTAL_COUNT', $total_count);
        $this->tpl->assign('TOTAL_GOODS_NET', $total_goods_net);
        $this->tpl->assign('TOTAL_FACE_VALUE_VOUCHER', $total_face_value_voucher);
        $this->tpl->assign('TOTAL_DISCOUNT', $total_discount);
            
    }


    /**
     * get breakdown period
     */
    
    public function getBreakdownPeriod() {
    
        if (is_array($this->GET['reports-filter'])) {
            $breakdown_period['from'] = $this->GET['reports-filter']['from'];
            $breakdown_period['to'] = $this->GET['reports-filter']['to'];
        } else if (is_array($_SESSION['bo']['reports-filter'])) {
            $breakdown_period['from'] = $_SESSION['bo']['reports-filter']['from'];
            $breakdown_period['to'] = $_SESSION['bo']['reports-filter']['to'];
        } else {
            msg('period not selected', 'error');
            $breakdown_period['from'] = '2010-05-01';
            $breakdown_period['to'] = '2010-06-01';
        }
        
        return $breakdown_period;
    }
}

