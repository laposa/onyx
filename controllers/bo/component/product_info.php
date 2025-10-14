<?php
/** 
 * Copyright (c) 2008-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/bo/component.php');

class Onyx_Controller_Bo_Component_Product_Info extends Onyx_Controller_Bo_Component {

    /**
     * main action
     */
     
    public function mainAction() {

        parent::assignProductData();

        parent::parseTemplate();

        return true;
    }
}   

