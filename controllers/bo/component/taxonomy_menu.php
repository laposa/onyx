<?php
/**
 * Taxonomy tree
 *
 * Copyright (c) 2008-2017 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/component/menu_js.php');

class Onxshop_Controller_Bo_Component_Taxonomy_Menu extends Onxshop_Controller_Component_Menu_Js {

    /**
     * main action
     */
    
    public function mainAction() {
        
        /**
         * root folder name
         */
         
        if (is_numeric($this->GET['id'])) {
            
            require_once('models/common/common_taxonomy.php');
            $Taxonomy = new common_taxonomy();
        
            $item_detail = $Taxonomy->taxonomyItemDetail($this->GET['id']);
            
            $root = array('id'=>$this->GET['id'], 'title'=>$item_detail['label']['title']);
            
        } else {
            
            $root = array('id'=>0, 'title'=>'Categories');
            
        }
        
        $this->tpl->assign('ROOT', $root);
        
        /**
         * standard action
         */
         
        return parent::mainAction();
        
    }

    /**
     * get tree
     */
     
    public function getTree($publish = 1, $filter, $parent, $depth, $expand_all) {

        $list = $this->getList($publish);

        return $this->buildTree($list);
    }

    /**
     * get list
     */
     
    public function getList($publish = 1) {
        
        require_once('models/common/common_taxonomy.php');
        $Taxonomy = new common_taxonomy();
        
        $list = $Taxonomy->getTree($publish);
        
        return $list;
    }
    
    /**
     * getFullPath
     */
     
    public function getFullPath() {
        
        return array();
        
    }
    
    /**
     * Is given node active? I.e. is it or its parent active?
     * Override in subclass
     */
    protected function isNodeActive(&$item)
    {
    }
}
