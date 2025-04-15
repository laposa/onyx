<?php
/**
 * Copyright (c) 2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/component/survey.php');

class Onyx_Controller_Bo_Component_Survey_Question_Answer_Edit extends Onyx_Controller_Bo_Component_Survey {

    // TODO could be modal window?
    public $QuestionAnswer;
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        $this->QuestionAnswer = $this->initializeQuestionAnswer();
        
        /**
         * Save on request
         */
         
        if (is_array($_POST['answer'] ?? null)) {
        
            $this->saveQuestionAnswer($_POST['answer']);
            
        }
        
        /**
         * Display Detail
         */
         
        if (is_numeric($this->GET['id'])) $this->displayQuestionAnswer($this->GET['id']);
        

        /**
         * destroy
         */
         
        $this->QuestionAnswer = false;
        
        return true;
    }
    
}

