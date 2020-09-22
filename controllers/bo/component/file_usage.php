<?php
/**
 * File usage information
 * 
 * Copyright (c) 2008-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/bo/component/file.php');

class Onyx_Controller_Bo_Component_File_Usage extends Onyx_Controller_Bo_Component_File {

    /**
     * main action
     */
     
    public function mainAction() {
    
        require_once('models/common/common_file.php');
        $File = new common_file();
        
        /**
         * Setting base paths
         * 
         */
        
        $file_path_encoded_relative = $File->decode_file_path($this->GET['file_path_encoded_relative']);
        
        
        /**
         * Assign template variables
         * 
         */
        
        $this->tpl->assign('BASE', $base_folder);
        $this->tpl->assign('FOLDER_HEAD', str_replace('/', '/ ', $relative_folder_path));
        $this->tpl->assign('FOLDER', $relative_folder_path);
        $this->tpl->assign('MAX_FILE_SIZE', ini_get('upload_max_filesize'));
        
        
        /**
         * Get File List
         * 
         */
        
        
        $relations_list = $File->getRelations($file_path_encoded_relative);
        
        if ($relations_list['count'] == 0) {
            $this->tpl->parse('content.delete');
        } else {
            
            if (count($relations_list['file']) > 0) {
            
                $this->displayNodeInfo($relations_list['file']);
                
            }
            
            if (count($relations_list['node']) > 0) {
            
                $this->displayNodeInfo($relations_list['node']);
                
            }
            
            if (count($relations_list['product']) > 0) {
                foreach($relations_list['product'] as $image_detail) {
                    $this->tpl->assign('IMAGE_DETAIL', $image_detail);
                    $this->tpl->parse('content.usage.product');
                }
            }
            
            if (count($relations_list['product_variety']) > 0) {
                foreach($relations_list['product_variety'] as $image_detail) {
                    $this->tpl->assign('IMAGE_DETAIL', $image_detail);
                    $this->tpl->parse('content.usage.product_variety');
                }
            }
            
            if (count($relations_list['taxonomy']) > 0) {
                foreach($relations_list['taxonomy'] as $image_detail) {
                    $this->tpl->assign('IMAGE_DETAIL', $image_detail);
                    $this->tpl->parse('content.usage.taxonomy');
                }
            }
        
            if (count($relations_list['recipe']) > 0) {
                foreach($relations_list['recipe'] as $image_detail) {
                    $this->tpl->assign('IMAGE_DETAIL', $image_detail);
                    $this->tpl->parse('content.usage.recipe');
                }
            }
        
            if (count($relations_list['store']) > 0) {
                foreach($relations_list['store'] as $image_detail) {
                    $this->tpl->assign('IMAGE_DETAIL', $image_detail);
                    $this->tpl->parse('content.usage.store');
                }
            }
        
            if (count($relations_list['survey']) > 0) {
                foreach($relations_list['survey'] as $image_detail) {
                    $this->tpl->assign('IMAGE_DETAIL', $image_detail);
                    $this->tpl->parse('content.usage.survey');
                }
            }
        
            $this->tpl->parse('content.usage');
        }

        return true;
    }
    
    
    /**
     * display detail for node
     */
     
    function displayNodeInfo($relations_list) {
    
        require_once('models/common/common_node.php');
        $Node = new common_node();
    
        foreach($relations_list as $image_detail) {
    
            $node_detail = $Node->detail($image_detail['node_id']);
            if ($node_detail['node_group'] != 'page') {
                $image_detail['page_id'] = $Node->getParentPageId($image_detail['node_id']);
            } else {
                $image_detail['page_id'] = $image_detail['node_id'];
            }
            $this->tpl->assign('NODE_DETAIL', $node_detail);
            $this->tpl->assign('IMAGE_DETAIL', $image_detail);
            $this->tpl->parse('content.usage.node');
        }
                
    }
}
