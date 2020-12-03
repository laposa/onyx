<?php
/**
 *
 * Copyright (c) 2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class education_survey_question_answer extends Onyx_Model {

    /**
     * PRIMARY KEY
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
     * other_data
     */
    public $other_data;

    /**
     * hashMap
     */

    public $_metaData = [
        'id'          => ['label' => '', 'validation' => 'int', 'required' => true],
        'question_id' => ['label' => '', 'validation' => 'int', 'required' => true],
        'title'       => ['label' => '', 'validation' => 'string', 'required' => true],
        'description' => ['label' => '', 'validation' => 'string', 'required' => false],
        'is_correct'  => ['label' => '', 'validation' => 'int', 'required' => false],
        'points'      => ['label' => '', 'validation' => 'int', 'required' => true],
        'priority'    => ['label' => '', 'validation' => 'int', 'required' => false],
        'publish'     => ['label' => '', 'validation' => 'int', 'required' => false],
        'other_data'  => ['label' => '', 'validation' => 'string', 'required' => false],
    ];

    /**
     * create table sql
     */
    private function getCreateTableSql()
    {
        $sql = "CREATE TABLE education_survey_question_answer (
            id serial PRIMARY KEY NOT NULL,
            question_id int NOT NULL REFERENCES education_survey_question ON UPDATE CASCADE ON DELETE CASCADE,
            title text NOT NULL,
            description text,
            is_correct smallint, 
            points smallint,
            priority smallint DEFAULT 0,
            publish smallint DEFAULT 1,
            other_data text
        );

            CREATE INDEX education_survey_question_answer_question_id_idx ON education_survey_question_answer (question_id);
        ";

        return $sql;
    }

    /**
     * getDetail
     */
    public function getDetail($answer_id)
    {
        if (!is_numeric($answer_id)) {
            msg("Answer ID is not numeric", 'error');
            return false;
        }

        return $this->detail($answer_id);
    }

    /**
     * listAnswersForQuestion
     */
    public function listAnswersForQuestion($question_id)
    {
        if (!is_numeric($question_id)) {
            msg("Question ID is not numeric", 'error');
            return false;
        }

        $answer_list = $this->listing("question_id = $question_id", 'priority DESC, id ASC');
        return $answer_list;
    }

    /**
     * saveAnswer
     */
    public function saveAnswer($data)
    {
        if (!is_array($data)) return false;
        if ($data['points'] == '') $data['points'] = 0;

        return $this->save($data);
    }
}
