<?php
/**
 * Copyright (c) 2010-2017 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */


class Onyx_Controller_Component_News_Categories extends Onyx_Controller {
    
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
        
        // blog_node_id can be provided via GET parameter, find by actual content with fallback to configuration option
        if (is_numeric($this->GET['blog_node_id'])) $blog_node_id = $this->GET['blog_node_id'];
        else if ($news_section_current = $this->Node->getCurrentNewsSectionId()) $blog_node_id = $news_section_current;
        else $blog_node_id = $this->Node->conf['id_map-blog'];
        
        if (is_numeric($this->GET['taxonomy_parent_id'])) $taxonomy_parent_id = $this->GET['taxonomy_parent_id'];
        else $taxonomy_parent_id = false;
        
        if (!is_numeric($this->GET['taxonomy_tree_id'])) $this->tpl->assign('ACTIVE_CLASS_ALL', 'active');
        
        $this->tpl->assign('BLOG_NODE_ID', $blog_node_id);
        
        /**
         * total count
         */
        
        $this->tpl->assign('COUNT_ALL', $this->Node->count("node_group = 'page' AND node_controller = 'news' AND parent = $blog_node_id AND publish = 1"));
        
        /**
         * process
         */
         
        if ($article_archive = $this->Node->getArticlesCategories($blog_node_id)) {
            
            foreach ($article_archive as $item) {
                
                if ($item['publish']) {
                
                    $this->assignAndParseItem($item, $taxonomy_parent_id);
                    
                }
                
            }
            
            $this->tpl->parse('content.list');
            
        }
        
        
        return true;
    }
    
    /**
     * assignAndParseItem
     */
     
    public function assignAndParseItem($item, $taxonomy_parent_id = false) {
        
        if (!is_array($item)) return false;
        
        if (!$item['title']) {
            $item['title'] = I18N_NEWS_CATEGORY_UNCATEGORIZED;
            $item['taxonomy_tree_id'] = 0;
        }
        
        //active css class
        if ($item['taxonomy_tree_id'] == $this->GET['taxonomy_tree_id']) $this->tpl->assign('ACTIVE_CLASS', 'active');
        else $this->tpl->assign('ACTIVE_CLASS', '');
        
        $this->tpl->assign('ITEM', $item);
        
        //parse only items within parent_id if provided
        if (is_numeric($taxonomy_parent_id)) {
            if ($taxonomy_parent_id == $item['parent_id']) $this->tpl->parse('content.list.item');
        } else {
            $this->tpl->parse('content.list.item');
        }
                
    }

}
