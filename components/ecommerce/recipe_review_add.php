<?php
/** 
 * Copyright (c) 2013 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/component/ecommerce/recipe_review.php');
require_once('models/client/client_action.php');

class Onyx_Controller_Component_Ecommerce_Recipe_Review_Add extends Onyx_Controller_Component_Ecommerce_Recipe_Review {

    /**
     * custom comment action
     */
     
    public function customCommentAction($data, $options) {

        // enable captcha if captcha field is present in the template
        $this->enableCaptcha = (strpos($this->tpl->filecontents, 'comment-captcha_') !== FALSE);
        if ($this->enableCaptcha) $this->tpl->parse("content.comment_insert.invisible_captcha_field");

        $this->displaySubmitForm($data, $options);
    }

    public function insertComment($data, $options = false) {

        $result = parent::insertComment($data, $options);

        if ($result && client_action::hasOpenGraphStory('comment_on', 'recipe')) {
            $request = new Onyx_Request("component/client/facebook_story_create~" 
                . "action=comment_on"
                . ":object=recipe"
                . ":node_id=" . $_SESSION['active_pages'][0]
                . "~");
        }

        return $result;
    }

}
