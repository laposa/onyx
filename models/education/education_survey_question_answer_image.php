<?php
require_once('models/common/common_image.php');

/**
 * class education_survey_question_answer_image
 *
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class education_survey_question_answer_image extends common_image {

	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "
CREATE TABLE education_survey_question_answer_image ( 
	id serial NOT NULL PRIMARY KEY,
	src character varying(255),
	role character varying(255),
	node_id int NOT NULL REFERENCES education_survey_question_answer ON UPDATE CASCADE ON DELETE CASCADE,
	title character varying(255),
	description text,
	priority integer DEFAULT 0 NOT NULL,
	modified timestamp(0) without time zone,
	author integer
);
		";
		
		return $sql;
	}
}
