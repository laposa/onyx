<?php
/**
 * Frontend edit controller
 *
 * Copyright (c) 2022 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onyx_Controller_Bo_Fe_Menu extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
        
        // permission check
        $auth = Onyx_Bo_Authentication::getInstance();
        if ($auth->hasAnyPermission('back_office')) $this->tpl->parse('content.backoffice');
        if ($auth->hasAnyPermission('_all_')) $this->tpl->parse('content.css_edit');
        
        
        return true;
    }
}
