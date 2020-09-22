<?php
/**
 * class international_translation
 *
 * Copyright (c) 2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class international_translation extends Onxshop_Model {

    /**
     * @access private
     */
    var $id;

    /**
     * @access private
     */
    var $locale;

    /**
     * @access private
     */
    var $original_string;

    /**
     * @access private
     */
    var $translated_string;

    /**
     * @access private
     */
    var $context;

    /**
     * @access private
     */
    var $node_id;


    var $_metaData = array(
        'id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
        'locale'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'original_string'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'translated_string'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'context'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'node_id'=>array('label' => '', 'validation'=>'int', 'required'=>false)
    );

    /**
     * create table sql
     */

    private function getCreateTableSql() {

        $sql = "CREATE TABLE international_translation (
            id serial NOT NULL PRIMARY KEY,
            locale character varying(20) NOT NULL,
            original_string text NOT NULL,
            translated_string text NOT NULL,
            context character varying(63),
            node_id integer REFERENCES common_node ON UPDATE CASCADE ON DELETE CASCADE
        );

        CREATE INDEX international_translation_locale_idx ON international_translation USING btree (locale);
        CREATE INDEX international_translation_node_id_idx ON international_translation USING btree (node_id);
        ";

        return $sql;
    }

    /**
     * init configuration
     */

    static function initConfiguration() {

        if (array_key_exists('international_translation', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['international_translation'];
        else $conf = array();

        $conf['default'] = GLOBAL_DEFAULT_CURRENCY;

        if (array_key_exists('allowed', $conf)) {
            $conf['allowed'] = explode(',', $conf['allowed']);
        } else {
            $conf['allowed'] = array(GLOBAL_DEFAULT_CURRENCY);
            //$conf['allowed'] = array('all');
        }

        return $conf;
    }

    /**
     * Translate single word/phrase
     */
    public function translatePhrase($original_string, $translated_string, $context, &$content)
    {
        $original_string = preg_quote($original_string, "/");
        $replacement = "\${1}{$translated_string}\${2}";

        switch ($context) {
            case "separate_text":
                $pattern = "/(\W)$original_string(\W)/us";
                break;

            case "text_inside_tag":
                $pattern = "/(>)$original_string(<)/us";
                break;

            case "text_inside_span_tag":
                $pattern = "/(<span[^>]*>)$original_string(<\/span>)/us";
                break;

            case "navigation_label":
                $pattern = "/(<div id=\\\"(?:primary|secondary|global)Navigation\\\">.*?<ul>.*?<span>)$original_string(<\/span>.*?<\/ul>)/us";
                break;

            case "navigation_url":
                $pattern = "/(<div id=\\\"(?:primary|secondary|global)Navigation\\\">.*?<ul>.*?\href=\\\")$original_string(\\\".*?<\/ul>)/us";
                break;

            case "tag_attribute":
                $pattern = "/(\w+=\\\")$original_string(\\\")/u";
                break;

            case "link_label":
                $pattern = "/(<a [^>]+>)(?:<span>)?$original_string(?:<\/span>)?(<\/a>)/us";
                break;

            case "link_url":
                $pattern = "/(href=\\\")$original_string(\\\")/u";
                break;

            case "heading_title":
                $pattern = "/(<h[1-6] [^>]+>)(?:<span>)?$original_string(?:<\/span>)?(<\/h[1-6]>)/us";
                break;

            default:
                $pattern = "/(.*)$original_string(.*)/";

        }
        
        $translation = preg_replace($pattern, $replacement, $content);

        if (preg_last_error() == PREG_NO_ERROR) return $translation;

        if (preg_last_error() == PREG_INTERNAL_ERROR) msg('There is an internal error!', 'error', 1);
        if (preg_last_error() == PREG_BACKTRACK_LIMIT_ERROR) msg('Backtrack limit was exhausted!', 'error', 1);
        if (preg_last_error() == PREG_RECURSION_LIMIT_ERROR) msg('Recursion limit was exhausted!', 'error', 1);
        if (preg_last_error() == PREG_BAD_UTF8_ERROR) msg('Bad UTF8 error!', 'error', 1);
        if (preg_last_error() == PREG_BAD_UTF8_ERROR) msg('Bad UTF8 offset error!', 'error', 1);

        return $content;
    }

    /**
     * Load relevant parts of the dictionary
     */
    public function loadDictionary($locale, $node_id)
    {
        $locale = $this->db->quote($locale);
        $where = "locale = $locale";
        if (is_numeric($node_id) && $node_id > 0) $where .= " AND (node_id = $node_id OR node_id IS NULL)";
        else $where .= " AND node_id IS NULL";
        return $this->listing($where, "char_length(original_string)");
    }

    /**
     * Translate page
     * @param  string $content  Page to translate
     * @param  string $locale   Locale the page to be trasnlated to
     * @param  int    $node_id  Page node_id for context aware translation
     * @return string
     */
    public function translatePage($content, $locale, $node_id)
    {
        $dictionary = $this->loadDictionary($locale, $node_id);

        foreach ($dictionary as $item) {

            $content = $this->translatePhrase(
                $item['original_string'], 
                $item['translated_string'], 
                $item['context'], 
                $content
            );

        }

        return $content;
    }

    /**
     * temporary implementation (will be in general model in future)
     */
    
    function updateSingleAttribute($attribute, $update_value, $id) {

        // validation
        if (!in_array($attribute, array("locale", "original_string", "translated_string", "context", "node_id"))) return false;
        if ($attribute == "node_id" && !is_numeric($update_value)) $update_value = null;

        $data = $this->detail($id);

        if (is_array($data)) {

            $data[$attribute] = $update_value;
            if ($this->update($data)) return true;
            else return false;

        }

    }
}
