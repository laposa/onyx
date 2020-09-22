<?php
/**
 * Copyright (c) 2006-2017 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/node/content/default.php');

class Onxshop_Controller_Node_Content_News_List extends Onxshop_Controller_Node_Content_Default {

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
        
        //blog_node_id can be provided via GET parameter, find by actual content with fallback to configuration option
        if (is_numeric($this->GET['blog_node_id'])) $blog_node_id = $this->GET['blog_node_id'];
        else if ($news_section_current = $this->Node->getCurrentNewsSectionId()) $blog_node_id = $news_section_current;
        else $blog_node_id = $this->Node->conf['id_map-blog'];
        
        /**
         * get node detail
         */
         
        $node_data = $this->Node->nodeDetail($node_id);
        
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
         * template
         */
        
        switch ($node_data['component']['template']) {
        
            case 'full';
                $template = 'news_list';
            break;
            case 'teaser';
                $template = 'news_list_teaser';
            break;
            case 'grid';
                $template = 'news_list_grid';
            break;
            case 'stack';
                $template = 'news_list_stack';
            break;
            case 'latest';
            default:
                $template = 'news_list_latest';
            break;
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
        
        $_Onxshop = new Onxshop_Request("component/$template~blog_node_id=$blog_node_id:id=$node_id:limit_from=$limit_from:limit_per_page=$limit_per_page:display_pagination=$display_pagination:publish=1:taxonomy_tree_id={$taxonomy_tree_id}:image_width={$image_o['width']}:image_height={$image_o['height']}:image_fill={$image_o['fill']}~");
        $this->tpl->assign('NEWS_LIST', $_Onxshop->getContent());
        
        $this->tpl->assign('NODE', $node_data);
        
        /**
         * display title
         */

        $this->displayTitle($node_data);

        return true;
    }
}
