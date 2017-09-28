<?php
/**
 *
 * Copyright (c) 2011-2013 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class education_survey_entry_answer extends Onxshop_Model {

    /**
     * PRIMARY KEY
     *
     */
    public $id;
    
    /**
     * survey_entry_id
     */
    public $survey_entry_id;
    
    /**
     * question_id
     */
    public $question_id;
    
    /**
     * question_answer_id
     * this can be null for question type text
     */
    public $question_answer_id;
    
    /**
     * value
     * this is for question type text
     */
    public $value;
    
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
     
    public $_metaData = array(
        'id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
        'survey_entry_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
        'question_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
        'question_answer_id'=>array('label' => '', 'validation'=>'int', 'required'=>false), //this is required for radio, select and checkbox questions
        'value'=>array('label' => '', 'validation'=>'string', 'required'=>false), //this is required for text type questions
        'created'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
        'modified'=>array('label' => '', 'validation'=>'datetime', 'required'=>false),
        'publish'=>array('label' => '', 'validation'=>'int', 'required'=>false)
    );
    
    /**
     * create table sql
     */
     
    private function getCreateTableSql() {
    
        $sql = "
CREATE TABLE education_survey_entry_answer (
    id serial PRIMARY KEY NOT NULL,
    survey_entry_id int NOT NULL REFERENCES education_survey_entry ON UPDATE CASCADE ON DELETE CASCADE,
    question_id int NOT NULL REFERENCES education_survey_question ON UPDATE CASCADE ON DELETE RESTRICT,
    question_answer_id int REFERENCES education_survey_question_answer ON UPDATE CASCADE ON DELETE RESTRICT,
    value text,
    created timestamp(0) without time zone DEFAULT now() NOT NULL,
    modified timestamp(0) without time zone DEFAULT now(),
    publish smallint DEFAULT 0
);
        ";
        
        return $sql;
    }
    
    /**
     * getAnswersForQuestion
     */
     
    public function getAnswersForQuestion($question_id, $relation_subject = false) {
        
        if (!is_numeric($question_id)) return false;
        
        if ($relation_subject) $sql = "
SELECT education_survey_entry_answer.* FROM education_survey_entry 
LEFT OUTER JOIN education_survey_entry_answer ON (education_survey_entry.id = education_survey_entry_answer.survey_entry_id)
WHERE education_survey_entry.relation_subject LIKE '{$relation_subject}' AND education_survey_entry_answer.question_id = {$question_id};";
        else $sql = "SELECT * FROM education_survey_entry_answer WHERE question_id = {$question_id}";
        
        $list = $this->executeSql($sql);
        
        return $list;
    }
    
    /**
     * getAnswerUsageCount
     */
     
    public function getAnswerUsageCount($question_answer_id, $relation_subject = false) {
    
        if (!is_numeric($question_answer_id)) return false;
        
        if ($relation_subject) $sql = "
SELECT education_survey_entry_answer.* FROM education_survey_entry 
LEFT OUTER JOIN education_survey_entry_answer ON (education_survey_entry.id = education_survey_entry_answer.survey_entry_id)
WHERE education_survey_entry.relation_subject LIKE '{$relation_subject}' AND education_survey_entry_answer.question_answer_id = {$question_answer_id};";
        else $sql = "SELECT * FROM education_survey_entry_answer WHERE question_answer_id = {$question_answer_id}";
        
        $list = $this->executeSql($sql);

        $usage_count = count($list);
        
        return $usage_count;
    }

    /**
     * saveAnswer
     */
    
    public function saveAnswer($data, $file = false) {
    
        if (!is_array($data)) {
            msg("survey_entry_answer: data is not array", 'error');
            return false;
        }
        
        $data['created'] = date('c');
        
        $id = $this->save($data);
        
        if (is_numeric($id)) {
        
            // Save file (if provided)
            if ($file) $this->saveFile($file, $data['survey_entry_id'], $data['question_id'], $id);
            
            return $id;
            
        } else {
            
            msg("Cannot save Question {$data['question_id']}", 'error');
            return false;
            
        }
    }
    
    /**
     * saveFile
     */
     
    public function saveFile($file_single, $survey_entry_id, $question_id, $answer_id) {
        
        // find survey_id by $survey_entry_id
        require_once('models/education/education_survey_entry.php');
        $Survey_Entry = new education_survey_entry();
        $survey_entry_data = $Survey_Entry->detail($survey_entry_id);
        $survey_id = $survey_entry_data['survey_id'];

        /**
         * add prefix to filename (rename)
         */
         
        $file_single['name'] = $this->getFilenameToSave($file_single['name'], $survey_entry_id, $question_id, $answer_id);
        
        /**
         * file
         */
         
        require_once('models/common/common_file.php');
        //getSingleUpload could be a static method
        $CommonFile = new common_file();
        $upload = $CommonFile->getSingleUpload($file_single, "var/surveys/$survey_id/");
        
        /**
         * array indicated the same file name already exists in the var/tmp/ folder
         * this should never happen as we have entry id in filename 
         */
         
        if (is_array($upload)) {
        
            $attachment_saved_file = ONXSHOP_PROJECT_DIR . $upload['temp_file'];
        
        } else {
        
            $attachment_saved_file = ONXSHOP_PROJECT_DIR . $upload;
        
        }
        
        /**
         * check if file exists and than return filename
         */
         
        if (file_exists($attachment_saved_file)) {
            $attachment_info = $CommonFile->getFileInfo($attachment_saved_file);
            
            return $attachment_info['filename'];
        }
        
    }
    
    /**
     * getFilename
     */
     
    public function getFilenameToSave($file_name, $survey_entry_id, $question_id, $answer_id) {
        
        if (!$file_name) return false;
        if (!is_numeric($survey_entry_id)) return false;
        if (!is_numeric($question_id)) return false;
        if (!is_numeric($answer_id)) return false;
        
        require_once('models/common/common_file.php');
        $file_name = common_file::nameToSafe($file_name);
        $file_name = "{$survey_entry_id}-{$question_id}-{$answer_id}-" . $file_name;
        
        return $file_name;
        
    }
}
