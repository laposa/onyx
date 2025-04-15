<?php
/**
 * Copyright (c) 2006-2017 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/node/content/default.php');

class Onyx_Controller_Node_Content_News_List extends Onyx_Controller_Node_Content_Default {

    /**
     * main action
     */
     
    public function mainAction() {
        
        /**
         * initialise
         */
         
        require_once('models/common/common_node.php');
        $this->Node = new common_node();
        
        /**
         * input data
         */
        
        $node_id = $this->GET['id'];
        $node_data = $this->Node->nodeDetail($node_id);
        
        //blog_node_id can be provided via GET parameter, find by actual content with fallback to configuration option
        if (is_numeric($this->GET['blog_node_id'])) $blog_node_id = $this->GET['blog_node_id'];
        else if (is_numeric($node_data['parent'])) $blog_node_id = $node_data['parent'];
        else $blog_node_id = $this->Node->conf['id_map-blog'];

        /**
         * get node detail
         */
         
        
        /**
         * filtering
         * TODO: support for multiple taxonomy
         *
         */
         
        if (is_numeric($this->GET['taxonomy_tree_id'])) {
            $taxonomy_tree_id = $this->GET['taxonomy_tree_id'];
        } else if ($taxonomy = $this->Node->getTaxonomyForNode($node_data['id'])) {
            $taxonomy_tree_id = $taxonomy[0];
        } else {
            $taxonomy_tree_id = '';
        }
        
        if (is_numeric($this->GET['limit_from']) && is_numeric($this->GET['limit_per_page'])) {
            $limit_from = $this->GET['limit_from'];
            $limit_per_page = $this->GET['limit_per_page'];
        } else if (is_numeric($node_data['component']['limit'])) {
            $limit_from = 0;
            $limit_per_page = $node_data['component']['limit'];
        } else {
            $limit_from = '';
            $limit_per_page = '';
        }
        
        /**
         * pagination
         */
         
        if ($node_data['component']['pagination'] == 1) {
            $display_pagination = 1;
        } else {
            $display_pagination = 0;
        }
        
        /**
         * image size
         */
        
        $image_o = $this->getImageSizeOptions($node_data);
        
        /**
         * call controller
         */
        
        $_Onyx = new Onyx_Request("component/news_list~blog_node_id=$blog_node_id:id=$node_id:limit_from=$limit_from:limit_per_page=$limit_per_page:display_pagination=$display_pagination:publish=1:taxonomy_tree_id={$taxonomy_tree_id}:image_width={$image_o['width']}:image_height={$image_o['height']}:image_fill={$image_o['fill']}~");
        $this->tpl->assign('NEWS_LIST', $_Onyx->getContent());
        
        $this->tpl->assign('NODE', $node_data);
        
        /**
         * display title
         */

        $this->displayTitle($node_data);

        return true;
    }
}
