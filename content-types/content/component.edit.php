<?php
/** 
 * Copyright (c) 2006-2019 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/bo/node/content/default.php');

class Onyx_Controller_Bo_Node_Content_Component extends Onyx_Controller_Bo_Node_Content_Default {
    
    /**
     * pre action
     */
     
    function pre() {
        
        parent::pre();
        
        $_POST['node']['component']['template'] = trim($_POST['node']['component']['template']);
        $_POST['node']['component']['controller'] = trim($_POST['node']['component']['controller']);
        $_POST['node']['component']['parameter'] = trim($_POST['node']['component']['parameter']);
        
        /**
         * content list
         */
         
        $children = $this->Node->getChildren($this->GET['id']);
        if (count($children) > 0) {
            foreach ($children as $item) {
                $this->tpl->assign ('ITEM', $item);
                $this->tpl->parse('content.variables.item');
            }
            $this->tpl->parse('content.variables');
        }
    }
}
