<?php
/** 
 * Copyright (c) 2015 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/api/v1_3/recipe_list.php');
require_once('controllers/api/v1_4/recipe_detail.php');

class Onyx_Controller_Api_v1_4_Recipe_List extends Onyx_Controller_Api_v1_3_Recipe_List {
    
    static $thumbnail_size = 200;
    
    /**
     * formatItem
     */
     
    public function formatItem($item) {
        
        return Onyx_Controller_Api_v1_4_Recipe_Detail::formatItem($item);
        
    }
    
}
