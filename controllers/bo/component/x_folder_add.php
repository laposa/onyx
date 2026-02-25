<?php
/**
 * Server filesystem browser
 *
 * Copyright (c) 2026 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onyx_Controller_Bo_Component_X_Folder_Add extends Onyx_Controller {

    public function mainAction() {
        
        if($_POST['save'] ?? false) {
            require_once('models/common/common_file.php');
            $File = new common_file();
            
            // Setting base paths
            $folder = $_POST['folder'] ?? '';
            $actual_folder = ONYX_PROJECT_DIR . 'var/files/'. urldecode($folder);
            
            $new_folder = isset($_POST['folder_name']) ? iconv("UTF-8", "ASCII//IGNORE", trim($_POST['folder_name'])) : '';
            
            $new_folder = preg_replace("/\s/", "-", $new_folder);
            $new_folder = preg_replace("/&[^([a-zA-Z;)]/", 'and-', $new_folder);
            $new_folder = preg_replace("/\-{2,}/", '-', $new_folder);
            
            // Create a new folder
            if ($new_folder != '') {
                $new_folder_full = $actual_folder . $new_folder;
                if (!mkdir($new_folder_full)) {
                    msg("Cannot create folder $new_folder in $actual_folder", 'error');
                } else {
                    msg("Folder $new_folder has been created in $actual_folder");
                }
            } else {
                msg("Folder name cannot be empty", 'error');
            }
        }

        $this->tpl->assign('FOLDER', $this->GET['folder'] ?? '');

        return true;
    }
}   
        
