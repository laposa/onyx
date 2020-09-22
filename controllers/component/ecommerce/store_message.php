<?php
/** 
 * Copyright (c) 2013 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('models/ecommerce/ecommerce_store.php');
require_once('models/common/common_node.php');

class Onxshop_Controller_Component_Ecommerce_Store_Message extends Onxshop_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        if ($_SESSION['client']['customer']['id'] > 0) {

            $store_id = (int) $_SESSION['client']['customer']['store_id']; 

            if ($store_id > 0) {

                $Store = new ecommerce_store();
                $store = $Store->detail($store_id);
                $store_page = $Store->getStoreHomepage($store_id);

                $this->tpl->assign("STORE_PAGE", $store_page);
                $this->tpl->assign("STORE", $store);
                $this->tpl->parse('content.authorised_selected');
            }
            else {
                $this->tpl->parse('content.authorised_not_selected');
            }
            
        } else {
        
            $this->tpl->parse('content.anonymouse');
        
        }

        return true;
        
    }



}
