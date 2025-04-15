<?php
/**
 * File delete
 *
 * Copyright (c) 2008-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/bo/component/file.php');

class Onyx_Controller_Bo_Component_File_Delete extends Onyx_Controller_Bo_Component_File {

    /**
     * main action
     */
     
    public function mainAction() {
    
        //msg($file_path);
        require_once('models/common/common_file.php');
        $File = new common_file();
        
        $file_path = $File->decode_file_path($this->GET['file_path_encoded']);
        
        if ($file_path) {
            //msg($file_path);
            //TODO: safer to do another check for file_path string in node.content
            if ($File->deleteFile($file_path)) {
                msg("Deleted " . str_replace('var/files/', '', $file_path));
                $this->tpl->parse('content.deleted');
            }
        }

        return true;
    }
}
