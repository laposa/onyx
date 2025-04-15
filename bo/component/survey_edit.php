<?php
/**
 * Copyright (c) 2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/component/survey.php');

class Onyx_Controller_Bo_Component_Survey_Edit extends Onyx_Controller_Bo_Component_Survey {

    public $Survey;

    /**
     * main action
     */
     
    public function mainAction() {
    
        $this->Survey = $this->initializeSurvey();
        
        /**
         * Save on request
         */
         
        if (isset($_POST['save']) && $_POST['save'] && is_array($_POST['survey'])) {
        
            $this->saveSurvey($_POST['survey']);
            $id = (int) $this->GET['id'];
            onyxGoto("/backoffice/surveys/$id/detail");
            
        }
        
        /**
         * Display Detail
         */
         
        if (is_numeric($this->GET['id'])) $this->displaySurvey($this->GET['id']);
        

        /**
         * destroy
         */
         
        $this->Survey = false;
        
        return true;
    }
    
}

