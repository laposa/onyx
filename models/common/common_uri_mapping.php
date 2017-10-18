<?php
/**
 * class common_uri_mapping
 *
 * Copyright (c) 2009-2017 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class common_uri_mapping extends Onxshop_Model {

    /**
     * @access private
     */
    var $id;
    /**
     * 
     * @access private
     */
    var $node_id;
    /**
     * @access private
     * path with leading slash, but without closing slash
     * e.g. /example or /example.html
     * not /example/
     */
    var $public_uri;
    
    var $type;
    
    var $_metaData = array(
        'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
        'node_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
        'public_uri'=>array('label' => 'Public URI', 'validation'=>'string', 'required'=>true),
        'type'=>array('label' => 'Type', 'validation'=>'string', 'required'=>true)
        );
    
    /**
     * create table sql
     */
     
    private function getCreateTableSql() {
    
        $sql = "
CREATE TABLE common_uri_mapping (
    id serial NOT NULL PRIMARY KEY,
    node_id integer REFERENCES common_node ON UPDATE CASCADE ON DELETE CASCADE,
    public_uri text,
    \"type\" character varying(255)
);
ALTER TABLE common_uri_mapping ADD UNIQUE (public_uri);
        ";
        
        return $sql;
    }
    
    /**
     * init configuration
     */
     
    static function initConfiguration() {
    
        if (array_key_exists('common_uri_mapping', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['common_uri_mapping'];
        else $conf = array();
    
        require_once('models/common/common_node.php');
        $node_conf = common_node::initConfiguration();

        /**
         * default settings
         */
         
        if (!array_key_exists('homepage_id', $conf)) $conf['homepage_id'] = $node_conf['id_map-homepage'];
        if (!array_key_exists('404_id', $conf)) $conf['404_id'] = $node_conf['id_map-404'];
        
        if (!array_key_exists('seo', $conf)) $conf['seo'] = true;
        if (!array_key_exists('rewrite_home', $conf)) $conf['rewrite_home'] = true;
        if (!array_key_exists('delimiter', $conf)) $conf['delimiter'] = '/';
        if (!array_key_exists('append', $conf)) $conf['append'] = '';
        if (!array_key_exists('hash', $conf)) $conf['hash'] = false;
        if (!array_key_exists('and_string', $conf)) $conf['and_string'] = I18N_AND;

        return $conf;
    }
    
    /**
     * Constructor
     * 
     */
        
    function __construct() {
    
        $this->_class_name = get_class($this);
        $this->generic();
        
        $this->_rewrite_table = $this->getGenericURITable();
    }


    /**
     * get list
     */
     
    public function getList() {
    
        $records = $this->listing('', 'public_uri ASC');
        
        return $records;
        
    }
    
    /**
     * get detailed list
     */
     
    public function getDetailList() {
    
        $sql = 'SELECT * FROM common_uri_mapping mapping
        LEFT OUTER JOIN common_node node ON (node.id = mapping.node_id)
        ORDER BY public_uri ASC';
        
        $records = $this->executeSql($sql);
        
        return $records;
        
    }
    
    /**
     * system links to public translation
     */
     
    function system_uri2public_uri($html) {
    
        // first to the nice
        $html = $this->to_cms_url($html);
        
        // second to the SEO nice
        if ($this->conf['seo'] == true) $html = $this->to_seo_url($html);
        
        return $html;
    }
    
    /**
     * translation into cms urls
     */
     
    function to_cms_url($html) {
    
        $html = preg_replace("/href=[\"\'](?!JavaScript)(?!http)\/index.php\?request=" . addcslashes(ONXSHOP_DEFAULT_LAYOUT, '/') . ".page~id=([^\~]*)~[\"\']/i", "href=\"/page/\\1\"", $html);
        //fix home URI
        if ($this->conf['rewrite_home']) $html = preg_replace("/href=[\"\'](?!JavaScript)(?!http)\/page\/".$this->conf['homepage_id'].'"/', "href=\"/\"", $html);
        
        return $html;
    }
    
    /**
     * translation into seo urls
     */
     
    function to_seo_url($html) {
    
        //$html = preg_replace_callback("/([(href)(value)(action)]{1})=[\"\'](?!JavaScript)(?!http)\/page\/([0-9]*)[\"\']/i", array($this, '_replace'), $html);
        //$html = preg_replace_callback("/(href|value|action)=[\"\'](?!JavaScript)(?!http)\/page\/([0-9]*)(\?[^\"\'.]*)[\"\']/i", array($this, '_replace'), $html);
        $html = preg_replace_callback("/(href|value|action)=([\"\'])(?!JavaScript)(?!http)\/page\/([0-9]*)/i", array($this, '_replace'), $html);
        
        return $html;
    }
    
    /**
     * string to seo
     */
     
    function stringToSeoUrl($string) {

        $id = preg_replace("/\/page\//", '', $string);
        
        if ($id == $this->conf['homepage_id']) $seo_url = "/";
        else $seo_url = $this->_rewrite_table[$id];
        
        if ($seo_url != '') return $seo_url;
        else return $string; // seo_url doesn't exists
    }
    
    /**
     * translate
     * @param string $string
     * URL path, e.g. /contact-us
     *
     * @returns int
     * page node_id
     */
     
    function translate($string) {
        
        if (is_numeric($node_id = trim($string, '/'))) {
            
            /**
             * /1234
             * will do redirect
             */
            
            if ($full_url = $this->_rewrite_table[$node_id]) {
                
            }
            
        } else if (preg_match('/^\/page/', $string)) {
                    
            /**
             * page
             */
             
             // workaround to translate what's already translated :), eg /page/121
            if ($u == '') $u = $string;
            else $u = "/page/$u";
            
            $node_id = false;
        
            
        } else {
            
            /**
             * the rest
             * search for URL string
             */
             
            if (is_array($this->_rewrite_table)) $node_id = array_search($string, $this->_rewrite_table);
        
        }
        
        return $node_id;
    }
    
    /**
     * get node id from seo url
     */
    
    function getNodeIdFromSeoUri($seo_uri) {
        
        if (trim($seo_uri) == '') return false;
        
        if ($seo_uri == '/') return $this->conf['homepage_id'];
        
        if (!$this->isValidURIPath($seo_uri)) return false;
        
        $result = $this->listing("public_uri = '$seo_uri'");
        
        if (count($result) > 0) return $result['0']['node_id'];
        else return false;
    }
    
    /**
     * replace
     */
     
    function _replace($matches) {
    
        if (is_array($this->_rewrite_table)) {
        
            if (array_key_exists($matches[3],  $this->_rewrite_table)) {
                $replace_string = $this->_rewrite_table[$matches[3]];
            } else {
                $replace_string = "/page/{$matches[3]}";
            }
        
            return "{$matches[1]}={$matches[2]}{$replace_string}";
        
        } else {
        
            msg('common_uri_mapping:_replace: rewrite table is not an array', 'error', 2);
            return "{$matches[1]}=\"/page/{$matches[3]}\"";
        
        }
    }
    
    /**
     * get generic uri table
     */
     
    function getGenericURITable($update = 0) {
    
        if ($update == 1) {
            
            $rewrite_table = $this->generateAndSaveURITable();
            
        } else {
        
            //get from database
            $rt = $this->listing("type = 'generic'");
            foreach ($rt as $r) {
                $rewrite_table[$r['node_id']] = $r['public_uri'];
            }
        
        }
        
        return $rewrite_table;
    }
    
    /**
     * generateAndSaveURITable
     * 
     * @return array
     * rewrite table
     */
    
    public function generateAndSaveURITable() {
                
        //creating rewrite table
        $rewrite_table = $this->generateURITable();


        if ($rewrite_table) {

            //delete old one
            $this->deleteURIMapping();
        
            //insert in the DB
            foreach ($rewrite_table as $key=>$val) {
                $item['node_id'] = $key;
                $item['public_uri'] = $val;
                $item['type'] = 'generic';
                $this->insert($item);
            }
            
            msg("URI table has been generated");
            
            return $rewrite_table;
        
        } else {
            
            return false;
            
        }
        
    }

    /**
     * generate uri table
     */
     
    function generateURITable() {
    
        require_once('models/common/common_node.php');
        $Node = new common_node();
        
        $nodes = $Node->listing("node_group = 'page'");
        $rewrite_table = array();

        foreach ($nodes as $p) {
            $rewrite_table[$p['id']] = $this->generateSingleURIFullPath($p);
        }
        
        return $rewrite_table;
        
    }
    
    /** 
     * generate single
     */
     
    function generateSingleURIFullPath($node_data) {
    
            require_once('models/common/common_node.php');
            $Node = new common_node();
        
            $fp = $Node->getFullPathDetail($node_data['id']);
            
            $fullpath = "";
            
            foreach ($fp as $f) {
                if ($f['node_group'] == 'page') {
                
                    if ($f['uri_title'] != '') $title = $this->cleanTitle($f['uri_title']);
                    else $title = $this->cleanTitle($f['title']);
                    
                    // for blog/news pages prepend date
                    if ($f['node_controller'] == 'news') {
                        $title = preg_replace("/-/", "/", substr($f['created'], 0, 10)) . "/$title";
                    }
                    
                    if ($fullpath == '') $fullpath =  $title . $fullpath;
                    else $fullpath =  $title . str_replace('/', $this->conf['delimiter'], $fullpath);
                    
                    if ($this->conf['hash'] == true) $fullpath = '/' . sprintf('%08X', crc32($fullpath));
                    else $fullpath = '/' . $fullpath;
                }
                if (!preg_match("/" . $this->conf['append'] . "$/", $fullpath)) $fullpath  = $fullpath . $this->conf['append'];
            }
        
            return $fullpath;
    }
    
    /**
     * update single
     */
     
    function updateSingle($node_data) {
    
        if ($node_data['node_group'] != 'page') return false;
        
        $list = $this->listing("node_id = {$node_data['id']} AND type = 'generic'");
        
        if (!is_array($list[0]) || !is_numeric($list[0]['id'])) return false;
        
        $item = $this->detail($list[0]['id']);
        
        $old_uri = $item['public_uri'];
        $item['public_uri'] = $this->generateSingleURIFullPath($node_data);
        
        if ($old_uri != $item['public_uri']) {
        
            if ($this->update($item)) {
        
                $sql = "UPDATE common_uri_mapping SET public_uri = regexp_replace(public_uri, '{$old_uri}/', '{$item['public_uri']}/') WHERE id != {$item['id']};";
                $this->executeSql($sql);
                
                return true;
        
            } else {
        
                return false;
        
            }
        
        } else {
        
            return true;
        
        }
    }
    
    /**
     * insert new path
     */
     
    function insertNewPath($node_data) {
    
        $fullpath = $this->generateSingleURIFullPath($node_data);
        
        $item['node_id'] = $node_data['id'];
        $item['public_uri'] = $fullpath;
        $item['type'] = 'generic';
        
        if ($id = $this->insert($item)) return $id;
        else return false;
    }
    
    /**
     * clean title
     */
    
    function cleanTitle($title) {
    
        $title = str_replace('/', '-', trim($title));
        $title = $this->recodeUTF8ToAscii($title);
        $title = strtolower($title);
        $title = preg_replace("/\s/", "-", $title);
        $title = preg_replace("/&[^([a-zA-Z;)]/", $this->conf['and_string'] . '-', $title);
        $title = preg_replace("/[^\w-\/\.]/", '', $title);
        $title = preg_replace("/\-{2,}/", '-', $title);
        $title = trim($title, '-'); 
        
        return $title;
    }
    
    /**
     * get request
     */
     
    function getRequest($node_id) { 
        
        require_once('models/common/common_node.php');
        $Node = new common_node();
        
        if ($Node->detail($node_id)) {
            $append = ".node~id=$node_id~";
            if ($node_id == $this->conf['404_id']) $append = "{$append}.sys/404";
        } else {
            $append = ".node~id=" . $this->conf['404_id'] . "~.sys/404";
        }
        
        if (Onxshop_Bo_Authentication::getInstance()->isAuthenticated()) {
            //hack to pass _SESSION.fe_edit_mode even before it's called again from fe_edit
            //consider moving this to $Bootstrap->initPreAction
            //probably this whole block, _GET shouldn't be here!
            $_Onxshop_Request = new Onxshop_Request('bo/component/fe_edit_mode');   
            $request = ONXSHOP_DEFAULT_TYPE . "~id=$node_id~.bo/fe_edit~id=$node_id~." . ONXSHOP_MAIN_TEMPLATE . "~id=$node_id~$append";
        } else {
            $request = ONXSHOP_DEFAULT_LAYOUT . "~id=$node_id~" . "$append";
        }
        
        return $request;
    }

    /**
     * delete mapping
     */
     
    function deleteURIMapping($type = 'generic') {
    
        switch ($type) {
            case 'all':
                $this->deleteAll();
                break;
            case 'generic':
                $this->deleteAll("type = 'generic'");
                break;
        }
    }
    
    /**
     * list redirect uri
     */
     
    function listRedirectURIRecords() {
        
        $records = $this->listing("type = '301'");
        
        if (is_array($records)) {
            return $records;
        } else {
            return false;
        }
    }
    
    /**
     * get redirect uri
     */
     
    function getRedirectURI($uri) {
        
        if (!$this->isValidURIPath($uri)) return false;
        
        $records = $this->listing("type = '301' AND public_uri = '$uri'");

        if (is_array($records) && count($records) > 0) return $records[0];
        else return false;
    }

    /**
     * recodeUTF8ToAscii
     */
    
    function recodeUTF8ToAscii($string) {
        return recodeUTF8ToAscii($string);
    }
    
    /**
     * validate URI path
     */
     
    public function isValidURIPath($uri_path) {
        
        if (preg_match('/[^a-zA-Z0-9\._\-\/]/', $uri_path)) {
            
            msg("Not a valid URI path ({$uri_path})", 'error', 1);
            return false;
            
        } else {
        
            return true;
        
        }
    }
    
}
