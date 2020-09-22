<?php
/**
 * Copyright (c) 2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/component/survey.php');

class Onyx_Controller_Bo_Component_Survey_Question_Add extends Onyx_Controller_Bo_Component_Survey {

    /**
     * main action
     */
     
    public function mainAction() {
    
        $this->Question = $this->initializeQuestion();
        
        /**
         * Save on request
         */
         
        if ($_POST['save'] && is_array($_POST['question'])) {
        
            $this->saveQuestion($_POST['question']);
            
        }
                

        /**
         * destroy
         */
         
        $this->Question = false;
        
        return true;
    }
    
}

