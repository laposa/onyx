<?php
/**
 * Copyright (c) 2011-2013 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/component/survey.php');

class Onyx_Controller_Bo_Component_Survey_List extends Onyx_Controller_Bo_Component_Survey {

    /**
     * main action
     */
     
    public function mainAction() {
    
        $list = $this->getList();
        
        $this->parseList($list);

        return true;
    }
    
    /**
     * get list
     */
     
    public function getList() {
    
        require_once('models/education/education_survey.php');
        $Survey = new education_survey();
        
        $list = $Survey->getSurveyListStats('', 'id DESC');

        return $list;
    }

    /**
     * parse
     */
    
    public function parseList($list) {
    
        if (count($list) > 0) {
            foreach ($list as $item) {
                
                $this->tpl->assign('ITEM', $item);
                $this->tpl->parse('content.item');
            }
        } else {
            $this->tpl->parse('content.empty');
        }
    }
}

