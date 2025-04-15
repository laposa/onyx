<?php
/** 
 * Copyright (c) 2013 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('models/ecommerce/ecommerce_store.php');
require_once('models/common/common_node.php');

class Onyx_Controller_Component_Ecommerce_Store_Message extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        if (!empty($_SESSION['client']['customer']['id'])) {

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
