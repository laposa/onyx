<?php
/**
 * Copyright (c) 2013-2021 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/node/content/default.php');
require_once('models/education/education_survey.php');

class Onyx_Controller_Bo_Node_Content_Survey extends Onyx_Controller_Bo_Node_Content_Default {

    /**
     * pre action
     */
     
    function pre() {
    
        parent::pre();
        
        if ($_POST['node']['component']['require_user_details'] == 'on') $_POST['node']['component']['require_user_details'] = 1;
        else $_POST['node']['component']['require_user_details'] = 0;
        if ($_POST['node']['component']['require_t_and_c'] == 'on') $_POST['node']['component']['require_t_and_c'] = 1;
        else $_POST['node']['component']['require_t_and_c'] = 0;
        if ($_POST['node']['component']['display_results'] == 'on') $_POST['node']['component']['display_results'] = 1;
        else $_POST['node']['component']['display_results'] = 0;
    }

    /**
     * post action
     */
     
    function post() {

        parent::post();
        
        // require user details
        $this->node_data['component']['require_user_details'] = ($this->node_data['component']['require_user_details']) ? 'checked="checked"' : '';
        // require terms and conditions
        $this->node_data['component']['require_t_and_c'] = ($this->node_data['component']['require_t_and_c']) ? 'checked="checked"' : '';
        // display results options
        $this->node_data['component']['display_results'] = ($this->node_data['component']['display_results']) ? 'checked="checked"' : '';
        
        // survey dropdown  
        $Survey = new education_survey();

        $surveys = $Survey->getSurveyList();
        
        foreach ($surveys as $survey) {
            
            $survey['selected'] = ($this->node_data['component']['survey_id'] == $survey['id']) ? "selected='selected'" : '';
            
            if ($survey['publish'] == 0) $this->tpl->assign('NOT_PUBLISHED', '(Not published)');
            else $this->tpl->assign('NOT_PUBLISHED', '');
            
            $this->tpl->assign("SURVEY_ITEM", $survey);
            $this->tpl->parse("content.survey_item");
        }

        // template dropdown    
        $this->tpl->assign("SELECTED_template_{$this->node_data['component']['template']}", "selected='selected'");

        // restriction dropdown 
        $this->tpl->assign("SELECTED_restriction_{$this->node_data['component']['restriction']}", "selected='selected'");

        // limit dropdown   
        $this->tpl->assign("SELECTED_limit_{$this->node_data['component']['limit']}", "selected='selected'");

    }
}
