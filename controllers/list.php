<?php
/**
 * Copyright (c) 2017 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onyx_Controller_List extends Onyx_Controller {
    
    /**
     * get taxonomy
     */
     
    public function getTaxonomyList() {
        
        if (is_numeric($this->GET['taxonomy_tree_id'])) {
            $taxonomy_tree_id_list = $this->GET['taxonomy_tree_id'];
        } else if (is_numeric($this->GET['product_id'])) {
            $taxonomy_tree_id_list = $this->getTaxonomyListFromProduct($this->GET['product_id']);
        } else if (is_numeric($this->GET['node_id'])) {
            $taxonomy_tree_id_list = $this->getTaxonomyListFromNode($this->GET['node_id']);
        } else {
            $taxonomy_tree_id_list = '';
        }
        
        return $taxonomy_tree_id_list;
    }
    
    /**
     * get taxonomy from node
     */
     
    public function getTaxonomyListFromNode($node_id) {
        
        if (!is_numeric($node_id)) return false;
         
        $taxonomy_tree_id_list = $this->Node->getTaxonomyForNode($node_id);
        
        return $taxonomy_tree_id_list;
    }
    
    /**
     * get taxonomy from product
     * this is for showing related blog articles at product page
     */
     
    public function getTaxonomyListFromProduct($product_id) {
        
        if (!is_numeric($product_id)) return false;
        
        require_once('models/ecommerce/ecommerce_product.php');
        $Product = new ecommerce_product();
        
        $taxonomy_tree_id_list = $Product->getTaxonomyForProduct($product_id);
        
        return $taxonomy_tree_id_list;
    }
    
    /**
     * setImageOptions (IMAGE_PATH and IMAGE_RESIZE_OPTIONS)
     */
     
    public function setImageOptions() {
        
        /**
         * image size - for generating IMAGE_PATH
         */
         
        if (is_numeric($this->GET['image_width']) && $this->GET['image_width'] > 0) $image_width = $this->GET['image_width'];
        else $image_width = $GLOBALS['onyx_conf']['global']['stack_list_image_width'];
        
        if (is_numeric($this->GET['image_height']) && $this->GET['image_height'] > 0) $image_height = $this->GET['image_height'];
        else $image_height = $GLOBALS['onyx_conf']['global']['stack_list_image_height'];
        
        /**
         * set image path
         */
         
        if ($image_width == 0) $image_path = "/image/";
        else if ($image_height > 0) $image_path = "/thumbnail/{$image_width}x{$image_height}/";
        else $image_path = "/thumbnail/{$image_width}/";
        
        $this->tpl->assign('IMAGE_PATH', $image_path);
        
        /**
         * other resize options - generating IMAGE_RESIZE_OPTIONS
         */
        
        $image_resize_options = array();
        
        if ($this->GET['image_method']) $image_resize_options['method'] = $this->GET['image_method'];
        if ($this->GET['image_gravity']) $image_resize_options['gravity'] = $this->GET['image_gravity'];
        if ($this->GET['image_fill']) $image_resize_options['fill'] = $this->GET['image_fill'];
        else $image_resize_options['fill'] = 1;
        
        if (count($image_resize_options) > 0) $this->tpl->assign('IMAGE_RESIZE_OPTIONS', '?'.http_build_query($image_resize_options));
        
    }
}
