<?php
/** 
 * Copyright (c) 2008-2026 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/component/file.php');

class Onyx_Controller_Bo_Component_File_Edit extends Onyx_Controller_Bo_Component_File {

    /**
     * main action
     */
     
    public function mainAction() {
    
        $file_id = $this->GET['file_id'];
        $type = $this->GET['type'];
        $relation = $this->GET['relation'];
        $file = $this->initializeFile($relation);
        $file_data = $file_id ? $file->getFileDetail($file_id) : $_POST['file'];

        $this->tpl->assign('IMAGE_CONF', $file->conf);
        
        if ($file_data) {
            $this->tpl->assign('FILE', $file_data);

            switch ($type) {
            
                case'RTE':
                    $this->tpl->parse("content.RTE_select");
                break;
                case 'CSS':
                    $this->tpl->parse("content.CSS_select");
                break;
                case 'file':
                    //nothing
                break;
                default:
                    $this->tpl->parse("content.default");
                break;
            }
        }

        if ($_POST['save'] ?? false) {
            if ($file->updateFile($_POST['file'])) {
                msg("File {$file_data['title']} has been updated");
            } else {
                msg("Cannot update file {$file_data['title']}", 'error');
            }
        }

        if ($_POST['unlink'] ?? false) {
            if ($file->unlinkFile($_POST['file']['id'])) {
                msg("File {$file_data['title']} has been unlinked");
            } else {
                msg("Cannot unlink file {$file_data['title']}", 'error');
            }
        }

        // full details
        $_Onyx = new Onyx_Request("bo/component/file_info~file_path_encoded={$file_data['file_path_encoded']}~");
        $this->tpl->assign('FULL_DETAILS', $_Onyx->getContent());
        
        return true;
    }
    
}
