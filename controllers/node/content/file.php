<?php
/**
 * Copyright (c) 2006-2017 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/node/content/default.php');

class Onyx_Controller_Node_Content_File extends Onyx_Controller_Node_Content_Default {

    /**
     * main action
     */
     
    public function mainAction() {

        require_once('models/common/common_node.php');  
        $Node = new common_node();
        $node_id = (int)$this->GET['id'];

        $node_data = $Node->nodeDetail($node_id);
        
        require_once('models/common/common_file.php');
        $File = new common_file();
        
        $files = $File->listFiles($node_id);
        
        if (is_array($files)){
            foreach ($files as $file) {
                $this->tpl->assign('FILE', $file);
                $this->tpl->parse('content.item');
            }
        }
        
        $this->tpl->assign('NODE', $node_data);
        
        $this->displayTitle($node_data);
            
        return true;
    }
}
