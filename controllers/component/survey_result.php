<?php
/** 
 * Copyright (c) 2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('controllers/component/survey.php');

class Onxshop_Controller_Component_Survey_Result extends Onxshop_Controller_Component_Survey {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		/**
		 * input
		 */
		 
		if (is_numeric($this->GET['survey_id'])) $survey_id = $this->GET['survey_id'];
		else {
			msg("Survey ID is not numeric", 'error');
			return false;
		}
		
		/**
		 * initialise
		 */
		 
		require_once('models/education/education_survey.php');
		require_once('models/education/education_survey_entry.php');
		$this->Survey = new education_survey();
		$this->SurveyEntry = new education_survey_entry();
		
		
		/**
		 * get survey detail
		 */
		 
		$survey_detail = $this->Survey->getFullDetail($survey_id);
		
		/**
		 * incase we use the same survey for different subjects
		 */
		 
		$relation_subject = $this->getRelationSubject();
		
		//get survey result
		$survey_detail_full = $this->getSurveyResult($survey_detail, $relation_subject);
		
		/**
		 * display
		 */
	
		$this->displaySurvey($survey_detail_full);		
	
		
		return true;
		
	}
	
	/**
	 * get survey result
	 */
	 
	public function getSurveyResult($survey_detail, $relation_subject = false) {
	
		/**
		 * alter question_list to add results data
		 */
		
		foreach ($survey_detail['question_list'] as $kq=>$question) {
			
			if ($question['type'] == 'text') {
			
				$question['answer_list'] = $this->getAnswersForQuestion($question['id'], $relation_subject);
			
			} else {
			
				//add usage count and find max
				$usage_count_max = 0;
				foreach ($question['answer_list'] as $ka=>$answer) {	
					$usage_count = $this->getAnswerUsage($answer['id'], $relation_subject);
					$question['answer_list'][$ka]['usage_count'] = $usage_count;
					if ($usage_count > $usage_count_max) $usage_count_max = $usage_count;
				}
				
				//calculate usage_scale (1 to 10)
				foreach ($question['answer_list'] as $ka=>$answer) {
				
					if ($usage_count_max > 0) $usage_scale = $answer['usage_count'] / $usage_count_max * 10;
					else $usage_scale = 0;
					
					$question['answer_list'][$ka]['usage_scale'] = round($usage_scale);
					$question['answer_list'][$ka]['usage_scale_percentage'] = $usage_scale * 10;
				}
			}
			
			$survey_detail['question_list'][$kq] = $question; 
		}
	
		/**
		 * add number of total responses
		 */
		 
		$survey_detail['number_of_responses'] = $this->getSurveyUsage($survey_detail['id'], $relation_subject);
		
		/**
		 * get average rating
		 */
		 
		$survey_detail = $this->calculateSurveyAverageRating($survey_detail, $relation_subject);
		
		return $survey_detail;
		
	}
	
	/**
	 * getAnswersForQuestion
	 */
	 
	public function getAnswersForQuestion($question_id, $relation_subject = false) {
		
		if (!is_numeric($question_id)) return false;
		
		$list = $this->SurveyEntry->getAnswersForQuestion($question_id, $relation_subject);
		
		return $list;
	}
	
	/**
	 * getAnswerUsage
	 */
	 
	public function getAnswerUsage($question_answer_id, $relation_subject = false) {
		
		if (!is_numeric($question_answer_id)) return false;
		
		$usage_count = $this->SurveyEntry->getAnswerUsageCount($question_answer_id, $relation_subject);
		
		return $usage_count;
	}
	
	/**
	 * getSurveyUsage
	 */
	 
	public function getSurveyUsage($survey_id, $relation_subject = false) {
		
		if (!is_numeric($survey_id)) return false;
		
		$usage_count = $this->SurveyEntry->getSurveyUsageCount($survey_id, $relation_subject);

		return $usage_count;
	}
	
	/**
	 * calculateSurveyAverageRating
	 */
	 
	public function calculateSurveyAverageRating($survey_detail, $relation_subject = fals) {
	
		if (!is_array($survey_detail)) return false;
		if (!is_array($survey_detail['question_list'])) return false;
		
		$survey_total_x = 0;
		$survey_total_sum = 0;
			
		foreach ($survey_detail['question_list'] as $k=>$item) {
			
			$question_total_x = 0;
			$question_total_sum = 0;
			
			if (($item['type'] == 'radio' || $item['type'] == 'select') && is_array($item['answer_list'])) {
			
				foreach ($item['answer_list'] as $answer_k=>$answer_item) {
					$question_total_x = $question_total_x + $answer_item['usage_count'] * $answer_item['points'];
					$question_total_sum = $question_total_sum + $answer_item['usage_count'];
				}
				
				if ($question_total_sum > 0) $survey_detail['question_list'][$k]['average_rating'] = $question_total_x / $question_total_sum;
				else $survey_detail['question_list'][$k]['average_rating'] = 'n/a';
			
			} else {
				
				$survey_detail['question_list'][$k]['average_rating'] = 'n/a';
				
			}
		
			$survey_total_x = $survey_total_x + $question_total_x;
			$survey_total_sum = $survey_total_sum + $question_total_sum;
		}
		
		if ($survey_total_sum > 0) $survey_detail['average_rating'] = $survey_total_x / $survey_total_sum;
		else $survey_detail['average_rating'] = 'n/a';
		
		/**
		 * weighted mean rating
		 * calculating manually at this place, but we could use education_survey_entry->getWeightedMean()
		 */
		/* Option 1: manual
		$weighted_mean_top = 0;
		$weighted_mean_bottom = 0;
				
		foreach ($survey_detail['question_list'] as $k=>$item) {
			
			if (is_numeric($item['average_rating'])) {
			
				$weighted_mean_top = $weighted_mean_top + $item['weight'] * $item['average_rating'];
				$weighted_mean_bottom = $weighted_mean_bottom + $item['weight'];
				
			}
			
		}
		
		if ($weighted_mean_bottom > 0) {
			
			$survey_detail['weighted_mean'] = $weighted_mean_top / $weighted_mean_bottom;
			
		} else {
			
			$survey_detail['weighted_mean'] = 'n/a';
			
		}*/
		
		// Option 2: weighted mean calculated from education_survey_entry->getWeightedMean()
		require_once('models/education/education_survey_entry.php');
		$SurveyEntry = new education_survey_entry();
		$weighted_mean = $SurveyEntry->getWeightedMean($survey_detail['id'], $relation_subject);
		
		if (is_numeric($weighted_mean)) {
		
			$survey_detail['weighted_mean'] = $weighted_mean;
		
		} else {
		
			$survey_detail['weighted_mean'] = 'n/a';
		
		}
		
		return $survey_detail;
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
		
		$this->tpl->assign('SURVEY', $survey_detail);
	
		$this->tpl->parse('content.result');
		
	}

	/**
	 * displayQuestion
	 */
	 
	public function displayQuestion($question_detail) {
	
		if (!is_array($question_detail)) {
			msg("Question detail isn't array", 'error');
			return false;
		}

		//don't show hidden questions
		if ($question_detail['publish'] != 1) return true;
		
		$this->tpl->assign('QUESTION', $question_detail);
		
		switch ($question_detail['type']) {
			
			case 'text':

				/**
				 * iterate through and mark if at least one answer is available
				 */
				 
				foreach ($question_detail['answer_list'] as $item) {
					
					if (strlen(trim($item['value'])) > 0) {
						
						$this->tpl->assign('ANSWER', $item);
						$this->tpl->parse('content.result.question.answer_list_text.item');
						
						$at_least_one_text_answer_is_available = true;
					}
				}
				
				/**
				 * check if at least one text answer is shown
				 */
				 
				if (!$at_least_one_text_answer_is_available) {
					
					$dummy_answer['value'] = 'n/a'; 
					$this->tpl->assign('ANSWER', $dummy_answer);
					$this->tpl->parse('content.result.question.answer_list_text.item');
				
				}

				/**
				 * display the answer wrapping block
				 */
				 
				$this->tpl->parse('content.result.question.answer_list_text');
				
			break;
			
			case 'radio':
				
				foreach ($question_detail['answer_list'] as $item) {
					$this->tpl->assign('ANSWER', $item);
					$this->tpl->parse('content.result.question.answer_list_radio.item');
				}
			
				$this->tpl->parse('content.result.question.answer_list_radio');
				$this->tpl->parse('content.result.question.average_rating');
			
			break;

			case 'select':
			default:
			
				foreach ($question_detail['answer_list'] as $item) {
					
					$this->tpl->assign('ANSWER', $item);
					$this->tpl->parse('content.result.question.answer_list_select.item');	
			
				}		
			
				$this->tpl->parse('content.result.question.answer_list_select');
				$this->tpl->parse('content.result.question.average_rating');
			
			break;
		}
		
		$this->tpl->parse('content.result.question');
		
	}
}
