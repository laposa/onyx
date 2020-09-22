<?php
/**
 * Copyright (c) 2013-2015 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/component/ecommerce/recipe_list.php');

class Onxshop_Controller_Component_Ecommerce_Recipe_List_4columns extends Onxshop_Controller_Component_Ecommerce_Recipe_List {

    /**
     * Parse recipe list items
     */
    public function parseItems(&$list)
    {
        foreach ($list as $k=>$item) {
            
            $pos = $k+1;
            
            if ($pos%4 == 0) $column_num = 'Four';
            if ($pos%3 == 0) $column_num = 'Three';
            else if (($pos%2 == 0)) $column_num = 'Two';
            else $column_num = 'One';
            
            $this->tpl->assign('COLUMN_NUM', $column_num);
            
            $this->parseItem($item, 'content.layout.item');
            
            if ($pos%4 == 0) $this->tpl->parse('content.layout');
        
        }
        
    }

}
