<?php
/**
 * Copyright (c) 2006-2015 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/node/content/default.php');
require_once('models/ecommerce/ecommerce_product_image.php');

class Onxshop_Controller_Bo_Node_Content_Product_highlights extends Onxshop_Controller_Bo_Node_Content_Default {

    /**
     * pre action
     */

    function pre() {

        parent::pre();
        
        if ($_POST['node']['component']['display_sorting'] == 'on') $_POST['node']['component']['display_sorting'] = 1;
        else $_POST['node']['component']['display_sorting'] = 0;
        
        if ($_POST['node']['component']['display_pagination'] == 'on') $_POST['node']['component']['display_pagination'] = 1;
        else $_POST['node']['component']['display_pagination'] = 0;

        $this->parseProductSelect();
    }
    
    /**
     * post action
     */
     
    function post() {
    
        parent::post();
        
        //template
        $this->tpl->assign("SELECTED_template_{$this->node_data['component']['template']}", "selected='selected'");
        //image role
        $this->tpl->assign("SELECTED_image_role_{$this->node_data['component']['image_role']}", "selected='selected'");
        //sorting
        $this->node_data['component']['display_sorting'] = ($this->node_data['component']['display_sorting']) ? 'checked="checked"' : '';
        //pagination
        $this->node_data['component']['display_pagination'] = ($this->node_data['component']['display_pagination']) ? 'checked="checked"' : '';
        
        $Image = new ecommerce_product_image();
        //find product in the node
        $current = $this->node_data['component']['related'];
        if (is_array($current)) {
            foreach ($current as $product_id) {
                //find product in the node
                if (is_numeric($product_id)) {
                    $detail = $this->Node->listing("node_group = 'page' AND node_controller ~ 'product' AND content = '$product_id'");
                    $current = $detail[0];
                    if ($current['publish'] == 0) $current['class'] = 'notpublic';
                    $image = $Image->listing("node_id = $product_id", "priority DESC, id ASC");
                    if (count($image) > 0) $current['image_src'] = $image[0]['src'];
                    else $current['image_src'] = '/var/files/placeholder.png';
                    $this->tpl->assign('CURRENT', $current);
                    $this->tpl->parse('content.item');
                }
            }
        }
    }

    /**
     * parseProductSelect
     */
     
    function parseProductSelect() {

        require_once('models/ecommerce/ecommerce_product.php');
        $Product = new ecommerce_product();
        $Image = new ecommerce_product_image();

        $listing = $Product->listing('', 'name ASC');

        foreach ($listing as $item) {
            if ($item['publish'] == 0) $item['class'] = 'notpublic';
            $detail = $this->Node->listing("node_group = 'page' AND node_controller ~ 'product' AND content = '{$item['id']}'");            
            if (count($detail) == 0) $item['disabled'] = 'disabled';
            $image = $Image->listing("node_id = {$item['id']}", "priority DESC, id ASC");
            if (count($image) > 0) $item['image_src'] = $image[0]['src'];
            else $item['image_src'] = '/var/files/placeholder.png';
            $this->tpl->assign("PRODUCT", $item);
            $this->tpl->parse("content.product_select_item");
        }
    }
    
    /**
     * getDefaultImageWidth
     */
    
    public function getDefaultImageWidth() {
        
        return $GLOBALS['onxshop_conf']['global']['product_list_image_width'];
        
    }
}
