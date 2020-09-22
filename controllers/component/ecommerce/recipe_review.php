<?php
/** 
 * Copyright (c) 2013-2016 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/component/ecommerce/product_review.php');

class Onyx_Controller_Component_Ecommerce_Recipe_Review extends Onyx_Controller_Component_Ecommerce_Product_Review {
    
    /**
     * initialize comment
     */
     
    public function initializeComment() {
    
        require_once('models/ecommerce/ecommerce_recipe_review.php');
        return new ecommerce_recipe_review();
        
    }
    
    /**
     * custom comment action
     */
     
    public function customCommentAction($data, $options) {
    
        $_Onyx_Request = new Onyx_Request("component/ecommerce/recipe_review_list~node_id={$data['node_id']}:allow_anonymouse_submit={$options['allow_anonymouse_submit']}~");
        $this->tpl->assign('REVIEW_LIST', $_Onyx_Request->getContent());
        
        $_Onyx_Request = new Onyx_Request("component/ecommerce/recipe_review_add~node_id={$data['node_id']}:allow_anonymouse_submit={$options['allow_anonymouse_submit']}~");
        $this->tpl->assign('REVIEW_ADD', $_Onyx_Request->getContent());
        
    }
    
}
