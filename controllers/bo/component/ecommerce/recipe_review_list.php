<?php
/**
 * Copyright (c) 2013-2015 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/component/comment_list.php');
require_once('models/ecommerce/ecommerce_recipe_review.php');
require_once('models/ecommerce/ecommerce_recipe.php');

class Onyx_Controller_Bo_Component_Ecommerce_Recipe_Review_List extends Onyx_Controller_Bo_Component_Comment_List {

    /**
     * Initialize models
     */

    public function initModels()
    {
        $this->key = "recipe";
        $this->Comment = new ecommerce_recipe_review();
        $this->Node = new ecommerce_recipe();
    }

}

