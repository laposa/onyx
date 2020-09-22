<?php
/**
 * Copyright (c) 2014-2017 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onyx_Controller_Component_Google_Tag_Manager extends Onyx_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
        
        if (trim($GLOBALS['onyx_conf']['global']['google_tag_manager']) != '') {

            $this->tpl->parse('head.gtm');
            $this->tpl->parse('content.gtm');

        }

        return true;
    }
}
