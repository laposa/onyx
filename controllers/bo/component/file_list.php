<?php
/** 
 * Copyright (c) 2008-2025 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/component/file.php');
require_once('models/common/common_image.php');

class Onyx_Controller_Bo_Component_File_List extends Onyx_Controller_Bo_Component_File {

    public $File;

    /**
     * main action
     */
     
    public function mainAction() {
    
        parent::mainAction();

        /**
         * inputs
         */
        
        $type = $this->GET['type'];
        $relation = $this->GET['relation'];
        
        if (is_numeric($this->GET['node_id'] ?? null)) $node_id = $this->GET['node_id'];
        else if (is_numeric($_POST['file']['node_id'] ?? null)) $node_id = $_POST['file']['node_id'];
        else {
            msg("Missing node_id", 'error');
            return false;
        }

        /**
         * get list of related files
         */

        $files = $this->File->listFiles($node_id);
        
        if (is_array($files) && count($files) > 0) {
        
            if (count($files) == 0) {
            
                $this->tpl->parse('content.empty');
            
            } else {

                foreach ($files as $file_detail) {
                    
                    $this->parseItem($file_detail, $type, $relation);
                    
                }
                
                $this->tpl->parse("content.list");
            }
        }

        return true;
    }
    
    /**
     * parse item
     */
    
    public function parseItem($file_detail, $type, $relation) {
                    
        $this->tpl->assign('FILE', $file_detail);
        
        switch ($type) {
        
            case'RTE':
                $this->tpl->parse("content.list.item.RTE_select");
            break;
            case 'CSS':
                $this->tpl->parse("content.list.item.CSS_select");
            break;
            default:
                $this->tpl->parse("content.list.item.default");
            break;
        }
            
        $this->tpl->parse("content.list.item");
    }
}
