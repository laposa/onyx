<?php
/** 
 * Copyright (c) 2008-2018 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/component/file.php');

class Onyx_Controller_Bo_Component_File_Add extends Onyx_Controller_Bo_Component_File {

    /**
     * main action
     */
     
    public function mainAction() {
    
        $relation = $this->GET['relation'];
        
        $File = $this->initializeFile($relation);
        
        if(isset($_POST['file']['link_to_node_id']) && !is_numeric($_POST['file']['link_to_node_id'])) unset($_POST['file']['link_to_node_id']);
        
        if (isset($_POST['add']) && $_POST['add'] == 'add') {
        
            if ($id = $File->insertFile($_POST['file'])) {
                msg("File $relation/$id inserted");
            }
        
            $this->tpl->assign('FILE', $_POST['file']);
            
        } else {
        
            $file_data['src'] = str_replace(ONYX_PROJECT_DIR, "", $File->decode_file_path($this->GET['file_path_encoded']));
            $file_data['node_id'] = $this->GET['node_id'];
            $file_data['relation'] = $this->GET['relation'];
        
            if (trim($file_data['title'] ?? '') == '') {
                $file_info = $File->getFileInfo(ONYX_PROJECT_DIR . $file_data['src']);
                $file_data['title'] = $file_info['filename'];
                
                /**
                 * clean
                 */
                 
                $file_data['title'] = $this->cleanFileTitle($file_data['title']);
            
            }
        
            $this->tpl->assign('FILE', $file_data);
        
        }
        
        $this->tpl->assign("SELECTED_role_{$this->GET['role']}", "selected='selected'");

        return true;
    }
    
    /**
     * clean title
     */
     
    public function cleanFileTitle($title) {
        
        $title = preg_replace('/(\.jpg)?(\.jpeg)?(\.gif)?(\.png)?(\.doc)?(\.docx)?(\.pdf)?(\.zip)?(\.svg)?$/i', '', $title);
        $title = preg_replace('/[_-]/', ' ', $title);
        $title = ucfirst($title);
        
        return $title;
    }
}
