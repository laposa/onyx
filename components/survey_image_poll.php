<?php
/** 
 * Copyright (c) 2013 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/component/survey.php');

class Onyx_Controller_Component_Survey_Image_Poll extends Onyx_Controller_Component_Survey {

    protected function createFacebookStory()
    {
        if (client_action::hasOpenGraphStory('enter', 'competition')) {
            $request = new Onyx_Request("component/client/facebook_story_create~" 
                . "action=vote_in"
                . ":object=poll"
                . ":node_id=" . $_SESSION['active_pages'][0]
                . "~");
        }
    }

}
