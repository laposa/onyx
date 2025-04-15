<?php
/**
 * Copyright (c) 2014-2017 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('models/common/common_node.php');

class Onyx_Controller_Bo_Component_News_Section_Select extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {

        /**
         * input - selected blog
         */
         
        $blog_node_id = $this->GET['blog_node_id'];

        /**
         * create objects
         */
        
        $Node = new common_node();
        
        /**
         *  list all id_map-blog* configuration options
         */
        
        $list = $Node->getListOfNewsSectionIds();

        /**
         * parse dropdown
         */
         
        if (count($list) > 0) {

            foreach ($list as $id) {
                $node = $Node->getDetail($id);
                $this->tpl->assign('NODE', $node);
                $this->tpl->assign('SELECTED', $id == $blog_node_id ? 'selected="selected"' : '');
                $this->tpl->parse('content.select.item');
            }

            $this->tpl->parse('content.select');

        }

        return true;
    }

}
        
