<?php
/**
 *
 * Copyright (c) 2011-2019 Onxshop Ltd (https://onxshop.com)
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
     * ip_adress
     */
    public $ip_adress;

    /**
     * session_id
     */
    public $session_id;
    
    /**
     * other_data
     */
    public $other_data;

    /**
     * hashMap
     */
     
    public $_metaData = array(
        'id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
        'survey_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
        'customer_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
        'relation_subject'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'created'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
        'modified'=>array('label' => '', 'validation'=>'datetime', 'required'=>false),
        'publish'=>array('label' => '', 'validation'=>'int', 'required'=>false),
        'ip_address'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'session_id'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'other_data'=>array('label' => '', 'validation'=>'serialized', 'required'=>false)
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
    ip_address character varying(255),
    session_id character varying(32),
    other_data  text
    UNIQUE (survey_id, customer_id, relation_subject)
);
        ";
        
        return $sql;
    }
    
    /**
     * getListForSurveyId
     */
     
    public function getListForSurveyId($survey_id) {
        
        if (!is_numeric($survey_id)) return false;
        
        return $this->listing("survey_id = $survey_id");
        
    }
    
    /**
     * getQuestionsAndAnswersForEntry
     */
     
    public function getQuestionsAndAnswersForEntry($entry_id) {
        
        if (!is_numeric($entry_id)) return false;
        
        $sql = "SELECT 
            education_survey_question.id AS question_id,
            education_survey_entry_answer.id AS entry_answer_id,
            education_survey_question.title AS question_title,
            education_survey_question_answer.title AS answer_title,
            education_survey_entry_answer.value AS answer_value
         FROM education_survey_entry 
         LEFT OUTER JOIN education_survey_entry_answer ON (education_survey_entry_answer.survey_entry_id = education_survey_entry.id)
         LEFT OUTER JOIN education_survey_question ON (education_survey_question.id = education_survey_entry_answer.question_id)
         LEFT OUTER JOIN education_survey_question_answer ON (education_survey_question_answer.id = education_survey_entry_answer.question_answer_id)
         WHERE education_survey_entry.id = $entry_id
         ORDER BY education_survey_question.priority DESC, education_survey_question.id ASC";

        $records = $this->executeSql($sql);
        
        return $records;
        
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
        
        if ($relation_subject) $where = "survey_id = {$survey_id} AND relation_subject LIKE '$relation_subject' AND publish = 1";
        else $where = "survey_id = {$survey_id} AND publish = 1";
        
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
        
        $data['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $data['session_id'] = session_id();

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
            $EntryAnswer = new education_survey_entry_answer();
            
            /**
             * save normal data
             */
             
            foreach ($data['answers'] as $answer) {
            
                $answer['survey_entry_id'] = $survey_entry_id;
                
                if (!$EntryAnswer->saveAnswer($answer)) {
                    msg("Error occured in saving " . print_r($answer, true));
                    $error_occured = true;
                }
            
            }
            
            /**
             * save files
             */
             
            $this->saveFiles($survey_entry_id);

        }
        
        //TODO
        //if ($error_occured) $this->delete($survey_entry_id);
        
        return $survey_entry_id;
        
    }
    
    /**
     * saveFiles
     */
     
    public function saveFiles($survey_entry_id) {
        
        /**
         * attachment(s) via upload
         */
         
        if (count($_FILES) > 0) {
        
            foreach ($_FILES as $key=>$file) {
                
                if (is_uploaded_file($file['tmp_name'])) {
                    
                    require_once('models/education/education_survey_entry_answer.php');
                    $EntryAnswer = new education_survey_entry_answer();
                    
                    $answer['question_id'] = str_replace('question-id-', '', $key);
                    $answer['survey_entry_id'] = $survey_entry_id;
                    $answer['value'] = $file['name'];
                    
                    if (!$EntryAnswer->saveAnswer($answer, $file)) {
                    
                        msg("Error occured in saving " . print_r($answer, true));
                        $error_occured = true;
                    
                    }
                    
                }
                
                
            }
        }
    }
    
    /**
     * getSurveyCustomerCount
     */
     
    public function getSurveyCustomerCount($survey_id = false, $relation_subject = false) {
        
        $add_to_where = 'education_survey_entry.publish = 1';
        
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
        
        $add_to_where = 'education_survey_entry.publish = 1 ';

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
    
        $add_to_where = 'education_survey_entry.publish = 1 ';
        
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
    
    /**
     * findPreviousEntry
     */
    
    public function findPreviousEntry($survey_entry_data) {
    
        if (!is_array($survey_entry_data)) return false;
        
        /**
         * find previous entry (list)
         */
         
        $previous_entries = $this->listing("survey_id = {$survey_entry_data['survey_id']} AND customer_id = {$survey_entry_data['customer_id']} AND relation_subject = '{$survey_entry_data['relation_subject']}'");
    
        /**
         * check if anything found
         */
            
        if (is_array($previous_entries) && count($previous_entries) == 1) {
        
            /**
             *  $previous_entries should be size of 1 due to database constraint, use only first found
             */
             
            $previous_entry_id = $previous_entries[0]['id'];
        
            /**
             * check ID is numeric
             */
                
            if (is_numeric($previous_entry_id)) {
                
                msg("Previous entry ID $previous_entry_id found");
                return $previous_entry_id;
                
            } else {
            
                msg("Previsous entry found, but ID is not numeric", 'error');
                return false;
                
            }
        
        } else {
        
            return false;
        
        }
    }
    
    /**
     * deleteEntry
     */
     
    public function deleteEntry($previous_entry_id) {
        
        if (!is_numeric($previous_entry_id)) return false;
        
        if ($this->delete($previous_entry_id)) {
        
            msg("Deleted old entry ID $previous_entry_id");
            return true;
        
        } else {
            
            msg("Cannot deleted old entry ID $previous_entry_id", 'error');
            return false;
        
        }
    }

    /**
     * Check previous entries for given IP adress
     */
     
    public function numEntriesForIpAddress($survey_id, $ip_address, $today = false) {

        if (!is_numeric($survey_id)) return false;
        $ip_address = pg_escape_string($ip_address);

        if ($today) $today_sql = " AND modified::date >= (CURRENT_DATE)::date AND modified::date < (CURRENT_DATE + INTERVAL '1 day')::date";
        else $today_sql = '';
        return $this->count("survey_id = $survey_id AND ip_address = '$ip_address'" . $today_sql);
    }

    /**
     * Check previous entries for given IP adress
     */
     
    public function numEntriesForSessionId($survey_id, $session_id, $today = false) {

        if (!is_numeric($survey_id)) return false;
        $session_id = pg_escape_string($session_id);

        if ($today) $today_sql = " AND modified::date >= (CURRENT_DATE)::date AND modified::date < (CURRENT_DATE + INTERVAL '1 day')::date";
        else $today_sql = '';
        return $this->count("survey_id = $survey_id AND session_id = '$session_id'" . $today_sql);
    }

}
