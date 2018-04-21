<?php
/** 
 * Copyright (c) 2008-2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/component/image_gallery.php');

class Onxshop_Controller_Component_Image_Gallery_Cycle extends Onxshop_Controller_Component_Image_Gallery {

    /**
     * main action
     */
     
    public function mainAction() {
    
        /* we need to include config, can be removed when we initialize all conf on beginning */
        require_once('models/common/common_image.php');
        $common_image_conf = common_image::initConfiguration();

        /**
         * Set input variables
         */
         
        $relation = preg_replace('/[^a-zA-Z_-]/', '', $this->GET['relation']);
        $role = preg_replace('/[^a-zA-Z_-]/', '', $this->GET['role']);
        
        if (is_numeric($this->GET['node_id'])) $node_id = $this->GET['node_id'];
        else $node_id = 0;
        
        if ($this->GET['limit']) $limit = $this->GET['limit'];
        else $limit = "";
        
        /**
         * cycle conf
         */
         
        $cycle = array();

        if ($this->GET['cycle_fx']) $cycle['fx'] = $this->GET['cycle_fx'];
        else $cycle['fx'] = 'fade';
        
        //"'disable'" value must be interpreted as "null"
        if ($this->GET['cycle_easing'] && $this->GET['cycle_easing'] != 'disable') $cycle['easing'] = "'{$this->GET['cycle_easing']}'";
        else $cycle['easing'] = 'null';
        
        if (is_numeric($this->GET['cycle_timeout'])) $cycle['timeout'] = $this->GET['cycle_timeout'];
        else $cycle['timeout'] = 0;
        
        if (is_numeric($this->GET['cycle_speed'])) $cycle['speed'] = $this->GET['cycle_speed'];
        else $cycle['speed'] = 2000;
        
        //cycle_link_to_node_id
        $cycle['link_to_node_id'] = array();
        if ($this->GET['cycle_link_to_node_id'] != '') {
            $cycle_link_to_node_id_items = explode(',', $this->GET['cycle_link_to_node_id']);
            foreach ($cycle_link_to_node_id_items as $cycle_link_to_node_id_item) {
                if (is_numeric($cycle_link_to_node_id_item)) $cycle['link_to_node_id'][] = $cycle_link_to_node_id_item;
            }
        }
        
        /**
         * image path
         */
         
        $img_path = $this->getImagePath();
        
        
        /**
         * Find what object we need
         */
        
        $Image = $this->createImageObject($this->GET['relation']);

        
        if (is_numeric($this->GET['width'])) $width = $this->GET['width'];
        else $width = 100;
        
        if (is_numeric($this->GET['height'])) $height = $this->GET['height'];
        else $height = false;
        
        /**
         * initialize variables for extreme dimensions
         */
         
        $dimension_extreme['width_max'] = 0;
        $dimension_extreme['width_min'] = 0;
        $dimension_extreme['height_max'] = 0;
        $dimension_extreme['height_min'] = 0;
        $dimension_extreme['proportion_max'] = 0;
        $dimension_extreme['proportion_min'] = 10;

        /**
         * Prepare query
         */
         
        $role = $this->GET['role'] ? "role='{$this->GET['role']}' AND ":'';
        $image_get_query = "{$role} node_id={$node_id}";
                
        /**
         * Get the list
         */
        
        $images = $Image->listing($image_get_query, "priority DESC, id ASC", $limit);
        
        /**
         * Loop through the list
         */
         
        if (is_array($images)) {
        
            foreach ($images as $i=>$img) {
        
                $img['path'] = $img_path;
                $img['first_id'] = $images[0]['id'];
                
                $size = $Image->getImageSize(ONXSHOP_PROJECT_DIR . $img['src']);
                
                if ($size) {
                    if ($size['width'] > $dimension_extreme['width_max']) $dimension_extreme['width_max'] = $size['width'];
                    if ($size['width'] < $dimension_extreme['width_min']) $dimension_extreme['width_min'] = $size['width'];
                    if ($size['height'] > $dimension_extreme['height_max']) $dimension_extreme['height_max'] = $size['height'];
                    if ($size['height'] < $dimension_extreme['height_min']) $dimension_extreme['height_min'] = $size['height'];
                    if ($size['proportion'] > $dimension_extreme['proportion_max']) $dimension_extreme['proportion_max'] = $size['proportion'];
                    if ($size['proportion'] < $dimension_extreme['proportion_min']) $dimension_extreme['proportion_min'] = $size['proportion'];
                    // deprecated, keep for backward compatibility
                    if (is_numeric($cycle['link_to_node_id'][$i])) $img['link_to_node_id'] = $cycle['link_to_node_id'][$i];
                    
                    $this->assignAndParseItem($img);
                }
                
            }
            
            $images_count = count($images);
        }
        
        /**
         * Calculate relative height
         */
        
        if ($width > 0) {
            $dimension_max['width'] = $width;
            $dimension_max['height'] = $width / $dimension_extreme['proportion_min'];
        } else {
            $dimension_max['width'] = $dimension_extreme['width_max'];
            $dimension_max['height'] = $dimension_extreme['height_max'];
        }
        
        /**
         * overwrite when fixed height requested
         */
         
        if ($height) {
            $dimension_max['height'] = $height; 
        }
        
        /**
         * round
         */
         
        $dimension_max['width'] = round($dimension_max['width']);
        $dimension_max['height'] = round($dimension_max['height']);
        
        $this->tpl->assign("DIMENSION_MAX", $dimension_max);
        $this->tpl->assign("CYCLE", $cycle);
        
        /**
         * show control and placeholder only if multiple images
         */
        
        if ($images_count > 1) {
            $this->tpl->parse('content.control');
            $this->tpl->parse('content.placeholder');
        }

        return true;
    }
    
    /**
     * assignAndParseItem
     */
    
    public function assignAndParseItem($item) {
        
        $this->tpl->assign('ITEM', $item);
                    
        if (is_numeric($item['link_to_node_id']) && $item['link_to_node_id'] > 0) $this->tpl->parse('content.item.link');
        else $this->tpl->parse('content.item.normal');
                    
        $this->tpl->parse('content.item');
            
    }

}
