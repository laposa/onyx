<?php
/**
 * Copyright (c) 2008-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Component_Filter extends Onyx_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        if (is_numeric($this->GET['node_id'])) $node_id = $this->GET['node_id'];
        if ($this->GET['template'] == '') $template = 'menu_UL';
        
        $Onyx_Request = new Onyx_Request("component/menu&type=taxonomy&level=2&display_all=1&id=$node_id&template=$template");
        
        $this->tpl->assign("FILTER", $Onyx_Request->getContent());

        return true;
    }
}
