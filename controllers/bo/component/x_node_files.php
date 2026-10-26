<?php
/** 
 * Copyright (c) 2008-2025 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/component/x.php');

class Onyx_Controller_Bo_Component_X_Node_Files extends Onyx_Controller_Bo_Component_X {

    public $File;

    /**
     * main action
     */
     
    public function mainAction() {
    
        $node_id = $_POST['node_id'] ?? $this->GET['node_id'] ?? null;
        
        if (!is_numeric($node_id)) {
            return false;
        }

        // hint for image specifications
        $hint = (array) ($this->GET['hint'] ?? []);
        foreach ($hint as $item) {
            if (!is_string($item)) continue;
            $this->tpl->assign('HINT', htmlspecialchars($item));
            $this->tpl->parse('content.preview.info.hint');
            $this->tpl->parse('content.edit.info.hint');
        }
        if (count($hint) > 0) {
            $this->tpl->parse('content.preview.info');
            $this->tpl->parse('content.edit.info');
        }

        parent::parseTemplate();

        return true;
    }
}
