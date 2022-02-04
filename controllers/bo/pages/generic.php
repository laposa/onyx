<?php
/**
 * Pages controller
 *
 * Copyright (c) 2022 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onyx_Controller_Bo_Pages_Generic extends Onyx_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {

        if (strlen($this->GET['title']) > 0 ) $this->tpl->parse('content.header');

        return true;
    }
}
