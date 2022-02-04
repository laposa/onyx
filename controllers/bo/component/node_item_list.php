<?php
/**
 * Copyright (c) 2022 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/component/news_list.php');

class Onyx_Controller_Bo_Component_Node_Item_List extends Onyx_Controller_Component_News_List {

    /**
     * main action
     */
     
    public function mainAction() {
    
        /**
         * input parameters
         */

        if (is_numeric($this->GET['parent'])) $parent = $this->GET['parent'];
        else {
            msg('(numeric) parent parameter is requred');
            return false;
        }
        if (strlen($this->GET['node_group']) > 0) $node_group = $this->GET['node_group'];
        else {
            msg('(string) node_group parameter is requred');
            return false;
        }

        if (strlen($this->GET['node_controller']) > 0) $node_controller = $this->GET['node_controller'];
        else {
            msg('(string) node_controller parameter is requred');
            return false;
        }

        /**
         * initialise
         */
         
        require_once('models/common/common_node.php');
        $this->Node = new common_node();
        
        /**
         * get list of current items
         */

        $list =  $this->getItemList($node_group, $node_controller, $parent);

        /**
         * display items
         */

         foreach ($list as $item) {
            if ($item['publish'] == 0)  $item['class'] = 'disabled';
            $this->tpl->assign('ITEM', $item);
            $this->tpl->parse('content.list.item');
         }

         $this->tpl->parse('content.list');
    }

    /**
     * getItemList
     */
     
    public function getItemList($node_group, $node_controller, $parent) {
        
        /**
         * prepare filter
         */
         
        $filter = [
            'node_group' => $node_group,
            'node_controller' => $node_controller,
            'parent' => $parent
        ];

        $sorting = 'priority DESC, id ASC';

        /**
         * get list
         */

        $list = $this->Node->getNodeList($filter, $sorting);
        
        
        return $list;
        
    }
    
}
        
