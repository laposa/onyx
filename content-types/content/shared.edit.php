<?php
/** 
 * Copyright (c) 2006-2017 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/bo/node/content/default.php');

class Onyx_Controller_Bo_Node_Content_Shared extends Onyx_Controller_Bo_Node_Content_Default {

    /**
     * pre action
     */
     
    function pre() {
        
        parent::pre();
        
        $node_id = $this->GET['id'];
        $select = $this->GET['select'];
        
        $node_detail = $this->Node->detail($node_id);
        
        if (is_numeric($select)) {

            $selected_node_detail = $this->Node->detail($select);
            
            if ($selected_node_detail['node_group'] == 'content') {
                    
                $node_detail['content'] = $select;
                    
                if ($this->Node->update($node_data)) {
                    msg('updated');
                }
    
            } else {
                msg("This element is a " . ucfirst($selected_node_detail['node_group']) . ", not a Content!", 'error');
            }
        }
    
    }
}
