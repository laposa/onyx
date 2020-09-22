<?php
/** 
 * Copyright (c) 2013 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/component/ecommerce/recipe_review.php');

class Onyx_Controller_Component_Ecommerce_Recipe_Review_List extends Onyx_Controller_Component_Ecommerce_Recipe_Review {

    /**
     * custom comment action
     */
     
    public function customCommentAction($data, $options) {
        
        /**
         * list comments
         */
        
        $this->listComments($data['node_id'], $options);
        
    }
        
}
