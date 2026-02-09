<?php
/** 
 * Copyright (c) 2026 Laposa Limited (https://laposa.ie)
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

        if (is_numeric($this->GET['store_id'] ?? null)) $store_id = $this->GET['store_id'];
        else $store_id = false;

        if (is_numeric($store_id)) $this->tpl->parse('content.preview');

        return true;
    }
}
