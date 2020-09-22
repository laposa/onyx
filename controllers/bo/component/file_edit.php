<?php
/** 
 * Copyright (c) 2008-2013 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/component/file.php');

class Onyx_Controller_Bo_Component_File_Edit extends Onyx_Controller_Bo_Component_File {

    /**
     * main action
     */
     
    public function mainAction() {
    
        $file_id = $this->GET['file_id'];
        $relation = $this->GET['relation'];
        
        $File = $this->initializeFile($relation);
        
        if (is_numeric($_POST['file']['id'])) {
            $File->getDetail($_POST['file']['id']);
            if ($File->updateFile($_POST['file'])) msg('updated');
        }
        
        $detail = $File->getDetail($file_id);
        $this->tpl->assign("SELECTED_role_{$detail['role']}", "selected='selected'");
        $this->tpl->assign('FILE', $detail);

        return true;
    }
}
