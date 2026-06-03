<?php
/** 
 *
 * Copyright (c) 2009-2026 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/component/menu.php');

class Onyx_Controller_Bo_Component_Node_Type_Menu extends Onyx_Controller_Component_Menu {
    
    public function mainAction() {
        $list = $this->getList();
        $list = $this->filterAndAssignInfo($list);
        $md_tree = $this->buildTree($list, null);
        $this->generateSelectMenu($md_tree);
        
        return true;
    } 

    public function generateSelectMenu($md_tree) {
        $md_tree = $this->reorder($md_tree);
        
        if (!is_array($md_tree)) return false;
        $this->iterateThroughGroups($md_tree);
        
        return true;
    }
    
    public function iterateThroughGroups($list) {
        foreach ($list as $group) {
            // display only what requested, but for content and layout both
            if (
                $this->GET['expand_all'] == 1 || 
                $group['name'] == $this->GET['node_group'] ||
                ($group['name'] == 'layout' && $this->GET['node_group'] == 'content')
            ) {             
                $this->tpl->assign('GROUP', $group);
                
                if (isset($group['children']) && is_array($group['children']) && count($group['children']) > 0) {
                
                    $this->iterateThroughItems($group['children']);
                }
                
                $this->tpl->parse("content.group");
            }
            
        }

    }
    
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
    function getList() {
        require_once('models/common/common_file.php');
        $File = new common_file();
        
        // getting list of templates, joing project and onyx node dir
        $list = $File->getFlatArrayFromFsJoin("templates/node/", true);
        
        // remove .html, .php
        foreach ($list as $k=>$item) {
            $list[$k]['name'] = preg_replace('/\.html$/', '', $list[$k]['name']);
            $list[$k]['name'] = preg_replace('/\.php$/', '', $list[$k]['name']);
            $list[$k]['id'] = preg_replace('/\.html$/', '', $list[$k]['id']);
            $list[$k]['id'] = preg_replace('/\.php$/', '', $list[$k]['id']);

            if ($list[$k]['parent']  !== null) {
                $list[$k]['parent'] = preg_replace('/\.html$/', '', $list[$k]['parent']);
                $list[$k]['parent'] = preg_replace('/\.php$/', '', $list[$k]['parent']);
            }
        }

        return $list;
    }
    
    
    /**
     * reorder file list
     */
    public function reorder($md_tree) {
        array_multisort($md_tree);

        // reorder
        $temp = array();

        if(isset($this->GET['only_group']) && $this->GET['only_group'] && $this->GET['only_group'] !== "") {
            $temp[0] = $this->findInMdTree($md_tree, $this->GET['only_group']);
        } else {
            $temp[0] = $this->findInMdTree($md_tree, 'content');    //content
            $temp[1] = $this->findInMdTree($md_tree, 'layout');     //layout
            $temp[2] = $this->findInMdTree($md_tree, 'page');       //page
            $temp[3] = $this->findInMdTree($md_tree, 'container');  //container
            $temp[4] = $this->findInMdTree($md_tree, 'site');       //site
            $temp[4] = $this->findInMdTree($md_tree, 'variable');
        }
        
        $md_tree = $temp;
        
        return $md_tree;
    }
    
    public function findInMdTree($md_tree, $query) {
        foreach ($md_tree as $item) {
            if ($item['id'] == $query) return $item;
        }
    }
    
    /**
     * filter to show only allowed items
     */
    public function filterAndAssignInfo($list) {

        $templates_info = getTemplatesInfo();
        
        // set selected item
        if ($this->GET['open']) $selected = $this->GET['open'];
        else $selected = $templates_info[$this->GET['node_group']]['default_template'];

        // create filtered array
        $filtered_list = array();
        
        foreach ($list as $item) {
            if (!array_key_exists($item['parent'], $templates_info)) {
                $filtered_list[] = $item;
                continue;
            }
                
            // dont' show items with visibility false
            $item_visibility_status = $templates_info[$item['parent']][$item['name']]['visibility'] ?? null;
            
            // but show item currently selected
            if ($item['parent'] == $this->GET['node_group'] && $selected == $item['name']) $item_visibility_status = true;

            if (is_bool($item_visibility_status) && $item_visibility_status === false) {
                continue;
            }

            //use template info title if available
            $templates_info_item_title = trim($templates_info[$item['parent']][$item['name']]['title'] ?? '');
            if ($templates_info_item_title !== '') $item['title'] = $templates_info_item_title;
            else $item['title'] = $item['name'];
        
            $filtered_list[] = $item;
        }

        // mark selected item
        foreach ($filtered_list as $k=>$item) {
            if ($item['name'] == $selected && $this->GET['node_group'] == $item['parent']) $filtered_list[$k]['selected'] = true;
            else $filtered_list[$k]['selected'] = false;
        }
        
        
        return $filtered_list;
    }
}
