<?php
/**
 * Frontend edit controller
 *
 * Copyright (c) 2008-2017 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onyx_Controller_Bo_Fe_edit extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
        
        require_once 'models/common/common_node.php';
        $this->Node = new common_node();

        //check if we are comming from backoffice
        if ($_SERVER['REQUEST_URI'] == '/edit') {
            
            if (is_array($_SESSION['active_pages']) && count($_SESSION['active_pages']) > 0) {
            
                $node_id = $_SESSION['active_pages'][0];
                
                // if last visited page (or other resource) was 404, change to homepage to avoid bo_login component reporting error
                if ($node_id == $this->Node->conf['id_map-404']) $node_id = $this->Node->conf['id_map-homepage'];
                
                $request = translateURL("page/$node_id");
                header("Location: {$request}");
                exit;
                
            } else {
                
                header("Location: /");
            
            }
        }
        
        return true;
    }
}
