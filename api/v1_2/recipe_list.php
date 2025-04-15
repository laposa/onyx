<?php
/** 
 * Copyright (c) 2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/api/v1_1/recipe_list.php');
require_once('controllers/api/v1_2/recipe_detail.php');

class Onyx_Controller_Api_v1_2_Recipe_List extends Onyx_Controller_Api_v1_1_Recipe_List {
    
    /**
     * formatItem
     */
     
    public function formatItem($item) {
        
        return Onyx_Controller_Api_v1_2_Recipe_Detail::formatItem($item);
        
    }
    
}
