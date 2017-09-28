<?php
/**
 * Copyright (c) 2010-2017 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 * this is really node_tree as it depends on common_node
 *
 */

require_once('models/common/common_node.php');

abstract class Onxshop_Controller_Tree extends Onxshop_Controller {

    /**
     * main action
     */
    
    public function mainAction() {
        
        return $this->standardAction();
        
    }

    /**
     * standard action
     */
     
    public function standardAction($node_id = null, $publish = 1, $max_display_level = 0, $expand_all = 0, $filter = 'page') {

        $this->Node = new common_node();
        
        $tree = $this->getTree($publish, $filter, $node_id, $max_display_level - 1, $expand_all);

        $end_result = $this->parseTree($tree);
        $this->tpl->assign('END_RESULT_GROUPS', $end_result);

        return true;
    }

    /**
     * build tree from a 2D array
     */
     
    function buildTree($nodes, $id = 0, $level = 1) {

        //this function is called again and again
        
        $tree = array();

        if(is_array($nodes)) {
        
            foreach($nodes as $node) {
            
                // safety check
                if ($node['parent'] === $node['id']) {
                        msg("Infinite loop in tree.buildTree (id={$node['id']}, parent={$node['parent']}", 'error');
                        return false;
                }
                
                if ($node["parent"] == $id) array_push($tree, $node);
            }

            for($x = 0; $x < count($tree); $x++) {
                    
                $tree[$x]["level"] = $level;
                $tree[$x]["children"] = $this->buildTree($nodes, $tree[$x]["id"], $level + 1);

            }

            return $tree;

        }

    }

    /**
     * get tree in descending order (from root down to given level)
     */
     
    public function getTree($publish = 1, $filter, $parent, $depth, $expand_all)
    {
        /**
         * try optimised
         */
         
        if (!$parent && $depth == -1 && $expand_all) {
            // we can hugely optimise this special case 
            $flat_tree = $this->Node->getTree($publish, $filter);
            $flat_tree = $this->processPermission($flat_tree);
            return $this->buildTree($flat_tree);;
        }

        /**
         * heavy, but reliable
         */
         
        $tree = $this->Node->getNodesByParent($publish, $filter, $parent);

        if (is_array($tree) && count($tree) > 0 && $depth != 0) {

            $tree = $this->processPermission($tree);
            $depth--;

            foreach ($tree as $i => $node) {

                $children = $this->getTree($publish, $filter, $node['id'], $depth, $expand_all);

                if ($expand_all || $this->isNodeOpen($node) || $this->isNodeActive($node)) {
                    if (is_array($children) && count($children) > 0) $tree[$i]['children'] = $children;
                } else {
                    $tree[$i]['has_children'] = count($children) > 0;
                }
            }
        }

        return $tree;
    }

    /**
     * parseTree
     */
     
    public function parseTree(&$tree) {

        $count = count($tree);

        if ($count == 0) return '';

        foreach ($tree as $i => $item) {

            $item['css_class'] = '';

            /**
             * css classes (first/last/middle)
             */
            if ($i == 0) $item['css_class'] = 'first';
            else if ($i == ($count - 1)) $item['css_class'] = 'last';
            else $item['css_class'] = 'middle';

            /**
             * parse children
             */
            if (is_array($item['children']) && count($item['children']) > 0) {

                $item['subcontent'] = $this->parseTree($item['children']);

            } else {
            
                $item['subcontent'] = '';
            
            }

            if (!empty($item['subcontent']) || $item['has_children']) $item['css_class'] = $item['css_class'] . ' has-child';

            if ($item_parsed = $this->parseItem($item)) {
                
                $end_result_items .= $item_parsed;
                
            }
            
        }

        $this->tpl->assign('END_RESULT_ITEMS', $end_result_items);
        
        $group_parsed = $this->parseGroup();
        
        return $group_parsed;

    }
    
    /**
     * Parse single item
     *
     * @param array $item
     * @return text html
     */
     
    function parseItem($item) {
    
        /**
         * use description for HTML title attribute if available
         * or set HTML title as item name if not available
         */
         
        if ($item['description'] != '') $item['title'] = $item['description'];
        else if ($item['title'] == '') $item['title'] = $item['name'];
        
        /**
         * set open and active class
         */

        if ($this->isNodeActive($item)) {
            $item['css_class'] .= " open";
            if ($this->isNodeOpen($item)) $item['css_class'] .= " active";
        }

        /**
         * add publish, no_publish class if we showing all items
         */
        
        if (isset($this->GET['publish'])) {
            if ($item['publish'] == 0) $item['css_class'] .= " onxshop-nopublish";
        }
        

        /**
         * assign to template
         */
         
        $item['children'] = null; //dont assign children to save memory
        $this->tpl->assign('ITEM',$item);
        
        /**
         * other specific things, should be moved to separate controllers
         */
         
        if ($this->GET['display_strapline'] && trim($item['strapline']) != '') {
            $this->tpl->parse('content.group.item.link.strapline');
        }
        
        /**
         * parse no link block if appropriate
         */
        
        if ($item['display_in_menu'] == 2 || $item['node_group'] == 'container') {
            $this->tpl->parse('content.group.item.nolink');
        } else {
            $this->tpl->parse('content.group.item.link');
        }
        
        /**
         * parse item, get as text and reset template block
         */
         
        $this->tpl->parse('content.group.item');
        $text = $this->tpl->text('content.group.item');
        $this->tpl->reset('content.group.item');
        
        return $text;
    }
    
    /**
     * parse group, get as text and reset template block
     *
     * @return text html
     */
     
    function parseGroup() { 
        
        $this->tpl->parse('content.group');
        $text = $this->tpl->text('content.group'); 
        $this->tpl->reset('content.group');
        
        return $text;
    }
    
    /**
     * process persmission
     */
     
    function processPermission(&$tree) {
    
        $filtered_tree = array();
        
        foreach ($tree as $item) {
        
            /**
             * display_permission
             */
            
            if (is_numeric($item['display_permission'])) {
                
                if (common_node::checkDisplayPermission($item)) $filtered_tree[] = $item;
                
            } else {
            
                //it's not a node with display_permission (could be a file)
                $filtered_tree[] = $item;
                
            }
        
        }
        
        return $filtered_tree;
    }

    /**
     * Is given node active? I.e. is it or its parent active?
     * Override in subclass
     */
    protected function isNodeActive(&$item)
    {
    }

    /**
     * Is given node open? Override if necessary
     * Override in subclass
     */
    protected function isNodeOpen(&$item)
    {
    }

}


