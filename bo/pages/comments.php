<?php
/**
 * Comments controller
 *
 * Copyright (c) 2015 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onyx_Controller_Bo_Pages_Comments extends Onyx_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {

        if ($this->GET['products']) {
            $controller = "bo/component/ecommerce/product_review_list";
            $active['products'] = "active";
        } else if ($this->GET['recipes']) {
            $controller = "bo/component/ecommerce/recipe_review_list";
            $active['recipes'] = "active";
        } else {
            $controller = "bo/component/comment_list";
            $active['comments'] = "active";
        }

        $list = new Onyx_Request($controller);
        $this->tpl->assign('ACTIVE', $active);
        $this->tpl->assign('SUBCONTENT', $list->getContent());

        return true;
    }
}

