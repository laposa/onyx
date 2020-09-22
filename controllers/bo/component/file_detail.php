<?php
/** 
 * Copyright (c) 2010-2017 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/component/file.php');

class Onxshop_Controller_Bo_Component_File_Detail extends Onxshop_Controller_Bo_Component_File {

    /**
     * main action
     */
     
    public function mainAction() {
    
        $file_id = $this->GET['file_id'];
        $type = $this->GET['type'];
        $relation = $this->GET['relation'];
        
        $File = $this->initializeFile($relation);

        $this->tpl->assign('IMAGE_CONF', $File->conf);
        
        if ($file_detail = $File->getFileDetail($file_id)) {
            
            $this->tpl->assign('FILE', $file_detail);

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

        // full details
        $_Onxshop = new Onxshop_Request("bo/component/file_info~file_path_encoded={$file_detail['file_path_encoded']}~");
        $this->tpl->assign('FULL_DETAILS', $_Onxshop->getContent());
        
        // properties edit
        $_Onxshop = new Onxshop_Request("bo/component/file_edit~file_id={$file_detail['id']}:type=$type:relation=$relation~");
        $this->tpl->assign('PROPERTIES_EDIT_FORM', $_Onxshop->getContent());

        return true;
    }
    
}
