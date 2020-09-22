<?php
/**
 * Copyright (c) 2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/component/survey.php');

class Onxshop_Controller_Bo_Component_Survey_Question_Edit extends Onxshop_Controller_Bo_Component_Survey {

    /**
     * main action
     */
     
    public function mainAction() {
    
        $this->Question = $this->initializeQuestion();
        
        /**
         * Save on request
         */
         
        if ($_POST['save'] && is_array($_POST['question'])) {
            
            $question_data = $_POST['question'];
            if (!is_numeric($question_data['mandatory'])) $question_data['mandatory'] = 0;
            $this->saveQuestion($question_data);
            
        }
        
        /**
         * Display Detail
         */
         
        if (is_numeric($this->GET['id'])) $this->displayQuestion($this->GET['id']);
        

        /**
         * destroy
         */
         
        $this->Question = false;
        
        return true;
    }
    
}

