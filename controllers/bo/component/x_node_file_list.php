<?php
/** 
 * Copyright (c) 2008-2025 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/component/file.php');

class Onyx_Controller_Bo_Component_X_Node_File_List extends Onyx_Controller_Bo_Component_File {

    public $File;

    /**
     * main action
     */
     
    public function mainAction() {
    
        //initialize file based on relation (node, recipe, product, ...)
        $relation = $_POST['relation'] ?? $this->GET['relation'] ?? 'node';
        $this->File = $this->initializeFile($relation);

        $node_id = $_POST['node_id'] ?? $this->GET['node_id'] ?? null;
        
        if (!is_numeric($node_id)) {
            msg("Missing node_id", 'error');
            return false;
        }

        /**
         * get list of related files
         */

        $files = $this->File->listFiles($node_id);

        if (is_array($files)) {
            
            if (count($files) == 0) {
                $this->tpl->parse('content.empty');
            } else {
                foreach ($files as $file_detail) {
                    $this->tpl->assign('FILE', $file_detail);
                    $this->tpl->parse("content.list.item");
                }
                
                $this->tpl->parse("content.list");
            }
        }

        /**
         * change position
         */
        if ($_POST['reposition'] ?? false) {
            if (!is_numeric($_POST['file_id']) || !is_numeric($_POST['position'])) return false;

            if ($files) {
                
                $i = 0;
                $sibling_count = count($files);
                
                foreach ($files as $sibling) {
                
                    if ($sibling['id'] == $_POST['file_id']) {
                        $new_priority = ($sibling_count - $_POST['position']) * 10 + 5;
                    } else {
                        $new_priority = ($sibling_count - $i) * 10;
                        $i++;
                    }

                    $file_data = $this->File->getDetail($sibling['id']);
                    $file_data['priority'] = $new_priority;
                    // these throw database error - unknon columns
                    unset($file_data['file_path_encoded']);
                    unset($file_data['info']);
                    $this->File->updateFile($file_data);
                }

                msg("File {$_POST['title']} has been successfully moved to position {$_POST['position']}");
                return true;

            } else {
                msg("Cannot change position, file list is empty", 'error');
                return false;
            }
        }

        $this->tpl->assign('FILE', $file_detail);

        return true;
    }
}
