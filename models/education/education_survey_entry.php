<?php
/**
 *
 * Copyright (c) 2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class education_survey_entry extends Onxshop_Model {

	/**
	 * PRIMARY KEY
	 *
	 */
	public $id;
	
	/**
	 * survey_id
	 */
	public $survey_id;
	
	/**
	 * customer_id
	 */
	public $customer_id;
	
	/**
	 * relation_subject
	 * reference to something, when using the same survey for a different subject
	 */
	public $relation_subject;
	
	/**
	 * created
	 */
	public $created;

	/**
	 * modified
	 */
	public $modified;
	
	/**
	 * publish
	 */
	public $publish;

	/**
	 * hashMap
	 */
	 
	public $_hashMap = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'survey_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'customer_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'relation_subject'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'created'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
		'modified'=>array('label' => '', 'validation'=>'datetime', 'required'=>false),
		'publish'=>array('label' => '', 'validation'=>'int', 'required'=>false)
	);
	
	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "
CREATE TABLE education_survey_entry (
	id serial PRIMARY KEY NOT NULL,
	survey_id int NOT NULL REFERENCES education_survey ON UPDATE CASCADE ON DELETE RESTRICT,
	customer_id int NOT NULL REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT,
	relation_subject text,
	created timestamp(0) without time zone DEFAULT now() NOT NULL,
	modified timestamp(0) without time zone DEFAULT now(),
	publish smallint DEFAULT 0,
	UNIQUE (survey_id, customer_id, relation_subject)
);
		";
		
		return $sql;
	}
	
	/**
	 * getAnswersForQuestion
	 */
	 
	public function getAnswersForQuestion($question_id, $relation_subject = false) {
		
		require_once('models/education/education_survey_entry_answer.php');
		$SurveyEntryAnswer = new education_survey_entry_answer();
		
		//hack for Unicode in Postgresql/JSON
		if ($relation_subject) $relation_subject = preg_replace('/\\\/', '_', $relation_subject);
		
		return $SurveyEntryAnswer->getAnswersForQuestion($question_id, $relation_subject);
		
	}
	
	/**
	 * getAnswerUsageCount
	 */
	 
	public function getAnswerUsageCount($question_answer_id, $relation_subject = false) {
	
		require_once('models/education/education_survey_entry_answer.php');
		$SurveyEntryAnswer = new education_survey_entry_answer();
		
		//hack for Unicode in Postgresql/JSON
		if ($relation_subject) $relation_subject = preg_replace('/\\\/', '_', $relation_subject);
		
		return $SurveyEntryAnswer->getAnswerUsageCount($question_answer_id, $relation_subject);
	}
	
	/**
	 * getSurveyUsageCount
	 *
	 */
	 
	public function getSurveyUsageCount($survey_id, $relation_subject = false) {
	
		if (!is_numeric($survey_id)) return false;
		
		//hack for Unicode in Postgresql/JSON
		if ($relation_subject) $relation_subject = preg_replace('/\\\/', '_', $relation_subject);
		
		if ($relation_subject) $where = "survey_id = {$survey_id} AND relation_subject LIKE '$relation_subject'";
		else $where = "survey_id = {$survey_id}";
		
		$usage_count = $this->count($where);
		
		return $usage_count;
		
	}

	/**
	 * saveEntry
	 */
	
	public function saveEntry($data) {
	
		if (!is_array($data)) {
			msg("survey_entry: data is not array", 'error');
			return false;
		}
		
		if (!$data['created']) $data['created'] = date('c');
		$data['modified'] = date('c');
		if (!is_numeric($data['publish'])) $data['publish'] = 1; 
		
		if ($survey_entry_id = $this->save($data)) {
			
			return $survey_entry_id;	
			
		} else {
		
			msg("Cannot saveEntry", 'error');
			return false;
		}
		
	}
	
	/**
	 * saveEntryFull
	 */
	 
	public function saveEntryFull($data) {
		
		if (!is_array($data)) {
			msg("survey_entry: data is not array", 'error');
			return false;
		}
		
		/**
		 * first try to save into education_survey_entry table
		 */
		 
		$survey_entry_data = array();
		$survey_entry_data['survey_id'] = $data['survey_id'];
		$survey_entry_data['customer_id'] = $data['customer_id'];
		if ($data['relation_subject']) $survey_entry_data['relation_subject'] = $data['relation_subject'];
		
		$survey_entry_id = $this->saveEntry($survey_entry_data);
		
		/**
		 * than save each answer
		 */

		if (is_numeric($survey_entry_id)) {

			require_once('models/education/education_survey_entry_answer.php');
			
			foreach ($data['answers'] as $answer) {
				$answer['survey_entry_id'] = $survey_entry_id;
				//TEMP reset before each insert (can be removed in Onxshop 1.5)
				$EntryAnswer = new education_survey_entry_answer();
				if (!$EntryAnswer->saveAnswer($answer)) {
					msg("Error occured in saving " . print_r($answer, true));
					$error_occured = true;
				}
			}

		}
		
		//TODO
		//if ($error_occured) $this->delete($survey_entry_id);
		
		return $survey_entry_id;
		
	}
	
	/**
	 * getSurveyCustomerCount
	 */
	 
	public function getSurveyCustomerCount($survey_id = false, $relation_subject = false) {
		
		$add_to_where = '1=1 ';
		
		if (is_numeric($survey_id)) {
			$add_to_where .= " AND education_survey_entry.survey_id = $survey_id";
		}
		
		if ($relation_subject) {	
			$add_to_where .= " AND relation_subject LIKE '$relation_subject'";
		}
		
		$sql = "SELECT count(DISTINCT customer_id) FROM education_survey_entry WHERE $add_to_where";
		$result = $this->executeSql($sql);
		
		return $result[0]['count'];
	}
	
	/**
	 * get average rating
	 */
	
	public function getAverageRating($survey_id = false, $relation_subject = false) {
		
		$add_to_where = '1=1 ';

        if (is_numeric($survey_id)) {
            $add_to_where .= " AND education_survey_entry.survey_id = $survey_id";
        }

        if ($relation_subject) {
            $add_to_where .= " AND relation_subject LIKE '$relation_subject'";
        }

        $sql = "SELECT avg(education_survey_question_answer.points) FROM education_survey_entry
        LEFT OUTER JOIN education_survey_entry_answer ON (education_survey_entry_answer.survey_entry_id = education_survey_entry.id)
        LEFT OUTER JOIN education_survey_question_answer ON (education_survey_question_answer.id = education_survey_entry_answer.question_answer_id)
        WHERE $add_to_where;
        ";

        $result = $this->executeSql($sql);

        return $result[0]['avg'];
        
	}
	
	/**
	 * get weighted mean
	 */
	 
	public function getWeightedMean($survey_id = false, $relation_subject = false) {
	
		$add_to_where = '1=1 ';
		
		if (is_numeric($survey_id)) {
			$add_to_where .= " AND education_survey_entry.survey_id = $survey_id";
		}
		
		if ($relation_subject) {
			$add_to_where .= " AND relation_subject LIKE '$relation_subject'";
		}
		
		$sql = "SELECT sum(education_survey_question_answer.points * education_survey_question.weight) / sum(education_survey_question.weight) AS weighted_mean FROM education_survey_entry
		LEFT OUTER JOIN education_survey_entry_answer ON (education_survey_entry_answer.survey_entry_id = education_survey_entry.id)
		LEFT OUTER JOIN education_survey_question_answer ON (education_survey_question_answer.id = education_survey_entry_answer.question_answer_id)
		LEFT OUTER JOIN education_survey_question ON (education_survey_question.id = education_survey_question_answer.question_id)
		WHERE $add_to_where;
		";
		
		$result = $this->executeSql($sql);
		
		return $result[0]['weighted_mean'];
	
	}
	
}
