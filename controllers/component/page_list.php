<?php
/** 
 * Copyright (c) 2017-2019 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/list.php');
require_once('models/common/common_node.php');
require_once('controllers/node/page/default.php');

class Onyx_Controller_Component_Page_List extends Onyx_Controller_List {

    /**
     * main action
     */
     
    public function mainAction() {

        /**
         * input data
         */
        
        $node_ids = $this->GET['node_ids'];
        if (trim($this->GET['link_text']) == '') $link_text = I18N_LIST_VIEW_DETAIL;
        else $link_text = $this->GET['link_text'];
        
        /**
         * image_width
         * image_height
         * image_method
         * image_gravity
         * image_fill
         */
         
        $this->setImageOptions(); // set in template variables IMAGE_PATH and IMAGE_RESIZE_OPTIONS
        
        /**
         * initialise
         */
        
        $this->Node = new common_node();
        
        /**
         * parse items
         */

        $node_ids = explode(",", trim($node_ids));
        
        foreach ($node_ids as $node_id) {

            if (is_numeric($node_id)) {
                
                $item_node_data = $this->Node->nodeDetail($node_id);
                $item_node_data['image'] = $this->Node->getTeaserImageForNodeId($node_id);;
                $item_node_data['link_text'] = $link_text;
                
                /**
                 * related taxonomy
                 */

                $related_taxonomy = $this->Node->getRelatedTaxonomy($node_id);
                $item_node_data['taxonomy'] = $related_taxonomy;
                $item_node_data['taxonomy_class'] = Onyx_Controller_Node_Page_Default::createTaxonomyClass($related_taxonomy);

                $this->tpl->assign('ITEM', $item_node_data);
                if ($item_node_data['image']['src']) $this->tpl->parse("content.item.image");
                $this->tpl->parse('content.item');
                
            }

        }



        return true;
    }


}
