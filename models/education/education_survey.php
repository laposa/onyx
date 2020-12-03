<?php

use Symfony\Contracts\Cache\ItemInterface;

/**
 *
 * Copyright (c) 2011-2018 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
class education_survey extends Onyx_Model {

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
     * other_data
     */
    public $other_data;

    /**
     * hashMap
     */

    public $_metaData = [
        'id'          => ['label' => '', 'validation' => 'int', 'required' => true],
        'title'       => ['label' => '', 'validation' => 'string', 'required' => true],
        'description' => ['label' => '', 'validation' => 'string', 'required' => false],
        'created'     => ['label' => '', 'validation' => 'datetime', 'required' => true],
        'modified'    => ['label' => '', 'validation' => 'datetime', 'required' => false],
        'priority'    => ['label' => '', 'validation' => 'int', 'required' => false],
        'publish'     => ['label' => '', 'validation' => 'int', 'required' => false],
        'other_data'  => ['label' => '', 'validation' => 'string', 'required' => false],
    ];

    /**
     * create table sql
     */

    private function getCreateTableSql()
    {

        $sql = "CREATE TABLE education_survey (
            id serial PRIMARY KEY NOT NULL,
            title varchar(255) NOT NULL,
            description text,
            created timestamp(0) without time zone DEFAULT now() NOT NULL,
            modified timestamp(0) without time zone DEFAULT now(),
            priority smallint DEFAULT 0,
            publish smallint DEFAULT 0,
            other_data text
        )";

        return $sql;
    }

    /**
     * getSurveyList
     */

    public function getSurveyList($where = '', $sort = 'priority ASC, id DESC')
    {

        $list = $this->listing($where, $sort);

        return $list;

    }

    /**
     * getSurveyListStats (cached)
     */

    public function getSurveyListStats($where = '', $sort = 'priority ASC, id DESC')
    {
        $list = $this->cache->get('getSurveyListStats', function (ItemInterface $item) use ($where, $sort) {
            $list = $this->getSurveyList($where, $sort);

            foreach ($list as $k => $item) {
                $list[$k]['usage_count'] = $this->getSurveyUsageCount($item['id']);
                $list[$k]['average_rating'] = $this->getAverageRating($item['id']);
            }

            return serialize($list);
        });

        return unserialize($list);
    }

    /**
     * getDetail
     */

    public function getDetail($survey_id)
    {

        if (!is_numeric($survey_id)) {
            msg("Survey ID is not numeric", 'error');
            return false;
        }

        $detail = $this->detail($survey_id);

        return $detail;
    }

    /**
     * get full detail
     * @param int $survey_id
     * @param boolean $include_stats
     * @returns array
     */

    public function getFullDetail($survey_id, $include_stats = true)
    {

        if (!is_numeric($survey_id)) {
            msg("Survey ID is not numeric", 'error');
            return false;
        }

        $detail = $this->getDetail($survey_id);
        $detail['question_list'] = $this->getFullQuestionsList($survey_id);

        if ($include_stats) {
            $detail['usage_count'] = $this->getSurveyUsageCount($survey_id);
            $detail['average_rating'] = $this->getAverageRating($survey_id);
        }

        return $detail;
    }

    /**
     * list questions
     */

    public function getFullQuestionsList($survey_id)
    {

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

    public function saveSurvey($data)
    {

        if (!is_array($data)) return false;

        $data['modified'] = date('c');

        return $this->save($data);

    }

    /**
     * getSurveyUsageCount
     *
     */

    public function getSurveyUsageCount($survey_id, $relation_subject = false)
    {

        if (!is_numeric($survey_id)) return false;

        require_once('models/education/education_survey_entry.php');
        $SurveyEntry = new education_survey_entry();

        $usage_count = $SurveyEntry->getSurveyUsageCount($survey_id, $relation_subject);

        if (is_numeric($usage_count)) return $usage_count;
        else return 'n/a';

    }

    /**
     * getAverageRating
     */

    public function getAverageRating($survey_id, $relation_subject = false)
    {

        if (!is_numeric($survey_id)) return false;

        require_once('models/education/education_survey_entry.php');
        $SurveyEntry = new education_survey_entry();

        $average_rating = $SurveyEntry->getWeightedMean($survey_id, $relation_subject);

        if (is_numeric($average_rating)) return $average_rating;
        else return 'n/a';

    }

    /**
     * getSurveyResult
     */

    public function getSurveyResult($survey_id, $relation_subject = false)
    {

        if (!is_numeric($survey_id)) return false;

        $survey_detail = $this->getFullDetail($survey_id);

        /**
         * alter question_list to add results data
         */

        foreach ($survey_detail['question_list'] as $kq => $question) {

            if ($question['type'] == 'text') {

                $question['answer_list'] = $this->getAnswersForQuestion($question['id'], $relation_subject);

            } else {

                //add usage count and find max
                $usage_count_max = 0;
                foreach ($question['answer_list'] as $ka => $answer) {
                    $usage_count = $this->getAnswerUsage($answer['id'], $relation_subject);
                    $question['answer_list'][$ka]['usage_count'] = $usage_count;
                    if ($usage_count > $usage_count_max) $usage_count_max = $usage_count;
                }

                //calculate usage_scale (1 to 10)
                foreach ($question['answer_list'] as $ka => $answer) {

                    if ($usage_count_max > 0) $usage_scale = $answer['usage_count'] / $usage_count_max * 10;
                    else $usage_scale = 0;

                    $question['answer_list'][$ka]['usage_scale'] = round($usage_scale);
                    $question['answer_list'][$ka]['usage_scale_percentage'] = $usage_scale * 10;
                }
            }

            $survey_detail['question_list'][$kq] = $question;
        }

        return $survey_detail;

    }

    /**
     * getAnswersForQuestion
     */

    public function getAnswersForQuestion($question_id, $relation_subject = false)
    {

        if (!is_numeric($question_id)) return false;

        require_once('models/education/education_survey_entry.php');
        $SurveyEntry = new education_survey_entry();

        $list = $SurveyEntry->getAnswersForQuestion($question_id, $relation_subject);

        return $list;
    }

    /**
     * getAnswerUsage
     */

    public function getAnswerUsage($question_answer_id, $relation_subject = false)
    {

        if (!is_numeric($question_answer_id)) return false;

        require_once('models/education/education_survey_entry.php');
        $SurveyEntry = new education_survey_entry();

        $usage_count = $SurveyEntry->getAnswerUsageCount($question_answer_id, $relation_subject);

        return $usage_count;
    }

    /**
     * getRelationSubjects
     */

    public function getUsedRelationSubjectList($survey_id)
    {

        if (!is_numeric($survey_id)) return false;

        $sql = 'SELECT DISTINCT relation_subject from education_survey_entry WHERE survey_id = $survey_id ORDER BY relation_subject';

        $records = $this->executeSql($sql);

        if (is_array($records)) {
            $list = [];
            foreach ($records as $item) {
                $list[] = $item['relation_subject'];
            }
            return $list;
        } else return false;

    }

    /**
     * get all results
     */

    public function getAllResults($survey_id, $relation_subject = false)
    {

        if (!is_numeric($survey_id)) return false;

        if ($relation_subject) $relation_subject_condition = " AND education_survey_entry.relation_subject LIKE '{$relation_subject}'";
        else $relation_subject_condition = '';

        $sql = "SELECT education_survey_entry.relation_subject, education_survey_question.id AS question_id, avg(education_survey_question_answer.points) AS average_rating,
count(DISTINCT education_survey_entry.customer_id)
FROM education_survey_entry
        LEFT OUTER JOIN education_survey_entry_answer ON (education_survey_entry_answer.survey_entry_id = education_survey_entry.id)
        LEFT OUTER JOIN education_survey_question_answer ON (education_survey_question_answer.id = education_survey_entry_answer.question_answer_id)
LEFT OUTER JOIN education_survey_question ON (education_survey_question.id = education_survey_entry_answer.question_id)
        WHERE education_survey_entry.survey_id = $survey_id AND education_survey_entry.publish = 1
        $relation_subject_condition
GROUP BY education_survey_entry.relation_subject, education_survey_question.id
ORDER BY education_survey_entry.relation_subject, education_survey_question.id";

        $records = $this->executeSql($sql);

        if (is_array($records)) {
            return $records;
        } else return false;
    }

}
