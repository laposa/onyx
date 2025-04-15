<?php
/**
 * File unlink
 *
 * Copyright (c) 2008-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/bo/component/file.php');

class Onyx_Controller_Bo_Component_File_Unlink extends Onyx_Controller_Bo_Component_File {

    /**
     * main action
     */
     
    public function mainAction() {
    
        $relation = $this->GET['relation'];
        $file_id = $this->GET['file_id'];
        
        $File = $this->initializeFile($relation);
                
        if (is_numeric($file_id)) {
            if ($File->unlinkFile($file_id)) {
                msg("Unlinked file $relation/$file_id");
                $this->tpl->parse('content.deleted');
            }
        }

        return true;
    }
}
