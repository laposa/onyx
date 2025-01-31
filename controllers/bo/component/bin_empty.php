<?php
/** 
 * Copyright (c) 2024 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onyx_Controller_Bo_Component_Bin_Empty extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
        
        require_once('models/common/common_node.php');
        
        $Node = new common_node();
        $bin_id = $Node->conf['id_map-bin'];
        $node_data = $Node->detail($bin_id);
        $node_data['other_data'] = unserialize($node_data['other_data'] ?? '');

        if (!is_numeric($bin_id)) {
            msg('id_map-bin: id is not numeric', 'error');
            return false;
        }
            
        if($node_data['other_data']['last_empty'] ?? false) {
            $last_empty = date('d. m. Y H:i:s', strtotime($node_data['other_data']['last_empty']));
            $this->tpl->assign("LAST_EMPTY", $last_empty);
            $this->tpl->parse('content.button.last_empty');
        }
        
        $this->tpl->parse('content.button');

        return true;
    }
    
}
