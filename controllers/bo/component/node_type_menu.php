<?php
/** 
 *
 * Copyright (c) 2009-2013 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/component/menu.php');

class Onxshop_Controller_Bo_Component_Node_Type_Menu extends Onxshop_Controller_Component_Menu {
    
    /**
     * main action
     */
    
    public function mainAction() {
    
        $list = $this->getList($publish);
        $list = $this->filterAndAssignInfo($list);
        $md_tree = $this->buildTree($list, $node_id);
        $this->generateSelectMenu($md_tree);
        
        return true;
    } 

    /**
     * generate SELECT menu
     */
    
    public function generateSelectMenu($md_tree) {
    
        /**
         * retrieve template_info
         */
        
        $templates_info = $this->retrieveTemplateInfo();
        
        
        /**
         * reorder
         */
         
        $md_tree = $this->reorder($md_tree);
        
        
        if (!is_array($md_tree)) return false;
        
        /**
         * iterate through each item
         */
         
        $this->iterateThroughGroups($md_tree);
        
        return true;
    }
    
    /**
     * iterate throught groups
     */
     
    public function iterateThroughGroups($list) {
    
        foreach ($list as $group) {
                    
            /**
             * display only what requested, but for content and layout both
             */
             
            if (
                $this->GET['expand_all'] == 1 || 
                $group['name'] == $this->GET['node_group'] ||
                ($group['name'] == 'layout' && $this->GET['node_group'] == 'content')
            ) {             
                $this->tpl->assign('GROUP', $group);
                
                if (is_array($group['children']) && count($group['children']) > 0) {
                
                    $this->iterateThroughItems($group['children']);
                }
                
                $this->tpl->parse("content.group");
            }
            
        }

    }
    
    /**
     * iterate throught items
     */
     
    public function iterateThroughItems($list) {
        
        foreach ($list as $item) {
            
            if ($item['selected']) $item['selected'] = "selected='selected'";
            else $item['selected'] = '';
            
            $this->tpl->assign('ITEM', $item);
            $this->tpl->parse("content.group.item");
        }
    }
    
    
    /**
     * get md array for node directory
     */
    
    function getList($publish = 1) {
    
        require_once('models/common/common_file.php');
        $File = new common_file();
        
        //getting list of templates, joing project and onxshop node dir
        $list = $File->getFlatArrayFromFsJoin("templates/node/");
        
        //remove .html, .php
        foreach ($list as $k=>$item) {
            $list[$k]['name'] = preg_replace('/\.html$/', '', $list[$k]['name']);
            $list[$k]['name'] = preg_replace('/\.php$/', '', $list[$k]['name']);
            $list[$k]['id'] = preg_replace('/\.html$/', '', $list[$k]['id']);
            $list[$k]['id'] = preg_replace('/\.php$/', '', $list[$k]['id']);
            $list[$k]['parent'] = preg_replace('/\.html$/', '', $list[$k]['parent']);
            $list[$k]['parent'] = preg_replace('/\.php$/', '', $list[$k]['parent']);
        }
                
        return $list;
    }
    
    
    /**
     * reorder file list
     */
     
    public function reorder($md_tree) {
        
        //make sure array is sorted
        //print_r($md_tree);
        array_multisort($md_tree);
        //print_r($md_tree);
        
        //reorder
        $temp = array();
        $temp[0] = $this->findInMdTree($md_tree, 'content');//content
        $temp[1] = $this->findInMdTree($md_tree, 'layout');//layout
        $temp[2] = $this->findInMdTree($md_tree, 'page');//page
        $temp[3] = $this->findInMdTree($md_tree, 'container');//container
        $temp[4] = $this->findInMdTree($md_tree, 'site');//site
        $temp[4] = $this->findInMdTree($md_tree, 'variable');
        
        $md_tree = $temp;
        
        return $md_tree;
    }
    
    /**
     * findInMdTree
     */
     
    public function findInMdTree($md_tree, $query) {
        
        foreach ($md_tree as $item) {
        
            if ($item['id'] == $query) return $item;
        
        }
        
    }
    
    /**
     * filter to show only allowed items
     */
     
    public function filterAndAssignInfo($list) {
        
        /**
         * retrieve template info
         */
         
        $templates_info = $this->retrieveTemplateInfo();
        
        /**
         * set selected item
         */
        
        if (!$this->GET['open']) $selected = $templates_info[$this->GET['node_group']]['default_template'];
        else $selected = $this->GET['open'];
        
        /**
         * create filtered array
         */
         
        $filtered_list = array();
        
        foreach ($list as $item) {
            
            if (array_key_exists($item['parent'], $templates_info)) {
                
                //dont' show items with visibility false
                $item_visibility_status = $templates_info[$item['parent']][$item['name']]['visibility'];
                
                //but show item currently selected
                if ($item['parent'] == $this->GET['node_group'] && $selected == $item['name']) $item_visibility_status = true;
                
                if (is_bool($item_visibility_status) && $item_visibility_status === false) {
                    
                    //don't show
                    
                } else {
                    
                    //use template info title if available
                    $templates_info_item_title = trim($templates_info[$item['parent']][$item['name']]['title']);
                    if ($templates_info_item_title !== '') $item['title'] = $templates_info_item_title;
                    else $item['title'] = $item['name'];
                
                    $filtered_list[] = $item;
                }
            } else {
                $filtered_list[] = $item;
            }
        }
        
        /**
         * mark selected item
         */
        
        foreach ($filtered_list as $k=>$item) {
            if ($item['name'] == $selected && $this->GET['node_group'] == $item['parent']) $filtered_list[$k]['selected'] = true;
            else $filtered_list[$k]['selected'] = false;
        }
        
        
        return $filtered_list;
    }
    
    /**
     * retrieve template_info
     */
    
    public function retrieveTemplateInfo() {
    
        //include always general
        require_once(ONXSHOP_DIR . "conf/node_type.php");
        $templates_info_onxshop = $templates_info;
        
        //local overwrites/extensions
        if (file_exists(ONXSHOP_PROJECT_DIR . "conf/node_type.php")) {
            $templates_info = false;
            require_once(ONXSHOP_PROJECT_DIR . "conf/node_type.php");
        }
        
        //merge
        if (is_array($templates_info)) {
        
            $templates_info = $this->array_merge_recursive_distinct($templates_info_onxshop, $templates_info);
            
        } else {
        
            $templates_info = $templates_info_onxshop;
        
        }
        
        return $templates_info;
    }
    
    /**
     * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
     * keys to arrays rather than overwriting the value in the first array with the duplicate
     * value in the second array, as array_merge does. I.e., with array_merge_recursive,
     * this happens (documented behavior):
     *
     * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => array('org value', 'new value'));
     *
     * array_merge_recursive_distinct does not change the datatypes of the values in the arrays.
     * Matching keys' values in the second array overwrite those in the first array, as is the
     * case with array_merge, i.e.:
     *
     * array_merge_recursive_distinct(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => array('new value'));
     *
     * Parameters are passed by reference, though only for performance reasons. They're not
     * altered by this function.
     *
     * @param array $array1
     * @param array $array2
     * @return array
     * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
     * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
     */
    public function array_merge_recursive_distinct ( array &$array1, array &$array2 )
    {
      $merged = $array1;
    
      foreach ( $array2 as $key => &$value )
      {
        if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) )
        {
          $merged [$key] = $this->array_merge_recursive_distinct ( $merged [$key], $value );
        }
        else
        {
          $merged [$key] = $value;
        }
      }
    
      return $merged;
    }   
}
