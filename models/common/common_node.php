<?php
/**
 * class common_node
 *
 * Copyright (c) 2009-2024 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class common_node extends Onyx_Model {

    /**
     * @access private
     */
    var $id;
    
    /**
     * @access private
     */
    var $title;
    
    var $node_group;
    
    var $node_controller;
    /**
     * @access private
     */
    var $parent;
    
    var $parent_container;
    
    var $priority;
    
    /**
     * headline strapline
     */
    var $strapline;

    /**
     * @access private
     */
    var $content;
    
    /**
     * @access private
     * Excerpt
     */
    var $description;
    /**
     * @access private
     */
    var $keywords;
    
    var $page_title;
    
    /**
     * @access private
     */
    var $head;

    var $created;

    var $modified;
    
    var $publish;
    
    /**
     * 1 Normal
     * 2 Don't create a link
     * 0 Don't display in the menu
     */
     
    var $display_in_menu;
    
    var $author;
    
    var $uri_title;
    
    /**
     * numerical permission for user active status
     *
     * 0 display always
     * 1 display at normal login
     * 2 hide at normal login
     * 3 display at trade login
     * 4 hide at trade login
     */
    
    var $display_permission;
    
    var $other_data;

    var $css_class;
    
    var $layout_style;

    var $component;

    var $relations;

    var $display_title;
    
    var $display_secondary_navigation;

    var $require_login;
    
    var $display_breadcrumb;
    
    var $browser_title;
    
    var $link_to_node_id;
    
    var $require_ssl;
    
    /**
     * serialized ACL for client_group
     */
    
    var $display_permission_group_acl;
    
    var $share_counter;
    
    var $customer_id;
    
    var $custom_fields;

    public $Image;

    public $Taxonomy;

    /**
     * Static variable to speed up hiearchy determination within a single request
     */
    static $parentNodeIdCache = array();
    
    var $_metaData = array(
        'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
        'title'=>array('label' => '', 'validation'=>'string', 'required'=>true),
        'node_group'=>array('label' => '', 'validation'=>'string', 'required'=>true),
        'node_controller'=>array('label' => '', 'validation'=>'string', 'required'=>true),
        'parent'=>array('label' => '', 'validation'=>'int', 'required'=>false), //must be required, violates not null, TODO check everywhere
        'parent_container'=>array('label' => '', 'validation'=>'int', 'required'=>false), //must be required, violates not null
        'priority'=>array('label' => '', 'validation'=>'int', 'required'=>false), //must be required, violates not null
        'strapline'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'content'=>array('label' => '', 'validation'=>'xhtml', 'required'=>false),
        'description'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'keywords'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'page_title'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'head'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'body_attributes'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'created'=>array('label' => '', 'validation'=>'datetime', 'required'=>false),
        'modified'=>array('label' => '', 'validation'=>'datetime', 'required'=>true),
        'publish'=>array('label' => '', 'validation'=>'int', 'required'=>false),
        'display_in_menu'=>array('label' => '', 'validation'=>'int', 'required'=>false),
        'author'=>array('label' => '', 'validation'=>'int', 'required'=>false), //must be required, violates not null
        'uri_title'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'display_permission'=>array('label' => '', 'validation'=>'int', 'required'=>false),
        'other_data'=>array('label' => '', 'validation'=>'serialized', 'required'=>false),
        'css_class'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'layout_style'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'component'=>array('label' => '', 'validation'=>'serialized', 'required'=>false),
        'relations'=>array('label' => '', 'validation'=>'serialized', 'required'=>false),
        'display_title'=>array('label' => '', 'validation'=>'int', 'required'=>false),
        'display_secondary_navigation'=>array('label' => '', 'validation'=>'int', 'required'=>false),
        'require_login'=>array('label' => '', 'validation'=>'int', 'required'=>false),
        'display_breadcrumb'=>array('label' => '', 'validation'=>'int', 'required'=>false),
        'browser_title'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'link_to_node_id'=>array('label' => '', 'validation'=>'int', 'required'=>false),
        'require_ssl'=>array('label' => '', 'validation'=>'int', 'required'=>false),
        'display_permission_group_acl'=>array('label' => '', 'validation'=>'serialized', 'required'=>false),
        'share_counter'=>array('label' => '', 'validation'=>'int', 'required'=>false),
        'customer_id'=>array('label' => '', 'validation'=>'int', 'required'=>false),
        'custom_fields'=>array('label' => 'JSON data - binary', 'validation'=>'string', 'required'=>false)
        );
    
    /**
     * create table sql
     */
     
    private function getCreateTableSql() {
    
        $sql = "
CREATE TABLE common_node (
    id serial NOT NULL PRIMARY KEY,
    title character varying(255) NOT NULL,
    node_group character varying(255) NOT NULL,
    node_controller character varying(255),
    parent integer REFERENCES common_node ON UPDATE CASCADE ON DELETE CASCADE,
    parent_container smallint DEFAULT 0 NOT NULL,
    priority integer DEFAULT 0 NOT NULL,
    strapline text,
    content text,
    description text,
    keywords text,
    page_title character varying(255),
    head text,
    body_attributes character varying(255),
    created timestamp(0) without time zone DEFAULT now() NOT NULL,
    modified timestamp(0) without time zone DEFAULT now() NOT NULL,
    publish integer DEFAULT 0 NOT NULL,
    display_in_menu smallint DEFAULT 1 NOT NULL,
    author integer NOT NULL,
    uri_title character varying(255),
    display_permission smallint DEFAULT 0 NOT NULL,
    other_data  text,
    css_class character varying(255) DEFAULT '' NOT NULL,
    layout_style character varying(255) DEFAULT '' NOT NULL,
    component text,
    relations text,
    display_title smallint,
    display_secondary_navigation smallint,
    require_login smallint,
    display_breadcrumb smallint NOT NULL DEFAULT 0,
    browser_title varchar(255) NOT NULL DEFAULT '',
    link_to_node_id integer NOT NULL DEFAULT 0,
    require_ssl smallint NOT NULL DEFAULT 0,
    display_permission_group_acl text,
    share_counter int NOT NULL DEFAULT 0,
    customer_id integer REFERENCES client_customer ON UPDATE CASCADE ON DELETE RESTRICT,
    custom_fields jsonb
);

CREATE INDEX common_node_display_in_idx ON common_node USING btree (display_in_menu);
CREATE INDEX common_node_node_group_idx ON common_node USING btree (node_group);
CREATE INDEX common_node_node_controller_idx ON common_node USING btree (node_controller);
CREATE INDEX common_node_parent_idx ON common_node USING btree (parent);
CREATE INDEX common_node_publish_idx ON common_node USING btree (publish);
CREATE INDEX common_node_custom_fields_idx ON common_node USING gin (custom_fields);
        ";
        
        return $sql;
    }
    
    /**
     * init configuration
     */
     
    static function initConfiguration() {
    
        $conf = array_fill_keys(array(
            'default_status_publish',
            'layout_layout_style',
            'page_layout_style',
            'page_product_layout_style',
            'id_map-root',
            'id_map-global_navigation',
            'id_map-primary_navigation',
            'id_map-ecommerce_navigation',
            'id_map-system_navigation',
            'id_map-footer_navigation',
            'id_map-content_bits',
            'id_map-content_side',
            'id_map-content_foot',
            'id_map-bin',
            'id_map-homepage',
            'id_map-search',
            'id_map-contact',
            'id_map-sitemap',
            'id_map-blog',
            'id_map-login',
            'id_map-registration',
            'id_map-passwordreset',
            'id_map-myaccount',
            'id_map-addressedit',
            'id_map-personal_details',
            'id_map-newsletter_subscribe',
            'id_map-newsletter_unsubscribe',
            'id_map-404',
            'unpublish_on_duplicate',
            'contact_form_default_template'
        ), '');

        if (ONYX_ECOMMERCE) {
            $conf = array_merge($conf, array_fill_keys(array(
                'id_map-myorders',
                'id_map-order_detail',
                'id_map-basket',
                'id_map-checkout',
                'id_map-payment',
                'id_map-payment_protx_success',
                'id_map-payment_protx_failure',
                'id_map-payment_worldpay_callback',
                'id_map-terms',
                'id_map-notifications',
                'id_map-checkout_basket',
                'id_map-checkout_login',
                'id_map-checkout_delivery_options',
                'id_map-checkout_gift',
                'id_map-checkout_summary',
                'id_map-checkout_payment',
                'id_map-checkout_payment_success',
                'id_map-checkout_payment_failure'
            ), ''));
        }

        if (array_key_exists('common_node', $GLOBALS['onyx_conf'])) $conf = array_merge($conf, $GLOBALS['onyx_conf']['common_node']);
        
        /**
         * default new node settings
         */
         
        if (!is_numeric($conf['default_status_publish'])) $conf['default_status_publish'] = 1;
        if (trim($conf['layout_layout_style']) == '') $conf['layout_layout_style'] = 'fibonacci-1-1';
        if (trim($conf['page_layout_style']) == '') $conf['page_layout_style'] = 'fibonacci-2-1';
        if (trim($conf['page_product_layout_style']) == '') $conf['page_product_layout_style'] = 'fibonacci-1-1';
        
        /**
         * id map
         * every page defined in id_map-* will be protected from delete 
         */
        
        //containers
        if (!is_numeric($conf['id_map-root'])) $conf['id_map-root'] = 0;
        if (!is_numeric($conf['id_map-global_navigation'])) $conf['id_map-global_navigation'] = 88;//globalNavigation
        if (!is_numeric($conf['id_map-primary_navigation'])) $conf['id_map-primary_navigation'] = 1;//primaryNavigation
        if (!is_numeric($conf['id_map-ecommerce_navigation'])) $conf['id_map-ecommerce_navigation'] = 2;
        if (!is_numeric($conf['id_map-system_navigation'])) $conf['id_map-system_navigation'] = 3;
        if (!is_numeric($conf['id_map-footer_navigation'])) $conf['id_map-footer_navigation'] = 4;//footerNavigation
        if (!is_numeric($conf['id_map-content_bits'])) $conf['id_map-content_bits'] = 85;
        if (!is_numeric($conf['id_map-content_side'])) $conf['id_map-content_side'] = 86;
        if (!is_numeric($conf['id_map-content_foot'])) $conf['id_map-content_foot'] = 87;
        if (!is_numeric($conf['id_map-bin'])) $conf['id_map-bin'] = 94;
        
        //basic cms pages
        if (!is_numeric($conf['id_map-homepage'])) $conf['id_map-homepage'] = 5;
        if (!is_numeric($conf['id_map-search'])) $conf['id_map-search'] = 21;
        if (!is_numeric($conf['id_map-contact'])) $conf['id_map-contact'] = 20;
        if (!is_numeric($conf['id_map-sitemap'])) $conf['id_map-sitemap'] = 22;
        
        //multiple blogs: define in your local database common_configuration table, e.g: id_map-blog1, id_map-blog2, id_map-blog3
        //default blog container
        if (!is_numeric($conf['id_map-blog'])) $conf['id_map-blog'] = 83;
        
        //customer pages
        if (!is_numeric($conf['id_map-login'])) $conf['id_map-login'] = 8;
        if (!is_numeric($conf['id_map-registration'])) $conf['id_map-registration'] = 13;
        if (!is_numeric($conf['id_map-passwordreset'])) $conf['id_map-passwordreset'] = 9;
        if (!is_numeric($conf['id_map-myaccount'])) $conf['id_map-myaccount'] = 15;
        if (!is_numeric($conf['id_map-addressedit'])) $conf['id_map-addressedit'] = 16;
        if (!is_numeric($conf['id_map-personal_details'])) $conf['id_map-personal_details'] = 18;
        if (!is_numeric($conf['id_map-newsletter_subscribe'])) $conf['id_map-newsletter_subscribe'] = 90;
        if (!is_numeric($conf['id_map-newsletter_unsubscribe'])) $conf['id_map-newsletter_unsubscribe'] = 92;
        
        if (ONYX_ECOMMERCE) {
            //ecommerce pages (onepage checkout)
            if (!is_numeric($conf['id_map-myorders'])) $conf['id_map-myorders'] = 17;
            if (!is_numeric($conf['id_map-order_detail'])) $conf['id_map-order_detail'] = 19;
            if (!is_numeric($conf['id_map-basket'])) $conf['id_map-basket'] = 6;
            if (!is_numeric($conf['id_map-checkout'])) $conf['id_map-checkout'] = 7;
            if (!is_numeric($conf['id_map-payment'])) $conf['id_map-payment'] = 10;
            if (!is_numeric($conf['id_map-payment_protx_success'])) $conf['id_map-payment_protx_success'] = 12;
            if (!is_numeric($conf['id_map-payment_protx_failure'])) $conf['id_map-payment_protx_failure'] = 11;
            if (!is_numeric($conf['id_map-payment_worldpay_callback'])) $conf['id_map-payment_worldpay_callback'] = 999;
            if (!is_numeric($conf['id_map-terms'])) $conf['id_map-terms'] = 26;
            if (!is_numeric($conf['id_map-notifications'])) $conf['id_map-notifications'] = 0; // TODO add to template and get ID
            
            //checkout pages (wizard checkout)
            if (!is_numeric($conf['id_map-checkout_basket'])) $conf['id_map-checkout_basket'] = $conf['id_map-basket'];
            if (!is_numeric($conf['id_map-checkout_login'])) $conf['id_map-checkout_login'] = 0; // TODO add to template and get ID
            if (!is_numeric($conf['id_map-checkout_delivery_options'])) $conf['id_map-checkout_delivery_options'] = $conf['id_map-checkout']; // by default same as checkout page
            if (!is_numeric($conf['id_map-checkout_gift'])) $conf['id_map-checkout_gift'] = 0; // TODO add to template and get ID
            if (!is_numeric($conf['id_map-checkout_summary'])) $conf['id_map-checkout_summary'] = 0; // TODO add to template and get ID
            if (!is_numeric($conf['id_map-checkout_payment'])) $conf['id_map-checkout_payment'] = $conf['id_map-payment'];
            if (!is_numeric($conf['id_map-checkout_payment_success'])) $conf['id_map-checkout_payment_success'] = $conf['id_map-payment_protx_success'];
            if (!is_numeric($conf['id_map-checkout_payment_failure'])) $conf['id_map-checkout_payment_failure'] = $conf['id_map-payment_protx_failure'];
        }
        //system pages
        if (!is_numeric($conf['id_map-404'])) $conf['id_map-404'] = 14;
        
        /**
         * other settings
         */
         
        //unpublish node on duplicate
        if (!is_numeric($conf['unpublish_on_duplicate'])) $conf['unpublish_on_duplicate'] = 0;
        //default template for contact forms
        if (trim($conf['contact_form_default_template']) == '') $conf['contact_form_default_template'] = 'common_simple';
        
        return $conf;
    }
    
    
    /**
     * get detail
     */
     
    function getDetail($id) {
    
        return $this->nodeDetail($id);
    }
    
    /**
     * get list
     */
     
    public function getList($where = '', $order = 'priority DESC, id DESC', $limit = '') {
        
        return $this->listing($where, $order, $limit);
    
    }
    
    /**
     * get list of nodes
     *
     * @param array $filter
     * @return array
     */
     
    function getNodeList($filter = false, $sort = 'common_node.priority DESC, common_node.id DESC') {
        
        $add_to_where = '';
        $join = '';

        /**
         * query filter
         * 
         */
        
        //node type filter
        if ($filter['node_group'] ?? null) {
            $add_to_where .= " AND common_node.node_group = '{$filter['node_group']}'";
        }
        
        //node_controller filter
        if ($filter['node_controller'] ?? null) {
            $add_to_where .= " AND common_node.node_controller = '{$filter['node_controller']}'";
        }
        
        //parent filter
        if (is_numeric($filter['parent'] ?? null)) {
            $add_to_where .= " AND common_node.parent = {$filter['parent']}";
        }
        
        //publish filter (1 for only published, 0 for only not published, and a string (e.g. 'all') for no restriction by publishing status
        //non-authorised backoffice users shoud be limited in controller to use only 1
        if (is_numeric($filter['publish'] ?? null)) {
            $add_to_where .= " AND common_node.publish = {$filter['publish']}";
        }

        // navigation status
        if (is_numeric($filter['display_in_menu'] ?? null)) {
            $add_to_where .= " AND common_node.display_in_menu = {$filter['display_in_menu']}";
        }
        
        //created filter (only year-month)
        if (is_numeric($filter['created'] ?? null)) {
            $add_to_where .= " AND date_part('year', common_node.created) = '{$filter['created']}'";
        } else if (preg_match('/^([0-9]{4})-([0-9]{1,2})$/', $filter['created'] ?? '', $matches)) {
            $add_to_where .= " AND date_part('year', common_node.created) = '{$matches[1]}' AND date_part('month', common_node.created) = '{$matches[2]}'";
        }
        
        //taxonomy filter
        if (is_numeric($filter['taxonomy_tree_id'] ?? null)) {
        
            $join = "LEFT OUTER JOIN common_node_taxonomy ON (common_node_taxonomy.node_id = common_node.id)";
            
            if ($filter['taxonomy_tree_id'] > 0) {
                $add_to_where .= " AND common_node_taxonomy.taxonomy_tree_id = {$filter['taxonomy_tree_id']}";
            } else if ($filter['taxonomy_tree_id'] == 0) {
                $add_to_where .= " AND common_node_taxonomy.taxonomy_tree_id IS NULL";
            }
            
        } else if (is_array($filter['taxonomy_tree_id'] ?? null)) {
            
            /**
             * created $taxonomy_filtered_ids which will contain only allowed values
             */
             
            $taxonomy_filtered_ids = array();
            
            foreach ($filter['taxonomy_tree_id'] as $taxonomy_item_id) {
                
                if (is_numeric($taxonomy_item_id)) {
                    
                    if ($taxonomy_item_id > 0) {
                        $taxonomy_filtered_ids[] = $taxonomy_item_id;
                    }
                    
                }
                
            }
            
            /**
             * if valid $taxonomy_filtered_ids, add extra condition
             */
             
            if (is_array($taxonomy_filtered_ids) && count($taxonomy_filtered_ids) > 0) {
                
                $taxonomy_filtered_ids_string = implode(',', $taxonomy_filtered_ids);
                
                $join = "LEFT OUTER JOIN common_node_taxonomy ON (common_node_taxonomy.node_id = common_node.id)";
                $add_to_where .= " AND common_node_taxonomy.taxonomy_tree_id IN ($taxonomy_filtered_ids_string)";
                
            }
        }
        
        /**
         * SQL query
         */
        $sql = "
            SELECT DISTINCT
            common_node.*
            FROM common_node
            $join
            WHERE parent != {$this->conf['id_map-bin']}
            $add_to_where
            ORDER BY $sort
            ";
        //msg($sql);
        
        /**
         * execute
         */
         
        if ($result = $this->executeSql($sql)) {
        
            return $result;
            
        } else {
            
            return array();
        
        }
    }
    
    /**
     * get list of nodes and associated files
     *
     * @param array $filter
     * @return array
     */
     
    function getNodeListIncludingFiles($filter, $sort) {
        
        $node_list = $this->getNodeList($filter, $sort);

        foreach ($node_list as $k=>$item) {
            $node_list[$k] = $this->unpackDetail($item);
            $node_list[$k]['files'] = $this->getFilesForNodeId($item['id']);
        }

        return $node_list;
    }

    /**
     * node detail
     *
     * @param int $id
     * @return array
     */
        
    function nodeDetail($id) {
    
        $node_data = $this->detail($id);
        
        if (!$node_data) {
            msg("node id='$id' does not exist", 'error', 2);
            return false;
        }
        
        $node_data = $this->unpackDetail($node_data);

        // store parent into static variable, for caching
        self::$parentNodeIdCache[$id] = $node_data['parent'];

        return $node_data;
    }

    /**
     * unpack details
     *
     * @param array $node_data
     * @return array
     */

    public function unpackDetail($node_data) {

        if (!is_array($node_data)) return false;

        $node_data['author_detail'] = $this->getAuthorDetailbyId($node_data['customer_id']);
    
        if (!is_null($node_data['other_data'])) $node_data['other_data'] = unserialize(trim($node_data['other_data']));
        if (!is_null($node_data['custom_fields'])) $node_data['custom_fields'] = json_decode($node_data['custom_fields']);
        else $node_data['custom_fields'] = new stdClass();
        if (!is_null($node_data['component'])) $node_data['component'] = unserialize(trim($node_data['component']));
        if (!is_null($node_data['relations'])) $node_data['relations'] = unserialize(trim($node_data['relations']));
        if (!is_null($node_data['display_permission_group_acl'])) $node_data['display_permission_group_acl'] = unserialize($node_data['display_permission_group_acl']);
        
        //overwrite author (allowed in bo/node/news edit interface)
        if (is_array($node_data['component']) && isset($node_data['component']['author']) && $node_data['component']['author'] != '') $node_data['author_detail']['name'] = $node_data['component']['author'];

        return $node_data;
    }
    
    /**
     * node detail including related files
     *
     * @param int $id
     * @return array
     */

    public function getNodeDetailIncludingFiles($id) {

        if (!is_numeric($id)) return false;

        $node_data = $this->nodeDetail($id);
        $node_data['files'] = $this->getFilesForNodeId($id);

        return $detail;
    }
    
    /**
     * get author detail
     */
     
    public function getAuthorDetailbyId($author_id) {
    
        if ($author_id == 0) return array(
            'id' => 1000,
            'username' => "superuser",
            'email' => $GLOBALS['onyx_conf']['global']['admin_email'],
            'name' => $GLOBALS['onyx_conf']['global']['admin_email_name']
        );

        require_once('models/client/client_customer.php');
        $Custmer = new client_customer();
        $customer = $Custmer->getDetail($author_id);

        if ($customer) return array(
            'id' => $customer['id'],
            'username' => $customer['email'],
            'email' => $customer['email'],
            'name' => $customer['first_name'] . ' ' . $customer['last_name']
        );

        return false;
        
    }
    
    /**
     * update node
     *
     * @param array $node_data
     * @return bool
     */
    
    function nodeUpdate($node_data) {
    
        $node_data['modified'] = date('c');

        if (is_array($node_data['other_data'] ?? null)) $node_data['other_data'] = serialize($node_data['other_data']);
        if (is_object($node_data['custom_fields'] ?? null) || is_array($node_data['custom_fields'] ?? null)) $node_data['custom_fields'] = json_encode($node_data['custom_fields']);
        if (is_array($node_data['component'] ?? null)) $node_data['component'] = serialize($node_data['component']);
        if (is_array($node_data['relations'] ?? null)) $node_data['relations'] = serialize($node_data['relations']);
        
        // set owner only if empty
        if (isset($_SESSION['client']['customer']['id']) && (!isset($node_data['customer_id']) || !is_numeric($node_data['customer_id']))) $node_data['customer_id'] = (int) $_SESSION['client']['customer']['id'];
        
        //populate only if ACL in use, otherwise save as empty
        if (is_array($node_data['display_permission_group_acl'] ?? null)) {
            if (in_array('0', $node_data['display_permission_group_acl']) || in_array('1', $node_data['display_permission_group_acl'])) $node_data['display_permission_group_acl'] = serialize($node_data['display_permission_group_acl']);
            else $node_data['display_permission_group_acl'] = '';
        } else {
            $node_data['display_permission_group_acl'] = '';
        }
        
        /**
         * valid parent
         */
         
        if (!$this->validateParent($node_data['id'], $node_data['parent'])) return false;
        
        /**
         * commit update
         */
        
        if ($this->update($node_data)) {
            // load full data in case the UI didn't have node_group option
            $node_data_full = $this->detail($node_data['id']);
            if ($node_data_full['node_group'] == 'page') {
                    // update existing or insert a new one
                    if (!$this->updateSingleURI($node_data)) $this->insertNewMappingURI($node_data);
            }
            return true;
        } else {
            $node_group = ucfirst($node_data['node_group']);
            msg("$node_group (id={$node_data['id']}) can't be updated", 'error');
            return false;
        }
    }


    
    /**
     * restore revision node
     *
     * @param array $revision_data
     * @return bool
     */

    function restoreRevision($revision_data) {
        $node_data = unserialize($revision_data['content']);
        
        $node_data['modified'] = date('c');
        
        // set owner only if empty
        if (!is_numeric($node_data['customer_id'])) $node_data['customer_id'] = (int) $_SESSION['client']['customer']['id'];;
        
        //populate only if ACL in use, otherwise save as empty
        if (is_array($node_data['display_permission_group_acl'])) {
            if (in_array('0', $node_data['display_permission_group_acl']) || in_array('1', $node_data['display_permission_group_acl'])) $node_data['display_permission_group_acl'] = serialize($node_data['display_permission_group_acl']);
            else $node_data['display_permission_group_acl'] = '';
        } else {
            $node_data['display_permission_group_acl'] = '';
        }
        
        /**
         * valid parent
         */
         
        if (!$this->validateParent($node_data['id'], $node_data['parent'])) return false;
        
        /**
         * commit update
         */
        
        if ($this->update($node_data)) {
            // load full data in case the UI didn't have node_group option
            $node_data_full = $this->detail($node_data['id']);
            if ($node_data_full['node_group'] == 'page') {
                    // update existing or insert a new one
                    if (!$this->updateSingleURI($node_data)) $this->insertNewMappingURI($node_data);
            }
            return true;
        } else {
            $node_group = ucfirst($node_data['node_group']);
            msg("$node_group (id={$node_data['id']}) can't be restored to revision {$revision_data['id']}", 'error');
            return false;
        }
    }
    
    /**
     * insert a new node
     *
     * @param array $node_data
     * @return integer $id
     */
    
    function nodeInsert($node_data, $position = null) {
        
        //if title is empty, create a generic title and don't display it
        $node_data['display_title'] = $node_data['display_title'] ?? '';
        $node_data['title'] = $node_data['title'] ?? '';
        if  ($node_data['title'] == '') {
            $node_data['title'] = "{$node_data['node_group']} " . time();
            $node_data['display_title'] = 0;
        } else if (!is_numeric($node_data['display_title'])) {
            $node_data['display_title'] = 1;
        }
        
        $node_data['created'] = date('c');
        $node_data['modified'] = date('c');
    
        $node_data['publish'] = $node_data['publish'] ?? '';
        if (!is_numeric($node_data['publish'])) {
            if ($node_data['node_group'] == 'page' || $node_data['node_group'] == 'container') {
                $node_data['publish'] = $this->conf['default_status_publish'];
            } else {
                $node_data['publish'] = 1;
            }
        }
    
        if (!isset($node_data['parent_container']) || !is_numeric($node_data['parent_container'])) $node_data['parent_container'] = 0;
        if (!isset($node_data['priority']) || !is_numeric($node_data['priority'])) $node_data['priority'] = 0;
        
        $bo_user_id = Onyx_Bo_Authentication::getInstance()->getUserId();
        if (is_numeric($bo_user_id)) $node_data['customer_id'] = $bo_user_id;
        else $node_data['customer_id'] = (int) $_SESSION['client']['customer']['id'];
        
        $node_data['author'] = $node_data['customer_id']; // deprecated as of Onyx 1.7
        $node_data['css_class'] = $node_data['css_class'] ?? '';
        $node_data['browser_title'] = $node_data['browser_title'] ?? '';
        if (!isset($node_data['display_in_menu']) || !is_numeric($node_data['display_in_menu'])) $node_data['display_in_menu'] = 1;
        if (!isset($node_data['display_permission']) || !is_numeric($node_data['display_permission'])) $node_data['display_permission'] = 0;
        if (!isset($node_data['display_breadcrumb']) || !is_numeric($node_data['display_breadcrumb'])) $node_data['display_breadcrumb'] = 0;
        if (!isset($node_data['link_to_node_id']) || !is_numeric($node_data['link_to_node_id'])) $node_data['link_to_node_id'] = 0;
        if (!isset($node_data['require_ssl']) || !is_numeric($node_data['require_ssl'])) $node_data['require_ssl'] = 0;
        
        if ($node_data['node_group'] == 'layout') $node_data['layout_style'] = $this->conf['layout_layout_style'];
        else if ($node_data['node_group'] == 'page' && $node_data['node_controller'] == 'product') $node_data['layout_style'] = $this->conf['page_product_layout_style'];
        else if ($node_data['node_group'] == 'page') $node_data['layout_style'] = $this->conf['page_layout_style'];
        else $node_data['layout_style'] = '';
        
        if (is_array($node_data['other_data'] ?? null)) $node_data['other_data'] = serialize($node_data['other_data']);
        if (is_array($node_data['custom_fields'] ?? null)) $node_data['custom_fields'] = json_encode($node_data['custom_fields']);
        if (is_array($node_data['relations'] ?? null)) $node_data['relations'] = serialize($node_data['relations']);
        if (is_array($node_data['component'] ?? null)) $node_data['component'] = serialize($node_data['component']);
        
        //TODO: before insert, do a check, that node_data[title] is unique
        
        if ($id = $this->insert($node_data)) {
            
            $node_data['id'] = $id;
            
            if ($node_data['node_group'] == 'page') $this->insertNewMappingURI($node_data);

            if($position) {
                $this->changePosition($id, $position);
            }
            
            return $id;
        } else {
            msg("Node insert failed", 'error');
            return false;
        }
    
    }
    
    /**
     * Create a single new record in mapping table
     */
     
    function insertNewMappingURI($node_data) {
    
        require_once('models/common/common_uri_mapping.php');
        $Mapper = new common_uri_mapping();
        
        if ($Mapper->insertNewPath($node_data)) return true;
        else return false;
    }
    
    /**
     * Update a single record in mapping table
     */
     
    function updateSingleURI($node_data) {
    
        require_once('models/common/common_uri_mapping.php');
        $Mapper = new common_uri_mapping();
        
        if ($Mapper->updateSingle($node_data)) return true;
        else return false;
    }
    
    /**
     * get active pages (only node_group=page)
     *
     * @param unknown_type $id
     * @return unknown
     */
    
    function getActivePages($id) {
    
        if (!is_numeric($id)) return false;
        
        // find root parent page
        $fullpath = $this->getFullPathDetail($id);
        $active_pages = array();
    
        foreach ($fullpath as $fp) {
            if ($fp['node_group'] == 'page') {
                $active_pages[] = $fp['id'];
            }
        }
        
        return $active_pages;
    }
    
    
    /**
     * get active nodes
     *
     * @param int $id
     * @param array $filter
     * @return array
     */
    
    function getActiveNodes($id, $filter = false) {
    
        if (!is_numeric($id)) return false;
        
        // find root parent page
        $fullpath = $this->getFullPathDetail($id);
        $active_pages = array();
    
        foreach ($fullpath as $fp) {
            if (is_array($filter)) {
                if (in_array($fp['node_group'], $filter)) {
                    $active_pages[] = $fp['id'];
                }
            } else {
                
                $active_pages[] = $fp['id'];
            }
        }
        
        return $active_pages;
    }

    /**
     * get full path
     *
     * @param unknown_type $id
     * @return unknown
     */

    function getParentNodeId($id) 
    {
        if (!is_numeric($id)) return false;
        if (isset(self::$parentNodeIdCache[$id])) return self::$parentNodeIdCache[$id];
        $sql = "SELECT parent FROM common_node WHERE id = $id";
        $result = $this->executeSql($sql);
        return self::$parentNodeIdCache[$id] = (int) $result[0]['parent'];
    }
    
    /**
     * get full path
     *
     * @param unknown_type $id
     * @return unknown
     */

    function getFullPath($id) {
    
        if (!is_numeric($id)) return false;

        $result = array((int) $id);
        $parent_id = $this->getParentNodeId($id);

        while ($parent_id > 0) {
            $result[] = $parent_id;
            $parent_id = $this->getParentNodeId($parent_id);
        }

        return $result;
    }
    
    /**
     * get detailed full path
     *
     * @param unknown_type $id
     * @return unknown
     */
     
    function getFullPathDetail($id) {
    
        if (!is_numeric($id)) return false;
        
        msg("Calling getFullPathDetail($id)", 'error', 2);
        
        $path = array();
        $i = 0;
        
        if ($id > 0) {
            $path[$i] = $this->detail($id);
            $parent = $path[$i]['parent'];
            
            //only for pages// !!cant, not working product_list in node
            //if ($path[$i]['node_group'] == 'page') {
                while($parent > 0) {
                    $i++;
                    $path[$i] = $this->detail($path[$i-1]['parent']);
                    $parent = $path[$i]['parent'];
                }
            //}
        }
        return $path;
    }
    
    /**
     * get fullpath detail for breadcrumb
     */
     
    function getFullPathDetailForBreadcrumb($id) {
        
        if (!is_numeric($id)) return false;
        
        $path = $this->getFullPathDetail($id);
        $path = array_reverse($path);
        
        return $path;
    }
    
    /**
     * find first parent page in the node tree, starting from root
     * use getParentPageId() if you needed nearest parent page
     
     * @param int $id
     * @return int
     */
    
    function getFirstParentPageId($id) {
    
        if (!is_numeric($id)) return false;
        
        $active_pages = $this->getActivePages($id);

        //first page in path
        if (is_array($active_pages)) $active_pages = array_reverse($active_pages);
        $first_parent_page_id = $active_pages[0];
        
        return $first_parent_page_id;
    }

    /**
     * find first parent page in the node tree, starting from root
     * use getParentPageId() if you needed nearest parent page
     
     * @param array $active_pages
     * @return int
     */
    
    function getFirstParentPage($active_pages) {
    
        //first page in path
        if (is_array($active_pages)) $active_pages = array_reverse($active_pages);
        $first_parent_page_id = $active_pages[0];
        
        return $first_parent_page_id;
    }
    
    /**
     * find last parent page in node tree, starting from root
     *
     * @param array $active_pages
     * @return int
     */
    
    function getLastParentPage($active_pages) {
    
        //last page in path
        $last_parent_page_id = $active_pages[0];
        
        return $last_parent_page_id;
    }
    
    /**
     * find (nearest) parent page id
     *
     * @param integer $id
     * @return integer
     */

    function getParentPageId($id) {
    
        $active_pages = $this->getActivePages($id);
        $parent_id = $this->getLastParentPage($active_pages);

        return $parent_id;
    }
    
    /**
     * DEPRECATED
     *
     * @param integer $id
     * @return integer
     */
    
    function getParentPageIdOfNode($id) {
    
        return $this->getParentPageId($id);
        
    }

    /**
     * prepare SQL filtering query for specified filter options
     *
     * @param integer $publish
     * @param string $filter
     * @return string @sql
     */
    protected function prepareNodeGroupFilter($publish, $filter) {

        $sql = '';
        
        // TODO: only backoffice menu and sitemap should display containers
        switch ($filter) {

            case 'all':
            case 'content':
                $sql = '';
                break;

            case 'layout':
                $sql = "AND (node_group = 'layout' OR node_group = 'page' OR node_group = 'container') ";
                break;
                
            case 'page':
            default:
                $sql = "AND (node_group = 'page' OR node_group = 'container')";
                break;
            
            case 'page_exclude_news':
                $sql = "AND (node_group = 'page' OR node_group = 'container') AND node_controller != 'news'";
                break;
                
            case 'page_exclude_products_recipes':
                $sql = "AND (node_group = 'page' OR node_group = 'container') AND (node_controller != 'product' AND node_controller != 'recipe')";
                break;
            case 'page_exclude_products_recipes_news':
                $sql = "AND (node_group = 'page' OR node_group = 'container') AND (node_controller != 'product' AND node_controller != 'recipe' AND node_controller != 'news')";
                break;
        }

        if ($publish == 1) $sql .= " AND display_in_menu > 0";
        
        return $sql;
        
    }
    
    /**
     * get tree (as a flat list)
     *
     * input and output structured for menu (tree) component
     *
     * @param integer $publish
     * @param string $node_group
     * @return array $records
     */

    function getTree($publish = 1, $filter = 'page') {
    
        $condition = $this->prepareNodeGroupFilter($publish, $filter);

        $sql = "SELECT id, content, parent, title as name, page_title as title, node_group, node_controller, display_in_menu, display_permission, publish, priority, strapline, description, component FROM common_node WHERE publish >= $publish $condition ORDER BY priority DESC, id ASC";
        
        if ($records = $this->executeSql($sql)) {
        
            return $records;
        
        } else {
            
            return false;
        
        }
    }

    /**
     * get list of node by parent id
     */
    function getNodesByParent($publish = 1, $filter = 'page', $parent_id = false)
    {
        $condition = $this->prepareNodeGroupFilter($publish, $filter);

        if (is_numeric($parent_id)) $root = "AND parent = $parent_id";
        else $root = "AND (parent = 0 OR parent IS NULL)";
        
        $sql = "SELECT id, content, parent, title as name, page_title as title, node_group, node_controller, 
                display_in_menu, display_permission, publish, priority, strapline, description, component 
            FROM common_node 
            WHERE publish >= $publish $condition $root
            ORDER BY priority DESC, id ASC";        

        $records = $this->executeSql($sql);

        return $records;
    }

    /**
     * get lazy tree
     *
     * DEPRECATED
     *
     * @param unknown_type $from
     * @param unknown_type $publish
     * @param unknown_type $node_group
     * @return unknown
     */
     
    function getLazyTree($from = 0, $publish = 1, $node_group = 'page') {
    
        $whole_tree = $this->getTree($publish, $node_group);
        $tree = array();
        
        foreach ($whole_tree as $t) {
            if ($t['id'] == $from) $tree[] = $t;
        }
        
        foreach ($whole_tree as $t) {
            if ($t['parent'] == $from) $tree[] = $t;
        }
        
        return $tree;
    }
    
    /**
     * validate parent
     * this is not necesarly to call everytime - only on update
     *
     * @param unknown_type $item_id
     * @param unknown_type $item_parent
     * @return unknown
     */

    function validateParent($item_id, $item_parent) {
        
        /**
         * item_id shouldnt be same as parent
         */
         
        if ($item_parent == $item_id && $item_parent > 0) {
            msg("Can't be parent of itself!", 'error');
            $this->setValid('parent', false);
            return false;
        }
        
        /**
         * check requested parent is not under item in page tree
         */
         
        $parent_path = array_reverse($this->getFullPath($item_parent) ?? []);

        foreach ($parent_path as $parent_path_id) {
            if ($parent_path_id == $item_id) {
                msg("Node ID $item_id can't be parent of itself!", 'error');
                $this->setValid('parent', false);
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * get shared
     *
     * @param unknown_type $linked_to
     * @return unknown
     */
     
    function getShared($linked_to) {
    
        $shared = $this->listing("content = '$linked_to' AND node_controller = 'shared'");
        return $shared;
    }
    
    /**
     * parse children
     *
     * @param integer $id
     * @return mixed
     */
     
    function parseChildren($id, $container = false, $disable_fe_edit = false) {
    
        if (is_numeric($id)) {
        
            $contentx = [];
            $limit_to_container = is_numeric($container) ? "AND parent_container = $container" : '';
            
            $children = $this->listing("parent = $id AND (node_group = 'layout' OR node_group = 'content' OR node_group = 'variable') $limit_to_container", "priority DESC, id ASC");
            
            foreach ($children as $key=>$child) {
                if ($this->checkDisplayPermission($child)) {
                    $_nOnyx = new Onyx_Request("node&id={$child['id']}&no_parent=1&disable_fe_edit=$disable_fe_edit");
                    $contentx[$child['id']]['content'] = $_nOnyx->getContent();
                    $contentx[$child['id']]['container'] = $child['parent_container'];
                }
            }
    
            return $contentx;
            
        } else {
            
            trigger_error("Node->parseChildren: id is not numeric");
            return false;
        
        }
    }
    
    /**
     * CONDITIONAL DISPLAY OPTION BY display_permission and publish status
     *
     * @param array $node_data
     * @return bool
     */
     
    static function checkDisplayPermission($node_data, $force_admin_visibility = true) {
    
        //for editor show allways
        if (Onyx_Bo_Authentication::getInstance()->isAuthenticated() && $force_admin_visibility) {
        
            return true;
        
        } else {
        
            /**
             * dont' display when not published
             */
             
            if ($node_data['publish'] == 0) {
                return false;
            }
            
            /**
             * otherwise check display_permission
             */
             
            if ($node_data['display_permission'] == 0) {
                //0 display allways
                return true;
            } else if ($node_data['display_permission'] == 1) {
                //1 display only for logged in
                if (!empty($_SESSION['client']['customer']['id'])) {
                    return true;
                } else {
                    return false;
                }
            } else if ($node_data['display_permission'] == 2) {
                //2 dont display for logged in users
                if (empty($_SESSION['client']['customer']['id'])) {
                    return true;
                } else {
                    return false;
                }
            } else if ($node_data['display_permission'] == 3) {
                //3 show at trade
                if ($_SESSION['client']['customer']['account_type'] == 2) {
                    return true;
                } else {
                    return false;
                }
            } else if ($node_data['display_permission'] == 4) {
                //4 hide at trade
                if ($_SESSION['client']['customer']['account_type'] == 2) {
                    return false;
                } else {
                    return true;
                }
            }
            
        }

        return false;
    }
    
    /**
     * check group_acl
     */
     
    public function checkDisplayPermissionGroupAcl($node_data, $force_admin_visibility = true) {
        
        // return true in case display permission are not set
        if (!is_array($node_data['display_permission_group_acl'])) return true;
        
        if (Onyx_Bo_Authentication::getInstance()->isAuthenticated() && $force_admin_visibility) {
        
            return true;
        
        }

        // first set rule for Everyone
        switch ($node_data['display_permission_group_acl'][0]) {
            
            case '0':
                $visibility = false;
            break;
            case '1':
                $visibility = true;
            break;
            case '-1':
            default:
                $visibility = true;
            break;
            
        }

        // than set rule as per active user groups
        if (!is_array($_SESSION['client']['customer']['group_ids'])) return $visibility;
        if (count($_SESSION['client']['customer']['group_ids']) == 0) return $visibility;

        $visible = 0;
        $invisible = 0;

        foreach ($_SESSION['client']['customer']['group_ids'] as $group_id) {

            switch ($node_data['display_permission_group_acl'][$group_id]) {
                
                case '0':
                    $invisible++;
                    break;

                case '1':
                    $visible++;
                    break;

            }

        }

        // visibility has priority
        if ($visible > 0) return true;

        // if no visibility explicitly defined and invisibility explicitly defined then hide
        if ($visible == 0 && $invisible > 0) return false;

        // otherwise use rule for everyone
        return $visibility;

    }

    /**
     * get children
     *
     * @param unknown_type $parent_id
     * @return unknown
     */
     
    function getChildren($parent_id, $sort_by = 'node_group DESC, node_controller DESC, parent_container ASC, priority DESC', $published_only = false) {
        
        if ($published_only) $published_only_constraint = 'AND publish = 1';
        else $published_only_constraint = '';
        
        $children = array();

        if (is_numeric($parent_id)) {
            $children = $this->listing("parent = {$parent_id} $published_only_constraint", $sort_by);
        }
        
        return $children;
    }
    
    /**
     * TODO: add filter for "display_permission"
     */
    
    function search($q) {
    
        $q = pg_escape_string($q);
        $qs = explode(" ", $q);
        $where_query = '';
        
        foreach ($qs as $q) {
        
            $where_query .= "
                SELECT *
                FROM common_node
                WHERE to_tsvector('english', 
                    title || ' ' || 
                    coalesce(page_title, '') || ' ' || 
                    coalesce(description, '')  || ' ' || 
                    coalesce(keywords, '')  || ' ' || 
                    coalesce(content, '')
                    ) @@ to_tsquery('english', '$q')
                AND node_controller != 'symbolic' AND publish = 1
                ORDER BY modified DESC;
            ";
        
        }
        
        //msg($where_query);
        $result = $this->executeSQL($where_query);
        
        return $result;
    }
    
    
    /**
     * return pages and products which we want to display in sitemap 
     */

    function getFlatSitemap()
    {
        $sql = "
        -- get all nodes where every parent node in their hierarchy is published.
        WITH RECURSIVE node_tree AS (
            SELECT 
                id, parent, title, page_title, node_group, node_controller, content, display_in_menu, publish, priority, strapline, display_permission, modified, require_login
            FROM 
                common_node
            WHERE 
                parent = 0
                AND publish >= 1

            UNION 

            SELECT 
                cn.id, cn.parent, cn.title, cn.page_title, cn.node_group, cn.node_controller, cn.content, cn.display_in_menu, cn.publish, cn.priority, cn.strapline, cn.display_permission, cn.modified, cn.require_login
            FROM 
                common_node cn
            INNER JOIN 
                node_tree nt ON cn.parent = nt.id
            WHERE 
                cn.publish >= 1
                AND cn.node_group IN ('site', 'container', 'page') 
        ),

        -- get last modified for all pages and their children
        modified_tree AS (
            SELECT 
                id, parent, node_group, modified, id as root, 0 as level
            FROM 
                common_node
            WHERE
                node_group = 'page'

            UNION

            SELECT 
                cn.id, cn.parent, cn.node_group, cn.modified, mt.root, mt.level + 1
            FROM
                common_node cn
            INNER JOIN
                modified_tree mt ON cn.parent = mt.id
            WHERE
                -- for 0 level, we want to include all nodes
                -- for > 0 level, we want to include only childs of layout and content
                (mt.level = 0 OR mt.node_group IN ('layout', 'content'))
        ),

        -- get last modified by selecting the max modified date for each root node / page
        max_modified AS (
            SELECT 
                root, MAX(modified) as modified
            FROM 
                modified_tree
            GROUP BY 
                root
        )

        SELECT DISTINCT 
            nt.id, nt.parent, nt.title as name, nt.page_title as title, nt.node_group, nt.node_controller, nt.content, nt.display_in_menu, nt.publish, nt.priority, nt.strapline, nt.display_permission, mm.modified
        FROM 
            node_tree nt
        LEFT JOIN 
            max_modified mm ON nt.id = mm.root
        WHERE 
            nt.node_group = 'page'
            AND (nt.require_login IS NULL OR nt.require_login = 0)
            AND nt.display_permission = 0
            AND nt.display_in_menu > 0
            AND nt.parent != " . $this->conf['id_map-system_navigation'] . " 
            AND nt.parent != " . $this->conf['id_map-ecommerce_navigation'] . "
        ORDER BY 
            nt.id ASC
        ";

        $records = $this->executeSql($sql);

        if (!is_array($records)) return false;

        //filter only homepages of products
        if (ONYX_ECOMMERCE) {
            require_once("models/ecommerce/ecommerce_product.php");
            $Product = new ecommerce_product();
        }

        foreach ($records as $record) {

            if (ONYX_ECOMMERCE && $record['node_group'] == 'page' && $record['node_controller'] == 'product') {
                $homepage = $Product->getProductHomepage($record['content']);
                if ($homepage['id'] == $record['id']) $sitemap[] = $record;
            } else {
                $sitemap[] = $record;
            }
        }

        return $sitemap;
    }

    
    /**
     * Get SEO url path from uri_mapping for single node
     */
    
    function getSeoURL ($node_id) {
    
        if (!is_object($this->_common_uri_mapping)) {
            require_once("models/common/common_uri_mapping.php");
            $this->_common_uri_mapping = new common_uri_mapping();
        }
        
        
        $link = "/page/{$node_id}";
        $seo_link = $this->_common_uri_mapping->stringToSeoUrl($link);
        
        return $seo_link;
    }
    
    
    /**
     * get node id from seo uri
     */
     
    function getNodeIdFromSeoUri($seo_uri) {
    
        if (!is_object($this->_common_uri_mapping)) {
            require_once("models/common/common_uri_mapping.php");
            $this->_common_uri_mapping = new common_uri_mapping();
        }
        
        $node_id = $this->_common_uri_mapping->getNodeIdFromSeoUri($seo_uri);
        
        if (is_numeric($node_id)) return $node_id;
        else {
            msg("Node_id for SEO URI $seo_uri was not found", 'error', 1);
            return false;
        }
    }
    
    /**
     * Check children and return last modified datetime of content
     * 
     */
     
    function getLastMod($node_id, $modified = "") {
    
        //first get last mod of node itself
        if ($modified == "") {
            $node_detail = $this->detail($node_id);
            $last_modified = $node_detail['modified'];
        } else {
            $last_modified = $modified;
        }
        
        //find direct modified
        if (is_numeric($node_id)) {
            $children = $this->listing("parent = {$node_id}", "modified DESC");
        }
        
        if ($children[0]['modified'] > $last_modified) {
            $last_modified = $children[0]['modified'];
        }
        
        
        // find modified within layout
        $last_modified_layout = array();
        
        foreach ($children as $child) {
            if ($child['node_group'] == 'layout') {
                 $last_modified_layout[] = $this->getLastMod ($child['id']);
            }
        }
        
        if (count($last_modified_layout) > 0) {
            foreach($last_modified_layout as $k=>$v) {
                if ($v > $last_modified) {
                    $last_modified = $v;
                }
            }
        }
        
        
        return $last_modified;
        
    }
    
    /**
     * Find homepage of product
     */
     
    function getProductNodeHomepage($product_id) {
    
        if (!is_numeric($product_id)) {
        
            msg("Node->getProductNodeHomepage($product_id) is not numeric", 'error');
            return false;
        
        } else {
        
            require_once("models/ecommerce/ecommerce_product.php");
            $Product = new ecommerce_product();
        
            $homepage = $Product->getProductHomepage($product_id);
        
            return $homepage;
        }
    }
    
    /**
     * Find homepage of recipe
     */
     
    function getRecipeNodeHomepage($recipe_id) {
    
        if (!is_numeric($recipe_id)) {
            
            msg("Node->getRecipeNodeHomepage($product_id) is not numeric", 'error');
            return false;
            
        } else {
        
            require_once("models/ecommerce/ecommerce_recipe.php");
            $Recipe = new ecommerce_recipe();
        
            $homepage = $Recipe->getRecipeHomepage($recipe_id);
        
            return $homepage;
        }
    }
    
    /**
     * Find hard links (not /page/{node.id})
     *
     * @return unknown
     */
     
    function findHardLinks($node_id = null) {

        /**
         * filter
         */
         
        $add_to_where = '';
        if (is_numeric($node_id)) $add_to_where = " AND id = $node_id";
        
        /**
         * server URL
         */
    
        $http_host = $_SERVER['HTTP_HOST'];
        
        /**
         * query
         */
         
        $sql = "SELECT id, title, content, modified FROM common_node WHERE 
                (content SIMILAR TO '%href=\"/%' OR 
                content SIMILAR TO '%href=\"https*://$http_host/%') AND 
                content NOT SIMILAR TO '%href=\"/page/[0-9]*\"%' 
                $add_to_where ORDER BY modified DESC";
        
        /**
         * execute
         */
         
        if ($result = $this->executeSql($sql)) {
        
            if (count($result) > 0) return $result;
            else return array();
            
        } else {
        
            return array();
        
        }
        
    }
    
    /**
     * move item
     */
    
    function moveItem($source_id, $destination_id, $position, $container = 0) {
    
        if (!is_numeric($source_id) || !is_numeric($destination_id) || !is_numeric($position)) return false;
        
        //change parent
        if (!$this->updateSingleAttribute('parent', $destination_id, $source_id)) return false;
        
        //change container
        if (!$this->updateSingleAttribute('parent_container', $container, $source_id)) return false;
        
        //changePosition
        
        if ($this->changePosition($source_id, $position)) return true;
        else return false;
        
        return true;
    }
    
    /**
     * move item
     */
    
    function moveToBin($node_id) {
        
        $destination_id = $this->conf['id_map-bin'];
        
        if (!is_numeric($node_id)) {
            msg("moveToBin: node_id isn't numeric", 'error');
            return false;
        }
        
        if (!is_numeric($destination_id)) {
            msg("moveToBin: Bin node_id isn't numeric", 'error');
            return false;
        }
                
        //change parent
        if ($this->updateSingleAttribute('parent', $destination_id, $node_id)) return true;
        else return false;
        
    }

    function deleteFromBin($node_id) {

        if (!is_numeric($node_id)) {
            msg("deleteFromBin: node_id isn't numeric", 'error');
            return false;
        }

        $parents = $this->getFullPath($node_id) ?? [];

        if(in_array($this->conf['id_map-bin'], $parents)) {
            return $this->delete($node_id);
        } else {
            msg("deleteFromBin: node needs to be moved into Bin first in order to be deleted", 'error');
            return false;
        }
    }
    
    /**
     * change position
     */
     
    function changePosition($item_id, $position) {
    
        if (!is_numeric($item_id) || !is_numeric($position)) return false;

        //get list of all siblings
        if ($sibling_list = $this->getSiblingList($item_id)) {
            
            $sibling_count = count($sibling_list);
            
            $i = 0;
            
            foreach ($sibling_list as $sibling) {
            
                //msg("$i Sibling id {$sibling['id']} with priority {$sibling['priority']}");
                if ($sibling['id'] == $item_id) {
                    $new_priority = ($sibling_count - $position) * 10 + 5;
                } else {
                    $new_priority = ($sibling_count - $i) * 10;
                    $i++;
                }
                
                $this->updateSingleAttribute('priority', $new_priority, $sibling['id']);
                
            }
            
            return true;
            
        } else {
            
            return false;
        
        }
    }
    
    /**
     * get sibling
     */
     
    function getSiblingList($item_id, $exclude_unrelated = true) {
    
        if (!is_numeric($item_id)) return false;
        
        if ($item_data = $this->detail($item_id)) {
            
            /**
             * prepare exclude_query to make sure we are getting only related siblings
             */
            
            if ($exclude_unrelated) {
                
                if (in_array($item_data['node_group'], array('page', 'container', 'site'))) {
                    
                    // when ecommerce is enabled, include page or container, don't include products, recipes and news
                    if (ONYX_ECOMMERCE) $exclude_query = "(node_group = 'page' OR node_group = 'container') AND node_controller != 'product' AND node_controller != 'recipe' AND node_controller != 'news'";
                    else $exclude_query = "(node_group = 'page' OR node_group = 'container')";
                    
                } else {
                    
                    //asking for content or layout, exclude all pages and containers
                    $exclude_query = "node_group != 'page' AND node_group != 'container'";
                }
                
            } else {
                
                $exclude_query = '1=1';
            }
            
            //use same sorting as getTree() function
            $list = $this->listing("parent_container = {$item_data['parent_container']} AND parent = {$item_data['parent']} AND $exclude_query", 'priority DESC, id ASC');
            
        } else {
            
            return false;
            
        }
        
        if (is_array($list)) return $list;
        else return false;
    }
    
    /**
     * temporary implementation (will be in general model in future)
     */
    
    function updateSingleAttribute($attribute, $update_value, $id) {
    
        switch ($attribute) {
            
            case 'parent':
                //safety check
                if ($id == $update_value) {
                    msg("common_node: parent cannot be identical to id", 'error');
                    return false;
                }
                $data = $this->getDetail($id);
                unset($data['author_detail']);
                if (is_array($data)) {
                    $data['parent'] = $update_value;
                    if ($this->nodeUpdate($data)) return true;
                    else return false;
                }
            break;
            
            case 'parent_container':
                $data = $this->getDetail($id);
                unset($data['author_detail']);
                if (is_array($data)) {
                    $data['parent_container'] = $update_value;
                    if ($this->nodeUpdate($data)) return true;
                    else return false;
                }
            break;
            
            case 'priority':
                $data = $this->getDetail($id);
                unset($data['author_detail']);
                if (is_array($data)) {
                    $data['priority'] = $update_value;
                    if ($this->nodeUpdate($data)) return true;
                    else return false;
                }
            break;
            
            case 'page_title':
                $data = $this->getDetail($id);
                unset($data['author_detail']);
                if (is_array($data)) {
                    $data['page_title'] = $update_value;
                    if ($this->nodeUpdate($data)) return true;
                    else return false;
                }
            break;
            
            case 'browser_title':
                $data = $this->getDetail($id);
                unset($data['author_detail']);
                if (is_array($data)) {
                    $data['browser_title'] = $update_value;
                    if ($this->nodeUpdate($data)) return true;
                    else return false;
                }
            break;

            case 'description':
                $data = $this->getDetail($id);
                unset($data['author_detail']);
                if (is_array($data)) {
                    $data['description'] = $update_value;
                    if ($this->nodeUpdate($data)) return true;
                    else return false;
                }
            break;
            
            case 'keywords':
                $data = $this->getDetail($id);
                unset($data['author_detail']);
                if (is_array($data)) {
                    $data['keywords'] = $update_value;
                    if ($this->nodeUpdate($data)) return true;
                    else return false;
                }
            break;
        }
    }
    
    /**
     * get taxonomy relation
     * @returns array taxonomy IDs only
     */
     
    function getTaxonomyForNode($node_id) {
    
        if (!is_numeric($node_id)) return false;
        
        $node_detail = $this->detail($node_id); //don't need to call full nodeDetail
        
        if (ONYX_ECOMMERCE) $controller =  $node_detail['node_controller'];
        else $controller = 'default';
        
        switch ($controller) {

            case 'recipe':
                require_once('models/ecommerce/ecommerce_recipe_taxonomy.php');
                $Taxonomy = new ecommerce_recipe_taxonomy();
                $relations = $Taxonomy->getRelationsToRecipe($node_detail['content']);
            break;

            case 'product':
                require_once('models/ecommerce/ecommerce_product_taxonomy.php');
                $Taxonomy = new ecommerce_product_taxonomy();
                $relations = $Taxonomy->getRelationsToProduct($node_detail['content']);
            break;

            case 'store':
                require_once('models/ecommerce/ecommerce_store_taxonomy.php');
                $Taxonomy = new ecommerce_store_taxonomy();
                $relations = $Taxonomy->getRelationsToStore($node_detail['content']);
            break;

            case 'default':
            default:
                require_once('models/common/common_node_taxonomy.php');
                $Taxonomy = new common_node_taxonomy();
                $relations = $Taxonomy->getRelationsToNode($node_id);
            break;
        }
        
        return $relations;
    }
    
    
    /**
     * get archive
     */
     
    public function getBlogArticleArchive($blog_node_id, $published = 1, $date_part = 'year') {
    
        if (!is_numeric($blog_node_id)) return false;
        if (!is_numeric($published)) return false;
        
        if ($date_part == 'year-month') $date_part = "date_part('year', created), date_part('month', created)";
        else $date_part = "date_part('year', created)";
        
        /**
         * query 
         */
         
        $sql = "
            SELECT DISTINCT( $date_part ) AS date_part, count(id) 
            FROM common_node 
            WHERE node_group = 'page' AND node_controller = 'news' AND parent = $blog_node_id  AND publish = $published
            GROUP BY date_part
            ORDER BY date_part DESC";
    
        /**
         * execute
         */
        
        if ($result = $this->executeSql($sql)) {
        
            return $result;
            
        } else {
            
            return false;
        
        }
        
    }
    
    /**
     * get categories
     */
     
    public function getArticlesCategories($blog_node_id, $published_articles_only = 1, $sort_by = 'common_taxonomy_tree.priority DESC, common_taxonomy_label.title ASC', $article_type = 'news') {
    
        if (!is_numeric($blog_node_id)) return false;
        if (!is_numeric($published_articles_only)) return false;
        
        /**
         * query 
         */
         
        $sql = "
            SELECT  common_node_taxonomy.taxonomy_tree_id, common_taxonomy_tree.parent AS parent_id, count(common_node.id), common_taxonomy_label.title, common_taxonomy_label.description, common_taxonomy_label.publish
            FROM common_node 
LEFT OUTER JOIN common_node_taxonomy ON (common_node.id = common_node_taxonomy.node_id)
LEFT OUTER JOIN common_taxonomy_tree ON (common_node_taxonomy.taxonomy_tree_id = common_taxonomy_tree.id)
LEFT OUTER JOIN common_taxonomy_label ON (common_taxonomy_tree.label_id = common_taxonomy_label.id)
            WHERE common_node.node_group = 'page' AND common_node.node_controller = '$article_type' AND common_node.parent = $blog_node_id AND common_node.publish = $published_articles_only
            GROUP BY common_node_taxonomy.taxonomy_tree_id, common_taxonomy_label.title, common_taxonomy_label.description, common_taxonomy_tree.parent, common_taxonomy_tree.priority, common_taxonomy_label.publish
            ORDER BY $sort_by";
    
        /**
         * execute
         */
        
        if ($result = $this->executeSql($sql)) {
        
            return $result;
            
        } else {
            
            return false;
        
        }
        
    }
    
    /**
     * getRelatedTaxonomy
     * @returns taxonomy with labels
     */

    public function getRelatedTaxonomy($node_id) {

        if (!is_numeric($node_id)) return false;

        require_once('models/common/common_node_taxonomy.php');
        $Taxonomy = new common_node_taxonomy();

        $related_taxonomy_ids = $this->getTaxonomyForNode($node_id);

        $related_taxonomy = array();

        if (is_array($related_taxonomy_ids)) {

            foreach ($related_taxonomy_ids as $item_id) {
                $related_taxonomy[] = $Taxonomy->getLabel($item_id);
            }

        }

        return $related_taxonomy;
    }
    
    /**
     * get comment count
     */
     
    public function getCommentCount($node_group = 'page', $node_controller = 'news', $public = 1) {

        /**
         * validate input
         */
         
        if (!in_array($node_group, array('page', 'layout', 'content'))) return false;
        if (!in_array($node_controller, array('news', 'component', 'RTE'))) return false;
        if (!is_numeric($public)) return false;
        
        /**
         * create SQL
         */
         
        $sql = "
            SELECT common_node.id, count(common_comment.id)
            FROM common_node 
            LEFT OUTER JOIN common_comment ON  (common_comment.node_id = common_node.id)
            WHERE common_node.node_group = '$node_group' AND common_node.node_controller = '$node_controller'
                AND common_comment.publish = $public
            GROUP BY common_node.id";
        
        /**
         * execute
         */
         
        if ($result = $this->executeSql($sql)) {
        
            /**
             * reformat
             */
            
            if (is_array($result)) {
                
                foreach ($result as $item) {
                    
                    $formated_result[$item['id']] = $item['count']; 
                }
            
                $result = $formated_result;
            }
            
            /**
             * return
             */
             
            return $result;
            
        } else {
            
            return false;
        
        }
    }
    
    /**
     * getTeaserImageForNodeId
     * alias for getImageForNodeId
     *
     * @param integer $node_id
     * @param string $role
     * (teaser, background, feature, etc)
     *
     * @return array
     */
     
    public function getTeaserImageForNodeId($node_id, $role = 'teaser') {
        
        return $this->getImageForNodeId($node_id, $role);
        
    }
    
    /**
     * getImageForNodeId
     * If not available, try parent pages
     *
     * @param integer $node_id
     * @param string $role
     * (teaser, background, feature, etc)
     *
     * @return array
     */
     
    public function getImageForNodeId($node_id, $role = 'teaser') {
        
        $node_detail = $this->detail($node_id); //don't need to call full nodeDetail
        
        // recognise different image table if ecommerce is enabled
        if (ONYX_ECOMMERCE) $node_controller = $node_detail['node_controller'];
        else $node_controller = 'default';
        
        switch ($node_controller) {
            
            case 'recipe':
                require_once('models/ecommerce/ecommerce_recipe_image.php');
                $Image = new ecommerce_recipe_image();
                
                // change node ID
                $image_node_id = $node_detail['content'];
                
            break;
            
            case 'product':
                require_once('models/ecommerce/ecommerce_product_image.php');
                $Image = new ecommerce_product_image();
                
                // change node ID
                $image_node_id = $node_detail['content'];
                
            break;
            
            case 'store':
                require_once('models/ecommerce/ecommerce_store_image.php');
                $Image = new ecommerce_store_image();
                
                // change node ID
                $image_node_id = $node_detail['content'];
                
            break;
            
            case 'default':
            default:
                require_once('models/common/common_image.php');
                $Image = new common_image();
                
                // node_id in common_node is direct reference to common_node.id
                $image_node_id = $node_id;
            break;
        }
         
        if (is_numeric($image_node_id)) $image_detail = $Image->getImageForNodeId($image_node_id, $role);
        
        /* try parent recursively */
        if (!$image_detail && is_numeric($node_detail['parent'])) {
            
            $image_detail = $this->getImageForNodeId($node_detail['parent'], $role);

        }
        
        return $image_detail;
    }

    /**
     * getFilesForNodeId
     */
     
    public function getFilesForNodeId($node_id, $role = false) {
        
        require_once('models/common/common_image.php');
        $Image = new common_image();

        return $Image->listFiles($node_id, $role);
        
    }
    
    /**
     * incrementShareCounter
     */
     
    public function incrementShareCounter($node_id) {
        
        if (!is_numeric($node_id)) return false;
        
        $sql = "UPDATE common_node SET share_counter = share_counter + 1 WHERE id = $node_id";
        
        return $this->executeSql($sql);
        
    }
    
    /**
     * getIdMap
     */
     
    public function getIdMap() {
    
        $id_map = array();
        
        foreach ($this->conf as $key=>$val) {
        
            if (preg_match("/^id_map/", $key)) {
                $k = preg_replace("/^id_map-/", "", $key);
                $id_map[$k] = $val;
            }
        
        }
    
        return $id_map;
    }
    
    /**
     * getAuthorStats
     */
     
    public function getAuthorStats($customer_id) {
        
        if (!is_numeric($customer_id)) return false;
        
        $stats = array();
        
        // number of all nodes:
        $stats['total'] = $this->count("customer_id = $customer_id");
        
        // number of all pages created
        $stats['page'] = $this->count("customer_id = $customer_id AND node_group = 'page'");
        
        // number of all layouts created
        $stats['layout'] = $this->count("customer_id = $customer_id AND node_group = 'layout'");
        
        // number of all content created
        $stats['content'] = $this->count("customer_id = $customer_id AND node_group = 'content'");
        
        // number of pages created with description
        $stats['page_description'] = $this->count("customer_id = $customer_id AND node_group = 'page' AND description != ''");
        
        // number of pages type 'news' (blog) created
        $stats['news'] = $this->count("customer_id = $customer_id AND node_group = 'page' AND node_controller = 'news'");
        
        // number of pages type 'recipe' created
        $stats['recipe'] = $this->count("customer_id = $customer_id AND node_group = 'page' AND node_controller = 'recipe'");
        
        // number of pages type 'product' created
        $stats['product'] = $this->count("customer_id = $customer_id AND node_group = 'page' AND node_controller = 'product'");
        
        // number of pages type 'store' created
        $stats['product'] = $this->count("customer_id = $customer_id AND node_group = 'page' AND node_controller = 'store'");
        
        return $stats;
        
    }

    /**
     * getCustomerIdForLastModified
     *
     * @param integer $node_id
     * @return integer|bool $customer_id or FALSE on fail
     */
     
    public function getCustomerIdForLastModified($node_id) {
    
        if (!is_numeric($node_id)) return false;
        
        require_once('models/common/common_revision.php');
        $Revision = new common_revision();
                
        $revision_list = $Revision->listing("object = 'common_node' AND node_id = $node_id", 'id DESC', '0,1');
        
        if (is_array($revision_list)) {
            
            $customer_id = $revision_list[0]['customer_id'];
        
        } else {
            
            $node_detail = $this->detail($node_id);
            $customer_id = $node_detail['customer_id'];
            
        }
        
        return $customer_id;
        
    }
    
    /**
     * getPreviewToken
     */
     
    public function getPreviewToken($node_data) {
        
        $preview_token = md5($node_data['created']);
        
        return $preview_token;
        
    }
    
    /**
     * verifyPreviewToken
     */
     
    public function verifyPreviewToken($node_data, $preview_token) {
        
        $correct_preview_token = $this->getPreviewToken($node_data);
        
        if ($correct_preview_token == $preview_token) return true;
        else return false;
        
    }
    
    /**
     * isInBin
     *
     * @param integer $node_id
     * @param array $path
     * @return bool
     */
    
    public function isInBin($node_id, $path = false) {
        
        if (!is_array($path)) $path = $this->getFullPathDetailForBreadcrumb($node_id);
        $top_level_page_id = $path[0]['id'];
        
        if ($top_level_page_id == $this->conf['id_map-bin']) return true;
        else return false;
        
    }
    
    /**
     * getCurrentNewsSectionId
     */
     
    public function getCurrentNewsSectionId() {
                
        /**
         * find active section and set as section_id if it matches any news section
         */
         
        $news_section_ids = $this->getListOfNewsSectionIds();
        $parent_page_id = $_SESSION['active_pages'][0];
        //msg($parent_page_id);
        foreach ($news_section_ids as $item) {
            
            $news_section_id = $item == $parent_page_id ? $item : null;
            
        }
        
        if (is_numeric($news_section_id)) return $news_section_id;
        else return false;
        
    }
    
    /**
     * getListOfNewsSectionIds
     * @return array
     *
     */
    
    public function getListOfNewsSectionIds() {
        
        return $this->getListOfNewsSectionIdsFromContent();
        
    }
    
    /**
     * list all pages which have child node type news
     * @return array
     *
     */
    
    public function getListOfNewsSectionIdsFromContent() {
        
        $list = [];
        
        /**
         * SQL query
         */
        $sql = "
            SELECT DISTINCT
            common_node.parent
            FROM common_node
            WHERE node_controller = 'news' and parent != {$this->conf['id_map-bin']}
            ORDER BY parent
            ";
        
        /**
         * execute SQL
         */
         
        if ($result = $this->executeSql($sql)) {
            
            foreach ($result as $item) {
                $id = (int) $item['parent'];
                $list[$id] = $id;
            }

            return $list;
            
        } else {
            
            return $list;
        
        }

        /**
         * return array
         */
         
        return $list;
        
    }
    
    /**
     * list all id_map-blog* configuration options
     * traditional way used pre v 1.8, maybe useful for installation where we'll need more control
     * on where can and where cannot be created news articles
     *
     * @return array
     *
     */
    
    public function getListOfBlogSectionIdsFromConfiguration() {
        
        require_once('models/common/common_configuration.php');
        $Common_Configuration = new common_configuration();
        
        $conf = $Common_Configuration->listing("object = 'common_node' AND property LIKE 'id_map-blog%'");
        
        if (is_array($conf) && count($conf) > 0) {
            
            foreach ($conf as $item) {
                $id = (int) $item['value'];
                $list[$id] = $id;
            }
            
        } else {
        
            $default_blog_node_id = (int) $this->conf['id_map-blog'];
    
            $list = array($default_blog_node_id => $default_blog_node_id);
        
        }
        
        return $list;
        
    }

    /**
     * recursivelly duplicate node and its contens
     */

    public function duplicateNode($original_node_id, $new_parent_id = false, $new_priority = false, $new_parent_container = false)
    {
        require_once('models/common/common_image.php');
        require_once('models/common/common_node_taxonomy.php');

        $this->Image = new common_image();
        $this->Taxonomy = new common_node_taxonomy();

        // read original node
        $original_node_data = $this->detail($original_node_id);
        
        // copy and modify
        $new_node_data = $original_node_data;
        $new_node_data['title'] = "{$new_node_data['title']} (copy)";
        $new_node_data['created'] = $new_node_data['modified'] = date('c');
        $new_node_data['customer_id'] = (int) Onyx_Bo_Authentication::getInstance()->getUserId();
        if (is_numeric($new_priority)) $new_node_data['priority'] = $new_priority;
        if ($new_node_data['uri_title'] != '') $new_node_data['uri_title'] = "{$new_node_data['uri_title']}-copy";
        if ($new_parent_id > 0) $new_node_data['parent'] = $new_parent_id;
        if (is_numeric($new_parent_container)) $new_node_data['parent_container'] = $new_parent_container;
        else {
            //top level element can be forced to be unpublished via common_node.conf option
            if ($this->conf['unpublish_on_duplicate']) $new_node_data['publish'] = 0;
        }
        unset($new_node_data['id']);

        // insert as new
        $new_node_id = $this->nodeInsert($new_node_data);
        if (!is_numeric($new_node_id)) {
            msg("node_duplicate: Cannot create copy of node ID $original_node_id", 'error');
            return false;
        }

        // read related images
        $original_images = $this->Image->listing("node_id = $original_node_id");

        // duplicate images
        if (is_array($original_images)) {
            foreach ($original_images as $image) {
                $new_image = $image;
                $new_image['node_id'] = $new_node_id;
                $new_image['modified'] = date('c');
                $new_image['customer_id'] = (int) Onyx_Bo_Authentication::getInstance()->getUserId();
                unset($new_image['id']);
                $image_id = $this->Image->insert($new_image);
            }
        }

        // read taxonomy relations
        $original_categories = $this->Taxonomy->listing("node_id = $original_node_id");

        // duplicate taxonomy relations
        if (is_array($original_categories)) {
            foreach ($original_categories as $category) {
                $new_category = $category;
                $new_category['node_id'] = $new_node_id;
                unset($new_category['id']);
                $category_id = $this->Taxonomy->insert($new_category);
            }
        }

        // read and duplicate nested nodes, but skip page nodes
        $nested_nodes = $this->listing("parent = $original_node_id");
        if (is_array($nested_nodes)) {
            foreach ($nested_nodes as $nested_node) {
                if ($nested_node['node_group'] != 'page')
                    $this->duplicateNode($nested_node['id'], $new_node_id);
            }
        }

        return $new_node_id;
    }

    /**
     * getUsedContentTypes
     * 
     * @return array
     */

    public function getUsedContentTypes() {

        $list = [];
        
        /**
         * SQL query
         */
        $sql = "
            SELECT
            DISTINCT ON (node_controller) node_controller, id 
            FROM common_node
            WHERE node_group = 'content'
            AND parent != {$this->conf['id_map-bin']}
            AND publish = 1
            AND node_controller != 'component'
            ORDER BY node_controller, id ASC
            ";
        
        /**
         * execute SQL
         */
         
        if ($result = $this->executeSql($sql)) {

            return $result;
            
        } else {
            
            return $list;
        
        }

    }

    public function getNodesByController($node_controller) {

        $list = [];
        
        /**
         * SQL query
         */

        $sql = "
            SELECT *
            FROM common_node
            WHERE node_group = 'content'
            AND parent != {$this->conf['id_map-bin']}
            AND node_controller = '{$node_controller}'
            ORDER BY modified DESC, id DESC
            ";
        /**
         * execute SQL
         */

        if ($result = $this->executeSql($sql)) {

            return $result;
            
        } else {
            
            return $list;
        
        }

    }
}
