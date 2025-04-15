<?php
/**
 * Taxonomy tree
 *
 * Copyright (c) 2011-2017 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/component/menu_js.php');

class Onyx_Controller_Component_Taxonomy_Menu extends Onyx_Controller_Component_Menu_Js {

    /**
     * get tree
     */
     
    public function getTree($publish, $filter, $parent, $depth, $expand_all) {

        $list = $this->getList($publish);

        return $this->buildTree($list, $parent, $depth);
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
}
