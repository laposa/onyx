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
        $relation = $_POST['relation'] ?? $this->GET['relation'] ?? 'node';
        $file = $this->initializeFile($relation);
        $file_data = $file_id ? $file->getFileDetail($file_id) : $_POST['file'];

        if($file_data) {
            $this->tpl->assign('FILE', $file_data);
        } else {
            msg('Could not load file details', 'error');
            return false;
        }

        $this->tpl->assign('IMAGE_CONF', $file->conf);

        if ($_POST['save'] ?? false) {
            if ($file->updateFile($_POST['file'])) {
                msg("File {$file_data['title']} has been updated");
            } else {
                msg("Cannot update file {$file_data['title']}", 'error');
            }
            return true;
        }

        if ($_POST['unlink'] ?? false) {
            if ($file->unlinkFile($_POST['file']['id'])) {
                msg("File {$file_data['title']} has been unlinked");
            } else {
                msg("Cannot unlink file {$file_data['title']}", 'error');
            }
            return true;
        }

        $this->tpl->assign('OPEN', str_replace([$file_data['info']['filename'], 'var/files/'], '', $file_data['src']));

        // full details
        $_Onyx = new Onyx_Request("bo/component/file_info~file_path_encoded={$file_data['file_path_encoded']}~");
        $this->tpl->assign('FULL_DETAILS', $_Onyx->getContent());
        
        return true;
    }
    
}
