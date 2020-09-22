<?php
/** 
 * Copyright (c) 2010-2013 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/component/ecommerce/product_review.php');

class Onxshop_Controller_Component_Ecommerce_Product_Review_Add extends Onxshop_Controller_Component_Ecommerce_Product_Review {

    /**
     * custom comment action
     */
     
    public function customCommentAction($data, $options) {

        // enable captcha if captcha field is present in the template
        $this->enableCaptcha = (strpos($this->tpl->filecontents, 'comment-captcha_') !== FALSE);
        if ($this->enableCaptcha) $this->tpl->parse("content.comment_insert.invisible_captcha_field");
        
        $this->displaySubmitForm($data, $options);
                
    }
    
    

}
