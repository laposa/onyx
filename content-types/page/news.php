<?php
/**
 * Copyright (c) 2006-2021 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/node/page/default.php');

class Onyx_Controller_Node_Page_News extends Onyx_Controller_Node_Page_Default {

    /**
     * main action
     */
     
    public function mainAction() {
        
        //input data
        if (is_numeric($this->GET['id'])) $node_id = $this->GET['id'];
        else return false;
        
        //initialise
        require_once('models/common/common_node.php');
        $Node = new common_node();
        
        //get node detail
        $node_data = $Node->nodeDetail($node_id);
        
        //parent page is blog article container
        $blog_node_id = $node_data['parent'];
        $this->tpl->assign('BLOG_NODE_ID', $blog_node_id);
        
        /**
         * empty author helper class
         */
         
        if (trim($node_data['component']['author']) == '') {
            
            $this->tpl->assign('AUTHOR_EMPTY', 'author_empty');
            
        } else {
            
            $this->tpl->assign('AUTHOR_EMPTY', '');
            
        }
        
        /**
         * getRelatedTaxonomy
         */
         
        $related_taxonomy = $Node->getRelatedTaxonomy($node_id);
        $this->tpl->assign('TAXONOMY', $related_taxonomy);
        
        //standard page actions
        $this->processContainers();
        $this->processPage();

        return true;
    }
}
