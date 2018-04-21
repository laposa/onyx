<?php
/**
 *
 * Copyright (c) 2011-2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Bo_Pages_Client_Customers extends Onxshop_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        /**
         * conditional display of different buttons
         */
         
        if ($_SESSION['bo']['customer-filter-selected_group_id'] > 0) $this->tpl->parse('content.modify_group');
        else $this->tpl->parse('content.create_new_group');
                
        return true;
    }
}
