<?php
/** 
 * Copyright (c) 2009-2011 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Component_Taxonomy_Move extends Onxshop_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('models/common/common_taxonomy.php');
        $Taxonomy = new common_taxonomy();
        
        /**
         * check input variables
         */
         
        if (is_numeric($this->GET['source_node_id'])) $source_node_id = $this->GET['source_node_id'];
        else {
            msg("taxonomy_move: source_node_id is not numeric", 'error');
            return false;
        }
        
        if (is_numeric($this->GET['destination_node_id'])) {
            $destination_node_id = $this->GET['destination_node_id'];
        } else {
            msg("taxonomy_move: destination_node_id is not numeric", 'error');
            return false;
        }
        
        if (is_numeric($this->GET['position'])) $position = $this->GET['position'];
        else {
            msg("taxonomy_move: position is not numeric", 'error');
            return false;
        }
        
        /**
         * move
         */
        
        //msg($source_node_id, $destination_node_id, $position);
        if ($Taxonomy->moveItem($source_node_id, $destination_node_id, $position)) msg("Moved");
        else msg("Cannot move taxonomy to ($source_node_id, $destination_node_id, $position)", 'error');
        
        return true;
    }
}

