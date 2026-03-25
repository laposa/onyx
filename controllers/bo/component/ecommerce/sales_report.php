<?php
/** 
 * Copyright (c) 2010-2026 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/bo/component/ecommerce/reports_filter.php');
class Onyx_Controller_Bo_Component_Ecommerce_Sales_Report extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
        
        require_once('models/ecommerce/ecommerce_order.php');
        
        $Order = new ecommerce_order();
    
        $breakdown_period = Onyx_Controller_Bo_Component_Ecommerce_Reports_Filter::getDateRange();
        
        $product_list = $Order->getProductSalesList($breakdown_period['from'], $breakdown_period['to']);
        
        $this->renderList($product_list);
        
        return true;
    }
     
    /**
     * render list
     */
     
    public function renderList($product_list) {
    
        if (!is_array($product_list) || count($product_list) == 0) {

            $this->tpl->parse('content.empty');
            return false;
        }
                    
        /**
         * Display items and count total revenu
         */
        
        $total_units = 0;
        $total_revenue = 0;
        
        foreach ($product_list as $item) {
            $this->tpl->assign('ITEM', $item);
            $this->tpl->parse('content.item');
            
            $total_units = $total_units + $item['count'];
            $total_revenue = $total_revenue + $item['revenue'];
        }

        $this->tpl->assign('TOTAL_UNITS', $total_units);
        $this->tpl->assign('TOTAL_REVENUE', $total_revenue);
        $this->tpl->parse('content.foot');
        
    }
}
