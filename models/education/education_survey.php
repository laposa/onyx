<?php
/**
 *
 * Copyright (c) 2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class education_survey extends Onxshop_Model {

	/**
	 * PRIMARY KEY
	 *
	 */
	public $id;
	
	/**
	 * title
	 */
	public $title;
	
	/**
	 * description
	 */
	public $description;
	
	/**
	 * created
	 */
	public $created;
	
	/**
	 * modified
	 */
	public $modified;
	
	/**
	 * priority
	 */
	public $priority;

	/**
	 * publish
	 */
	public $publish;

	/**
	 * hashMap
	 */
	 
	public $_hashMap = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'title'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'description'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'created'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
		'modified'=>array('label' => '', 'validation'=>'datetime', 'required'=>false),
		'priority'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'publish'=>array('label' => '', 'validation'=>'int', 'required'=>false)
	);
	
	/**
	 * create table
	 */
	 
	private function createTable() {
	
		$sql = "
CREATE TABLE education_survey (
	id serial PRIMARY KEY NOT NULL,
	title varchar(255) NOT NULL,
	description text,
	created timestamp(0) without time zone DEFAULT now() NOT NULL,
	modified timestamp(0) without time zone DEFAULT now(),
	priority smallint DEFAULT 0,
	publish smallint DEFAULT 0
);
		";
	}
	
	/**
	 * getSurveyList
	 */
	 
	public function getSurveyList($where = '', $sort = 'priority ASC, id DESC') {
	
		$list = $this->listing($where, $sort);
		
		return $list;
	
	}

	/**
	 * getDetail
	 */
	 
	public function getDetail($survey_id) {
		
		if (!is_numeric($survey_id)) {
			msg("Survey ID is not numeric", 'error');
			return false;
		}
		
		$detail = $this->detail($survey_id);
		
		return $detail;
	}
	
	
	/**
	 * get full detail
	 */
	 
	public function getFullDetail($survey_id) {
	
		if (!is_numeric($survey_id)) {
			msg("Survey ID is not numeric", 'error');
			return false;
		}
		
		$detail = $this->getDetail($survey_id);
		$detail['question_list'] = $this->getFullQuestionsList($survey_id);
		
		return $detail;
	}
	
	
	/**
	 * list questions
	 */
	 
	public function getFullQuestionsList($survey_id) {
	
		if (!is_numeric($survey_id)) {
			msg("Survey ID is not numeric", 'error');
			return false;
		}
		
		require_once('models/education/education_survey_question.php');
		$SurveyQuestion = new education_survey_question();
		
		$question_list = $SurveyQuestion->listQuestions($survey_id);
		
		return $question_list;
		
	}
	
	/**
	 * updateSurvey
	 */
	
	public function saveSurvey($data) {
	
		if (!is_array($data)) return false;
		
		$data['modified'] = date('c');
		
		return $this->save($data);
		
	}
	
	/**
	 * getSurveyUsageCount
	 *
	 */
	 
	public function getSurveyUsageCount($survey_id, $relation_subject = false) {
	
		if (!is_numeric($survey_id)) return false;
		
		require_once('models/education/education_survey_entry.php');
		$SurveyEntry = new education_survey_entry();
		
		if ($relation_subject) $where = "survey_id = {$survey_id} AND relation_subject = '$relation_subject'";
		else $where = "survey_id = {$survey_id}";
		
		$usage_count = $SurveyEntry->count($where);
		
		return $usage_count;
		
	}
		
}
