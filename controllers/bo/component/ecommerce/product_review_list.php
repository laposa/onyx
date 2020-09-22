<?php
/**
 * Copyright (c) 2010-2015 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/component/comment_list.php');
require_once('models/ecommerce/ecommerce_product_review.php');
require_once('models/ecommerce/ecommerce_product.php');

class Onxshop_Controller_Bo_Component_Ecommerce_Product_Review_List extends Onxshop_Controller_Bo_Component_Comment_List {
    
    /**
     * Initialize models
     */

    public function initModels()
    {
        $this->key = "product";
        $this->Comment = new ecommerce_product_review();
        $this->Node = new ecommerce_product();
    }

}

