<?php
/**
 * Copyright (c) 2006-2021 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/node/page/default.php');
require_once('models/common/common_node.php');
require_once('models/common/common_image.php');

class Onyx_Controller_Node_Page_Pdf_Brochure extends Onyx_Controller_Node_Page_Default {

    /**
     * main action
     */
    
    public function mainAction() {

        //input data
        if (is_numeric($this->GET['id'])) $node_id = $this->GET['id'];
        else return false;
        
        //initialise
        $Node = new common_node();
        $File = new common_image();
        
        //get node detail
        $node_data = $Node->nodeDetail($node_id);
        $manifest = $node_data['custom_fields']->pdf2Web;
        $file_list = $File->listFiles($node_data['id']);
        array_shift($file_list);

        //rebuild manifest based on assigned images if there are any
        if (count($file_list) > 0) {
            $manifest_array = json_decode($manifest, true);
            
            foreach($manifest_array['pages'] as $key => $page) {
                $new_file = str_replace('var/files/pdf2web/'.$node_id.'/', '', $file_list[$key]['src']);
                $manifest_array['pages'][$key]['filename'] = $new_file;
            }
        }

        $this->tpl->assign('PDF2WEB_MANIFEST', json_encode($manifest_array));
        
        //standard page actions
        $this->processContainers();
        $this->processPage();

        return true;
    }
}
