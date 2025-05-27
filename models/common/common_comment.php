<?php
/**
 * Copyright (c) 2009-2015 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class common_comment extends Onyx_Model {

    /**
     * PRIMARY KEY
     * @access private
     */
    var $id;
    /**
     * NOT NULL REFERENCES common_comment ON UPDATE CASCADE ON DELETE CASCADE
     * @access private
     */
    var $parent;
    /**
     * NOT NULL REFERENCES common_node ON UPDATE CASCADE ON DELETE RESTRICT
     * @access private
     */
    var $node_id;
    /**
     * @access private
     */
    var $title;
    /**
     * @access private
     */
    var $content;
    /**
     * @access private
     */
    var $author_name;
    /**
     * @access private
     */
    var $author_email;
    /**
     * @access private
     */
    var $author_website;

    var $author_ip_address;
    /**
     * NOT NULL REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT
     * @access private
     */
    var $customer_id;
    /**
     * @access private
     */
    var $created;
    /**
     * @access private
     */
    var $publish;
    
    var $rating;
    
    var $relation_subject;

    var $_metaData = array(
        'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
        'parent'=>array('label' => '', 'validation'=>'int', 'required'=>false),
        'node_id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
        'title'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'content'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'author_name'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'author_email'=>array('label' => '', 'validation'=>'email', 'required'=>false),
        'author_website'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'author_ip_address'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'customer_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
        'created'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
        'publish'=>array('label' => '', 'validation'=>'int', 'required'=>true),
        'rating'=>array('label' => '', 'validation'=>'int', 'required'=>false),
        'relation_subject'=>array('label' => '', 'validation'=>'text', 'required'=>false)
    );
    
    /**
     * create table sql
     * 
     * @return string
     * SQL command for table creating
     */
     
    private function getCreateTableSql() {
    
        $sql = "
CREATE TABLE common_comment ( 
    id serial PRIMARY KEY NOT NULL,
    parent int REFERENCES common_comment ON UPDATE CASCADE ON DELETE CASCADE,
    node_id int REFERENCES common_node ON UPDATE CASCADE ON DELETE RESTRICT,
    title varchar(255) ,
    content text ,
    author_name varchar(255) ,
    author_email varchar(255) ,
    author_website varchar(255) ,
    author_ip_address varchar(255),
    customer_id int NOT NULL REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT,
    created timestamp(0) default now(),
    publish smallint,
    rating default 0,
    relation_subject text
);
CREATE INDEX common_comment_node_id_key1 ON common_comment USING btree (node_id);
        ";
        
        return $sql;
    }
    
    /**
     * get comments tree
     * 
     * @param integer $node_id
     * ID of node for comments
     * 
     * @param integer $public
     * only published (1) or also unpublished (0) comments
     * 
     * @param string $sort
     * sorting direction ['ASC'/'DESC']
     * 
     * @return array
     * comments
     */
    
    function getTree($node_id, $public = 1, $sort = 'ASC') {
        
        $sql = "SELECT id, parent, title as name, title as title, content, author_name, author_email, author_website, author_ip_address, customer_id, created, rating, relation_subject FROM common_comment WHERE publish >= $public AND node_id='$node_id' ORDER BY parent, created $sort";
        
        $records = $this->executeSql($sql);
        
        return $records;
    }
    
    /**
     * get detail
     * 
     * @param integer $id
     * comment ID
     * 
     * @return array
     * comment informations
     */
    
    public function getDetail($id) {
    
        if (!is_numeric($id)) {
            msg("common_comment.getDetail: id is not numeric", 'error');
            return false;
        }
        
        $data = $this->detail($id);
        
        return $data;
    }
    
    /**
     * list
     * 
     * @param array $filter
     * comments filter with any of keys node_id, relation_subject, customer_id and parent
     * 
     * @param string $sort
     * sorting direction ['ASC'/'DESC']
     * 
     * @param string $limit
     * limit
     * 
     * @return array
     * comments list or false
     */
     
    function getCommentList($filter = false, $sort = 'id ASC', $limit = '')
    {
        $where = $this->prepareWhereForListing($filter);
        return $this->listing($where, $sort, $limit);
    }
        
    
        /**
     * get number of items within given filter
     * 
     * @param array $filter
     * comments filter with any of keys node_id, relation_subject, customer_id and parent
     * 
     * @return int
     * number of comments
     */
     
    function getCommentCount($filter = false)
    {
        $where = $this->prepareWhereForListing($filter);
        return $this->count($where);
    }



    /**
     * Prepare SQL query for searching
     * 
     * @param array $filter
     * comments filter with any of keys node_id, relation_subject, customer_id and parent
     * 
     * @return string
     * SQL query
     */
     
    protected function prepareWhereForListing($filter = false)
    {
        $add_to_where = '1 = 1 ';
    
        /**
         * query filter
         * 
         */

        if (is_array($filter)) {
            if (is_numeric($filter['node_id'])) {
                $add_to_where .= "AND node_id = '{$filter['node_id']}' ";
            }
            
            if (!empty($filter['query'])) {
                $query = explode(" ", trim($filter['query']));
                $query_where = "1 = 0 ";
                foreach ($query as $item) {
                    $item = $this->db->quote("%" . trim($item) . "%");
                    $query_where .= "OR title ILIKE $item OR content ILIKE $item OR relation_subject ILIKE $item OR author_name ILIKE $item OR author_email ILIKE $item ";
                }
                $add_to_where .= " AND ($query_where)";
            }
            
            if ($filter['relation_subject']) {
                $add_to_where .= " AND relation_subject LIKE '{$filter['relation_subject']}' ";
            }
            
            if (is_numeric($filter['parent'] ?? null)) {
                $add_to_where .= " AND parent = '{$filter['parent']}' ";
            } else if (array_key_exists('parent', $filter) && $filter['parent'] === null) {
                $add_to_where .= " AND parent IS NULL ";
            }

            if (is_numeric($filter['customer_id'] ?? null)) {
                $add_to_where .= " AND customer_id = '{$filter['customer_id']}' ";
            }

        }
         
        return $add_to_where;
    }
    
    
    /**
     * getCommentsForNodeId
     *
     * @param int $node_id
     * reference to foreign key
     *
     * @param int $public_only
     * change to 0 to list all comments (including rejected)
     *
     * @param string $sort
     * SQL order by
     * 
     * @return integer
     * saved comment ID or false if save failed
     */
     
    public function getCommentsForNodeId($node_id, $public_only = 1, $sort = 'id ASC') {
        
        if (!is_numeric($node_id)) return false;
        
        $add_to_where = "node_id = $node_id AND content IS NOT NULL AND content != ''";
        
        if ($public_only === 1) $add_to_where .= " AND publish = 1";
        
        /**
         * get list
         */
         
        $list = $this->listing($add_to_where, $sort);
        
        return $list;
    }
    
    /**
     * insert comment
     * 
     * @param array $data
     * comment informations for save
     * 
     * @return integer
     * saved comment ID or false if save failed
     */

    function insertComment($data) {
    
        //retype null values
        if ($data['parent'] == 0) $data['parent'] = null;
        if ($data['node_id'] == 0) $data['node_id'] = null;
        
        $data['created'] = date('c');

        if (!is_numeric($data['publish'])) $data['publish'] = 0;
        if (!is_numeric($data['rating'])) $data['rating'] = 0;
        $data['author_ip_address'] = $_SERVER['REMOTE_ADDR'];

        if ($id = $this->insert($data)) {
        
            $this->sendNewCommentNotificationEmail($id, $data);
            
            return $id;
            
        } else {
            msg("Cannot insert comment", "error");
            return false;
        }
    }
    
    /**
     * update comment
     * 
     * @param array $data
     * comment informations for save
     * 
     * @return integer
     * saved comment ID or false if save failed
     */

    function updateComment($data) {
    
        //retype null values
        if ($data['parent'] == 0) $data['parent'] = null;
        if ($data['node_id'] == 0) $data['node_id'] = null;

        if (!is_numeric($data['publish'])) $data['publish'] = 0;
        if (!is_numeric($data['rating'])) $data['rating'] = 0;

        if ($id = $this->update($data)) {
            return $id;
        } else {
            msg("Cannot update comment", "error");
            return false;
        }
    }
    
    /**
     * get customer detail
     * 
     * @param integer $id
     * customer ID
     * 
     * @return array
     * customer informations
     */
    
    function getCustomerDetail($id) {
        require_once('models/client/client_customer.php');
        $Customer = new client_customer();
        
        $data = $Customer->getClientData($id);
        
        return $data;
    }

    /**
     * notification email
     * 
     * @param integer $comment_id
     * ID of comment - not used
     * 
     * @param array $comment_data
     * information about comment
     */
     
    public function sendNewCommentNotificationEmail($comment_id, $comment_data) {
    
        require_once('models/common/common_email.php');
        $EmailForm = new common_email();
                
        //is passed as DATA array into the template at common_email->_format
        $GLOBALS['common_email']['comment'] = $comment_data;
        
        if (!$EmailForm->sendEmail('comment_notify')) {
            msg('New comment notification email sending failed.', 'error');
        }
        
    }
    
    /**
     * getRating
     */
    
    public function getRating($node_id) {
        
        if (!is_numeric($node_id)) return false;
        
        $sql = "SELECT count(review.id) AS count, avg(review.rating) AS rating FROM {$this->_class_name} review WHERE node_id = $node_id AND publish = 1";
        
        $records = $this->executeSql($sql);
        
        if (is_array($records)) {
            
            $review = $records[0];
            
            if (is_array($review)) return $review;
            else return false;
        
        } else {
            
            return false;
        }
        
    }
    
    /**
     * insertRatingWithoutReview
     * 
     * @param int $node_id
     * @param int $rating
     * 
     * @return integer saved comment ID or false if save failed
     */

    function insertNodeRatingWithoutReview($node_id, $rating) {
    
        if (!is_numeric($node_id)) return false;
        if (!is_numeric($rating)) return false;
        
        $data = array();
        $data['node_id'] = $node_id;
        $data['rating'] = $rating;
        $data['created'] = date('c');
        $data['publish'] = 1;
        $data['author_ip_address'] = $_SERVER['REMOTE_ADDR'];
        if (is_numeric($_SESSION['client']['customer']['id'])) $data['customer_id'] = $_SESSION['client']['customer']['id'];
        else $data['customer_id'] = 0; // anonymous user
        
        if ($id = $this->insert($data)) {
            
            return $id;
            
        } else {
            
            msg("Cannot insert rating", "error");
            return false;
            
        }
    }

    /**
     * Return all node ids that were used to add a comment
     */
    function getUsedNodes() {
        $sql = "SELECT distinct(node_id) AS node_id, common_node.title FROM common_comment
            LEFT JOIN common_node ON common_node.id = node_id
            ORDER BY common_node.title ASC";
        $records = $this->executeSql($sql);
        return $records;
    }

}
