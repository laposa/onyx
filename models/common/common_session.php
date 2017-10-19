<?php
/**
 * class common_session
 *
 * inspired by article By Tony Marston
 * http://www.developertutorials.com/tutorials/php/saving-php-session-data-database-050711/page2.html
 *
 * Copyright (c) 2009-2017 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class common_session extends Onxshop_Model {

    /**
     * NOT NULL PRIMARY KEY
     * @access private
     */
    var $id;
    /**
     * session_id must be unique in this table (we should use UNIQUE constraint)
     * @access private
     */
    var $session_id;
    /**
     * @access private
     */
    var $session_data;
    /**
     * REFERENCES client_customer ON UPDATE CASCADE ON DELETE CASCADE
     * @access private
     */
    var $customer_id;
    /**
     * This is used to identify when the session was started.
     * @access private
     */
    var $created;
    /**
     * index
     * This is used to identify when the last request was processed for the session.
     * This is also used in garbage collection to remove those sessions which have been
     * inactive for a period of time.
     * @access private
     */
    var $modified;
    
    var $ip_address;
    
    var $php_auth_user;
    
    var $http_referer;

    var $http_user_agent;

    var $_metaData = array(
        'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
        'session_id'=>array('label' => '', 'validation'=>'string', 'required'=>true),
        'session_data'=>array('label' => '', 'validation'=>'string', 'required'=>true), 
        'customer_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
        'created'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
        'modified'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
        'ip_address'=>array('label' => '', 'validation'=>'string', 'required'=>true), 
        'php_auth_user'=>array('label' => '', 'validation'=>'string', 'required'=>false), 
        'http_referer'=>array('label' => '', 'validation'=>'string', 'required'=>false), 
        'http_user_agent'=>array('label' => '', 'validation'=>'string', 'required'=>false)
    );
    
    var $_cacheable = false;

    /**
     * create table sql
     */
     
    private function getCreateTableSql() {
    
        $sql = "
CREATE TABLE common_session ( 
    id serial NOT NULL PRIMARY KEY,
    session_id varchar(32) ,
    session_data text ,
    customer_id int REFERENCES client_customer ON UPDATE CASCADE ON DELETE CASCADE, 
    created timestamp(0) without time zone,
    modified timestamp(0) without time zone,
    ip_address varchar(255),
    php_auth_user varchar(255),
    http_referer text,
    http_user_agent varchar(255)
);
        ";
        
        return $sql;
    }
    
    /**
     * init configuration
     */
     
    static function initConfiguration() {
    
        //$conf = $GLOBALS['onxshop_conf']['common_session'];
        $conf = array();
        
        //default 1day
        $conf['ttl'] = 86400;
    
        return $conf;
    }
    
    /**
     *
     * @param string save_path   * @param string session_name    * @return 
     * @access public
     */
     
    function open( $save_path,  $session_name ) {
       // do nothing

        return true;

    }

    /**
     * The close() function is responsible for calling the gc() function to perform
     * garbage collection.
     *
     * @return 
     * @access public
     */
     
    function close( ) {
    
        if (!empty($this->fieldarray)) {

            // perform garbage collection on average after every 100 request
            if (mt_rand(1, 100) == 1) $result = $this->gc($this->conf['ttl']);
            else $result = true;
            
            return $result;

        }

        return false;

    }

    /**
     * The read() function is responsible for retrieving the data for the specified
     * session. Note that if there is no data it must return an empty string, not the
     * value NULL.
     *
     * @param string session_id      * @return 
     * @access public
     */
    
    function read( $session_id ) {

        $session_id = strtr($session_id, "./", "--");  // security measure
        $this->lock = fopen(ONXSHOP_PROJECT_DIR . "/var/sessions/$session_id.lock", 'w');
        flock($this->lock, LOCK_EX);

        $this->setCacheable(false);
    
        $fieldarray = $this->listing("session_id='$session_id'");

        

        if (isset($fieldarray[0]['session_data'])) {

            $this->fieldarray = $fieldarray[0];

            $this->fieldarray['session_data'] = '';

            return $fieldarray[0]['session_data'];

        } else {

            return '';  // return an empty string

        }
        
    }

    /**
     * The write() function is responsible for creating or updating the database with
     * the session data which is passed to it.
     *
     * @param string session_id      * @param string session_data    * @return 
     * @access public
     */
     
    function write( $session_id,  $session_data ) {
    
       if (!empty($this->fieldarray)) {

            if ($this->fieldarray['session_id'] != $session_id) {

                // user is starting a new session with previous data

                $this->fieldarray = array();

            }

        }

        if (empty($this->fieldarray)) {

            // create new record

            $array['session_id']   = $session_id;
            
            // TODO: check for size
            $array['session_data'] = $session_data;
            
            if (is_numeric($_SESSION['client']['customer']['id'])) $array['customer_id'] = $_SESSION['client']['customer']['id'];
            else $array['customer_id'] = 0;

            $array['created'] = date('c');

            $array['modified'] = date('c');
            
            $array['ip_address'] = $_SERVER['REMOTE_ADDR'];
            
            $array['php_auth_user'] = $_SERVER['PHP_AUTH_USER'];
            
            $array['http_referer'] = $_SERVER['HTTP_REFERER'];
            
            $array['http_user_agent'] = mb_substr($_SERVER['HTTP_USER_AGENT'], 0, 255);

            $this->insertSession($array);

        } else {

            // update existing record
            $array = $this->fieldarray;

            //if it's real user, than set customer_id, but don't overwrite it if user logged out
            if (isset($_SESSION['client']['customer']['id']) && $_SESSION['client']['customer']['id'] > 0) {
                $array['customer_id']  = $_SESSION['client']['customer']['id'];
            }

            $array['modified'] = date('c');
            
            //if it's real user, than set customer_id, but don't overwrite it if user logged out
            //TODO: on logout close session?
            if (isset($_SERVER['PHP_AUTH_USER'])) {
                $array['php_auth_user'] = $_SERVER['PHP_AUTH_USER'];
            }
            
            $array['session_data'] = $session_data;

            $this->update($array);

        }

        flock($this->lock, LOCK_UN);
        fclose($this->lock);

        return true;

    }


    /**
     * insert session to database
     */

    function insertSession($data) {
    
        $this->insert($data);
    }

    /**
     * If the session_destroy() function is issued in the code then this will be
     * responsible for deleting the session data from the database.
     *
     * @param string session_id      * @return 
     * @access public
     */
     
    function destroy( $session_id ) {
    
        $fieldarray['session_id'] = $session_id;

        $this->set('session_id', $fieldarray['session_id']);
        //$this->delete();

        return true;
    }

    /**
     * This is the garbage collection or "clean-up" function. Notice that the time
     * limit of 2 hours has been hard-coded. This means that any session record which
     * has not been modified within this time limit Will be deleted.
     *
     * @param int max_lifetime   * @return 
     * @access public
     */
     
    function gc( $max_lifetime ) {
    
        $real_now = time();

        $dt1 = $real_now - $max_lifetime;

        $dt2 = date('Y-m-d H:i:s', $dt1);
        
        //list expired session
        $expired = $this->listing("modified < '$dt2'");
        
        //insert expired session to common_session_archive
        require_once('models/common/common_session_archive.php');
        $Archive = new common_session_archive();
        foreach ($expired as $e) {
            $Archive->insertSession($e);
        }

        // delete locks older 2 hours
        $files = @glob(ONXSHOP_PROJECT_DIR . "/var/sessions/*.lock");
        foreach($files as $file) {
            if (is_file($file) && time() - filemtime($file) >= 2 * 60 * 60) @unlink($file);
        }
        
        //delete them from common_session table
        $q = "DELETE FROM common_session WHERE modified < '$dt2'";
        $this->executeSql($q);

        return true;

    }

    public function findAuthenticatedCustomers() {
        
        /*
            SELECT * FROM common_session WHERE session_data LIKE '%s:12:"authenticity";i:1;%'
        or
            SELECT * FROM common_session WHERE php_auth_user IS NOT NULL
        */
        
    }
    
    public function forceLogoutAllAuthenticatedCustomers() {
        
        /*
            UPDATE common_session SET session_id = 'disabled', php_auth_user = 'disabled' WHERE session_data LIKE '%s:12:"authenticity";i:1;%'
            
        */
    }
    
    /**
     * findLargeSessions
     */
     
    public function findLargeSessions($limit = 5) {
        
        if (!is_numeric($limit)) return false;
        
        $class_name = get_class($this);
        
        $sql = "SELECT *, max(octet_length(session_data)) AS data_size_in_bytes FROM $class_name GROUP BY id ORDER BY data_size_in_bytes DESC LIMIT $limit";
        
        return $this->executeSql($sql);
        
    }
}
