<?php
/** 
 * Copyright (c) 2009-2015 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onyx_Controller_Component_Breadcrumb_Taxonomy extends Onyx_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
        
        require_once('models/common/common_taxonomy_tree.php');
        $Node = new common_taxonomy_tree();
        
        if (!is_numeric($this->GET['id'])) {
            msg('component/breadcrumb_taxonomy: id must be numeric', 'error', 2);
            return true; // stop here without returning error
        }
        
        $path = $Node->getFullPathDetailForBreadcrumb($this->GET['id']);    
    
        foreach ($path as $item) {
            $this->tpl->assign('ITEM', $item);
            $this->tpl->parse('content.item');
        }

        return true;
    }
}
