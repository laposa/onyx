<?php
/** 
 * Copyright (c) 2009-2024 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onyx_Controller_Node_Default extends Onyx_Controller {

    var $Node;
    var $node_data;

    /**
     * main action
     */
     
    public function mainAction() {
    
        $this->processLayout();

        return true;
    }
    
    /**
     * process containers
     */
    
    public function processContainers($disable_fe_edit = false) {
    
        /**
         * check node id value
         */
         
        if (!is_numeric($this->GET['id'])) {
            msg("node/default: id not numeric", 'error');
            return false;
        } else {
            $node_id = $this->GET['id'];
        }
        
        /**
         * initialize
         */
         
        require_once('models/common/common_node.php');
        $this->Node = new common_node();
        $node_data = $this->Node->nodeDetail($node_id);
        
        //find child nodes
        $contentx = $this->Node->parseChildren($node_id, false, $disable_fe_edit);
        
        //assign to this controller as CONTAINER variable
        if (is_array($contentx)) {
            foreach ($contentx as $content) {
                $container[$content['container']] .= $content['content'];
            }
        }
        
        /**
         * node add icons
         * front-end node edit and node move (sort) icons are inserted in controller/node
         *  
         */
         
        if ($_SESSION['fe_edit_mode'] == 'edit' || $_SESSION['fe_edit_mode'] == 'move') {
            if ($disable_fe_edit == false) {
                //normally we support container.0 to container.6 in default templates, but why not to have some reserve, e.g. 20
                $min_container_id = 0;
                $max_container_id = 20;
                for ($key = $min_container_id; $key < ($max_container_id + 1); $key++) {
                    $container[$key] = "<div class='onyx-layout-container' id='onyx-layout-container-{$node_id}-{$key}'>{$container[$key]}</div>";    
                }
            } 
        }
            
        $this->tpl->assign("CONTAINER", $container);
        $this->tpl->assign("NODE", $node_data); 
    }
    
    /**
     * process layout
     */
     
    public function processLayout() {
    
    }
    
    /**
     * getImageSizeOptions
     * 
     */
     
    function getImageSizeOptions($node_data) {
        
            /**
             * image size: set width
             */
             
            if (is_numeric($node_data['component']['image_width'])) {
                $image_width = $node_data['component']['image_width'];
            } else {
                $node_data['component']['image_width'] = 0;
                $image_width = 0;
            }
            
            /**
             * image size: check constrain and set appropriate height
             */
            
            switch ($node_data['component']['image_constrain']) {
                
                case '1-1':
                    $image_height = $image_width;
                break;
                
                default:
                case '2_39-1': // 2.39:1
                    $image_height = (int)($image_width / 2.39 * 1);
                break;
                
                case '16-9':
                    $image_height = (int)($image_width / 16 * 9);
                break;
                
                case '4-3':
                    $image_height = (int)($image_width / 4 * 3);
                break;
                
                case '0':
                    $image_height = 0;
                break;
                
            }
            
            /**
             * image size: fill cropping option
             * 
             */
             
            if (is_numeric($node_data['component']['image_fill'])) $image_fill = $node_data['component']['image_fill'];
            else $image_fill = 1;

            /**
             * return array
             */
             
            return array('width'=>$image_width, 'height'=>$image_height, 'fill'=>$image_fill);
    }
}
