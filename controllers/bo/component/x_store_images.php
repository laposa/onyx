<?php
/** 
 * Copyright (c) 2025 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/component/x.php');

class Onyx_Controller_Bo_Component_X_Store_Images extends Onyx_Controller_Bo_Component_X {

    /**
     * main action
     */
     
    public function mainAction() {

        /**
         * input validation
         */

        if (is_numeric($this->GET['node_id'] ?? null)) $product_id = $this->GET['node_id'];
        else $product_id = false;

        if (is_numeric($product_id)) $this->tpl->parse('content.preview');

        return true;
    }
}
