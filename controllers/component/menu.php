<?php
/** 
 * Copyright (c) 2005-2017 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Component_Menu extends Onxshop_Controller {

    /**
     * main action
     */
     
    public function mainAction() {

        /**
         * input variables
         */
        
        // how deep we can go, zero means unlimited
        if (isset($this->GET['level'])) $max_display_level = $this->GET['level'];
        else $max_display_level = 0;
        
        // if to expand all items, when 1 show all (ie for sitemap), 0 expands only active items
        if ($this->GET['expand_all'] == 1) $expand_all = 1;
        else $expand_all = 0;
        
        // 1 parse strapline
        if ($this->GET['display_strapline'] == 1) $display_strapline = 1;
        else $display_strapline = 0;
        
        // 1 shows only published items, 0 shows all
        // possible security flaw, user can see list of not published items if provide the get parameter
        if (is_numeric($this->GET['publish'])) $publish = $this->GET['publish'];
        else $publish = 1;
        
        // open this item (active item)
        if (is_numeric($this->GET['open'])) $open = $this->GET['open'];
        else $open = null;
        
        // node_id
        if (is_numeric($this->GET['id'])) $node_id = $this->GET['id'];
        else $node_id = null; //null if not provided (it's correct value for tree's root elements)
        
        // filter (see common_node->prepareNodeGroupFilter() for available filters)
        if (isset($this->GET['filter'])) {
            
            $filter = $this->GET['filter'];
            
        } else {
            
            if (ONXSHOP_ECOMMERCE === true) $filter = 'page_exclude_products_recipes'; // don't show products in navigation on ecommerce sites as could have large product database
            else $filter = 'page';
            
        }
        
        /**
         * process action
         */
        
        return $this->standardAction($node_id, $publish, $max_display_level, $expand_all, $filter, $node_controller);
        
    }

    /**
     * standard action
     */
     
    public function standardAction($node_id = null, $publish = 1, $max_display_level = 0, $expand_all = 0, $filter = 'page') {

        require_once('models/common/common_node.php');
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
         * symbolic (a.k.a. alias or redirect) page type will user href as target page
         */
         
        if ($item['node_controller'] == 'symbolic' && $item['node_group'] == 'page') {
            
            $component_data = unserialize($item['component']);
            
            if (is_numeric($component_data['href'])) $href = "/page/{$component_data['href']}";
            else if (trim($component_data['href']) != '') $href = $component_data['href'];
            else $href = false;
            
        }

        /**
         * create href
         */
         
        if ($href) $item['href'] = $href;
        else $item['href'] = "/page/{$item['id']}";
        
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
     * Is given node active? I.e. is it or its parent selected/open?
     * Override if necessary
     */
    protected function isNodeActive(&$item)
    {
        if (is_numeric($this->GET['active_page'])) {
            if ($item['id'] == $this->GET['active_page']) return true;
        } else {
            return (in_array($item['id'], $_SESSION['active_pages']));
        }
    }

    /**
     * Is given node open? Override if necessary
     */
    protected function isNodeOpen(&$item)
    {
        if (is_numeric($this->GET['open']) && $item['id'] == $this->GET['open']) return true;
        return ($item['id'] == $_SESSION['active_pages'][0]);
    }
    
}
