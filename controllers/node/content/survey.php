<?php
/** 
 * Copyright (c) 2013-2019 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/node/content/default.php');

class Onxshop_Controller_Node_Content_Survey extends Onxshop_Controller_Node_Content_Default {

    /**
     * main action
     */

    public function mainAction() {
        
        /**
         * initialize node
         */
         
        require_once('models/common/common_node.php');
        $Node = new common_node();
        $node_data = $Node->nodeDetail($this->GET['id']);
        $survey_id = $node_data['component']['survey_id'];

        /**
         * init parameters
         */

        switch ($node_data['component']['template']) {

            case 'image_poll':
                $component = 'survey_image_poll';
                break;

            case 'survey_2':
                $component = 'survey_2';
                break;
            case 'survey':
            default:
                $component = 'survey';
                break;
        }

        $limit = (string) $node_data['component']['limit'];
        $votes_per_day = (int) $node_data['component']['votes_per_day'];
        $restriction = (string) $node_data['component']['restriction'];
        $spam_protection = (string) $node_data['component']['spam_protection'];
        $require_user_details = (int) $node_data['component']['require_user_details'];
        $require_t_and_c = (int) $node_data['component']['require_t_and_c'];
        $display_results = (int) $node_data['component']['display_results'];
        $href = (string) $node_data['component']['href'];
        $message_after_submission = urlencode((string) $node_data['component']['message_after_submission']);
        
        /**
         * call controller
         */

        $_Onxshop_Request = new Onxshop_Request("component/$component~node_id={$node_data['id']}:survey_id=$survey_id:limit=$limit:votes_per_day=$votes_per_day:restriction=$restriction:spam_protection=$spam_protection:require_user_details=$require_user_details:require_t_and_c=$require_t_and_c:display_results=$display_results:href=$href:message_after_submission=$message_after_submission~");
        $this->tpl->assign('SURVEY', $_Onxshop_Request->getContent());

        $this->tpl->assign('NODE', $node_data);

        if ($node_data['display_title'])  $this->tpl->parse('content.title');

        return true;
    }
}
