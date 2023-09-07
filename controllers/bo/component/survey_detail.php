<?php
/**
 * Copyright (c) 2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/component/survey.php');

class Onyx_Controller_Bo_Component_Survey_Detail extends Onyx_Controller_Bo_Component_Survey {

    /**
     * main action
     */
     
    public function mainAction() {

        if (is_numeric($this->GET['id'])) $survey_id = $this->GET['id'];
        else {
            msg("Survey ID is not numeric", 'error');
            return false;
        }

        require_once('models/education/education_survey.php');
        
        $this->Survey = new education_survey();

        if ($_SERVER['REQUEST_METHOD'] === "POST" && $_POST['action'] == 'delete-entries') {
            $this->deleteEntries($survey_id);
            return true;
        }
        
        $survey_detail = $this->Survey->getFullDetail($survey_id);
        
        $this->displaySurvey($survey_detail);
        
        return true;
        
    }
    
    /**
     * displaySurvey
     */
     
    public function displaySurvey($survey_detail) {
        
        if (!is_array($survey_detail)) {
            msg("Survey detail isn't array", 'error');
            return false;
        }
        
        foreach ($survey_detail['question_list'] as $item) {
            
            $this->displayQuestion($item);
            
        }

        if (count($survey_detail['question_list']) == 0) $this->tpl->parse('content.empty');
        
        $this->tpl->assign('SURVEY', $survey_detail);
    
    }

    /**
     * displayQuestion
     */
     
    public function displayQuestion($question_detail) {
    
        if (!is_array($question_detail)) {
            msg("Question detail isn't array", 'error');
            return false;
        }
        
        $this->tpl->assign('QUESTION', $question_detail);
        
        if ($question_detail['type'] == 'text' || $question_detail['type'] == 'file' || $question_detail['type'] == 'range') {
        
            $this->tpl->parse('content.question.answer_text');
        
        } else {
            
            foreach ($question_detail['answer_list'] as $item) {
                
                $item['usage'] = $this->Survey->getAnswerUsage($item['id']);
                $this->displayAnswer($item);
                
            }
            
            $this->tpl->parse('content.question.answer_list');
        }
        
        $this->tpl->parse('content.question');
        
    }
    
    
    /**
     * displayAnswer
     */
     
    public function displayAnswer($answer_detail) {
        
        if (!is_array($answer_detail)) {
            msg("Answer detail isn't array", 'error');
            return false;
        }
        
        $this->tpl->assign('ANSWER', $answer_detail);
        $this->tpl->parse('content.question.answer_list.item');
    }

    public function deleteEntries($survey_id) {
        $this->Survey->deleteEntries($survey_id);
    }
}

