<?php
/**
 *
 * Copyright (c) 2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class education_survey_question_answer extends Onxshop_Model {

	/**
	 * PRIMARY KEY
	 *
	 */
	public $id;
	
	/**
	 * question_id
	 */
	public $question_id;
	
	/**
	 * title
	 */
	public $title;
	
	/**
	 * description
	 * can use ### for substitution of input field
	 */
	public $description;
	
	/**
	 * is_correct
	 */
	public $is_correct;
	
	/**
	 * points
	 * for test valuation
	 * 
	 */
	public $points;
	
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
		'question_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'title'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'description'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'is_correct'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'points'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'priority'=>array('label' => '', 'validation'=>'int', 'required'=>false),
		'publish'=>array('label' => '', 'validation'=>'int', 'required'=>false)
	);
	
	/**
	 * create table
	 */
	 
	private function createTable() {
	
		$sql = "
CREATE TABLE education_survey_question_answer (
	id serial PRIMARY KEY NOT NULL,
	question_id int NOT NULL REFERENCES education_survey_question ON UPDATE CASCADE ON DELETE CASCADE,
	title text NOT NULL,
	description text,
	is_correct smallint, 
	points smallint,
	priority smallint DEFAULT 0,
	publish smallint DEFAULT 1
);
		";
	}

	/**
	 * getDetail
	 */
	 
	public function getDetail($answer_id) {
		
		if (!is_numeric($answer_id)) {
			msg("Answer ID is not numeric", 'error');
			return false;
		}
		
		$detail = $this->detail($answer_id);
		
		return $detail;
	}
	
	/**
	 * listAnswersForQuestion
	 */
	 
	public function listAnswersForQuestion($question_id) {
	
		if (!is_numeric($question_id)) {
			msg("Question ID is not numeric", 'error');
			return false;
		}
		
		$answer_list = $this->listing("question_id = $question_id", 'id ASC, priority DESC');
		
		return $answer_list;
		
	}
	 
	/**
	 * saveAnswer
	 */
	
	public function saveAnswer($data) {
	
		if (!is_array($data)) return false;
		//temp before switching to ZendDb in version 1.5
		if ($data['points'] == '') $data['points'] = 0;
		/*require_once('lib/Zend/Db/Expr.php');
		if ($data['points'] == '') $data['points'] = new Zend_Db_Expr('NULL');
		msg(print_r($data, true));*/
		
		return $this->save($data);
		
	}
	
}
