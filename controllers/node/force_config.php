<?php
/** 
 * Copyright (c) 2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once("controllers/node.php");

class Onyx_Controller_Node_Force_Config extends Onyx_Controller_Node {

    /**
     * main action
     */
     
    public function mainAction() {

        $node_id = $this->GET['node_id'];

        if (is_numeric($node_id)) {
            $global_conf_node_overwrites = $this->initGlobalNodeConfigurationOverwrites($node_id);
            $GLOBALS['onyx_conf'] = $this->array_replace_recursive($GLOBALS['onyx_conf'], $global_conf_node_overwrites);
        }

        return true;
    }
    
}
