<?php
/**
 * Blog controller
 *
 * Copyright (c) 2008-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onyx_Controller_Bo_Pages_News extends Onyx_Controller {
    
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
         * basic input data
         */
         
        if (is_numeric($this->GET['blog_node_id'])) $blog_node_id = $this->GET['blog_node_id'];
        else $blog_node_id = $this->Node->conf['id_map-blog'];

        /**
         * get detail of blog container node
         */
                
        $blog_section_detail = $this->Node->getDetail($blog_node_id);
        $this->tpl->assign('BLOG_SECTION', $blog_section_detail);

        return true;
    }
}

