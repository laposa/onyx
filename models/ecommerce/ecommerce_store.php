<?php
/**
 * class ecommerce_store
 *
 * Copyright (c) 2013-2019 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class ecommerce_store extends Onxshop_Model {

    /**
     * @access public
     */
    var $id;

    /**
     * @access public
     */
    var $title;
    
    /**
     * @access public
     */
    var $description;
    
    /**
     * @access public
     */
    var $address;

    /**
     * @access public
     */
    var $opening_hours;

    /**
     * @access public
     */
    var $telephone;
    
    /**
     * @access public
     */
    var $manager_name;
    
    /**
     * @access public
     */
    var $email;
    
    /**
     * @access public
     */
    var $url;
    
    /**
     * @access public
     */
    var $type_id;
    
    /**
     * @access public
     */
    var $coordinates_x;
    
    /**
     * @access public
     */
    var $coordinates_y;
    
    /**
     * @access public
     */
    var $latitude;
    
    /**
     * @access public
     */
    var $longitude;
    
    /**
     * @access public
     */
    var $created;
    
    /**
     * @access public
     */
    var $modified;
    
    /**
     * @access public
     */
    var $publish;
    
    /**
     * @access public
     */
    var $street_view_options;
    
    /**
     * @access public
     */
    var $other_data;
    
    /**
     * @access public
     */
    var $country_id;
    
    /**
     * @access public
     */
    var $address_name;
    
    /**
     * @access public
     */
    var $address_line_1;
    
    /**
     * @access public
     */
    var $address_line_2;
    
    /**
     * @access public
     */
    var $address_line_3;
    
    /**
     * @access public
     */
    var $address_city;
    
    /**
     * @access public
     */
    var $address_county;
    
    /**
     * @access public
     */
    var $address_post_code;
    
    /**
     * @access public
     */
    var $code;
    
    var $_metaData = array(
        'id'=>array('label' => '', 'validation' => 'int', 'required' => true),
        'title'=>array('label' => '', 'validation' => 'string', 'required' => true),
        'description'=>array('label' => '', 'validation' => 'string', 'required' => false),
        'address'=>array('label' => 'Full address as one input box', 'validation' => 'string', 'required' => false),
        'opening_hours'=>array('label' => '', 'validation' => 'string', 'required' => false),
        'telephone'=>array('label' => '', 'validation' => 'string', 'required' => false),
        'manager_name'=>array('label' => '', 'validation' => 'string', 'required' => false),
        'email'=>array('label' => '', 'validation' => 'email', 'required' => false),
        'url'=>array('label' => '', 'validation' => 'string', 'required' => false),
        'type_id'=>array('label' => '', 'validation' => 'int', 'required' => false),
        'coordinates_x'=>array('label' => '', 'validation' => 'int', 'required' => false),
        'coordinates_y'=>array('label' => '', 'validation' => 'int', 'required' => false),
        'latitude'=>array('label' => '', 'validation' => 'string', 'required' => false),
        'longitude'=>array('label' => '', 'validation' => 'string', 'required' => false),
        'created'=>array('label' => '', 'validation' => 'datetime', 'required' => false),
        'modified'=>array('label' => '', 'validation' => 'datetime', 'required' => false),
        'publish'=>array('label' => '', 'validation' => 'int', 'required' => false),
        'street_view_options'=>array('label' => '', 'validation' => 'string', 'required' => false),
        'other_data'=>array('label' => '', 'validation' => 'string', 'required' => false),
        'country_id'=>array('label' => 'Country', 'validation'=>'int', 'required'=>false),
        'address_name'=>array('label' => 'Name', 'validation'=>'string', 'required'=>false),
        'address_line_1'=>array('label' => 'Address line 1', 'validation'=>'string', 'required'=>false),
        'address_line_2'=>array('label' => 'Address line 2', 'validation'=>'string', 'required'=>false),
        'address_line_3'=>array('label' => 'Address line 3', 'validation'=>'string', 'required'=>false),
        'address_city'=>array('label' => 'City', 'validation'=>'string', 'required'=>false),
        'address_county'=>array('label' => 'County', 'validation'=>'string', 'required'=>false),
        'address_post_code'=>array('label' => 'Post code', 'validation'=>'string', 'required'=>false),
        'code'=>array('label' => 'Reference code', 'validation'=>'string', 'required'=>false)
    );
    
    /**
     * create table sql
     */
     
    private function getCreateTableSql() {
    
        $sql = "
CREATE TABLE ecommerce_store (
    id integer DEFAULT nextval('ecommerce_store_id_seq'::regclass) NOT NULL,
    title character varying(255),
    description text,
    address text,
    opening_hours text,
    telephone character varying(255),
    manager_name character varying(255),
    email character varying(255),
    url character varying(512),
    type_id REFERENCES ecommerce_store_type ON UPDATE CASCADE ON DELETE RESTRICT,
    coordinates_x integer,
    coordinates_y integer,
    latitude double precision,
    longitude double precision,
    created timestamp without time zone NOT NULL,
    modified timestamp without time zone NOT NULL,
    publish smallint DEFAULT 0 NOT NULL,
    street_view_options text,
    other_data text,
    country_id int REFERENCES international_country ON UPDATE CASCADE ON DELETE RESTRICT,
    address_name varchar(255),
    address_line_1 varchar(255),
    address_line_2 varchar(255),
    address_line_3 varchar(255),
    address_city varchar(255),
    address_county varchar(255),
    address_post_code varchar(255),
    code varchar(255)
);

CREATE INDEX ecommerce_store_title_idx ON ecommerce_store (title);
CREATE INDEX ecommerce_store_publish_idx ON ecommerce_store (publish);
CREATE INDEX ecommerce_store_type_id_idx ON ecommerce_store (type_id);
    ";
        
        return $sql;
    }
    
    /**
     * init configuration
     */
     
    static function initConfiguration()
    {
    
        if (array_key_exists('ecommerce_store', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['ecommerce_store'];
        else $conf = array();
        
        /**
         * default values
         */
         
        if (!$conf['latitude']) $conf['latitude'] = 53.344189;
        if (!$conf['longitude']) $conf['longitude'] = -6.264478;
        
        if (!$conf['default_store_url']) $conf['default_store_url'] = '/btq';

        return $conf;
    }

    /**
     * getDetail
     */
     
    public function getDetail($id) {
        
        $data = $this->detail($id);
        
        if (is_array($data)) {
            // handle other_data
            $data['other_data'] = unserialize($data['other_data']);
        }
        
        return $data;
        
    }
    
    /**
     * insert store
     */
    function insertStore($data)
    {
        $data['publish'] = 0;
        $data['created'] = date('c');
        $data['modified'] = date('c');

        if ($id = $this->insert($data)) {
            return $id;
        } else {
            return false;
        }
    }

    /**
     * updateStore
     * 
     * @param array $data
     * @return integer $id
     */

    public function storeUpdate($data) {
        
        $data['modified'] = date('c');
        $store_id = $this->update($data);

        if (array_key_exists('publish', $data)) {

            // update node publishing info (if node exists)
            $store_homepage = $this->getStoreHomepage($store_id);
            
            if (is_array($store_homepage) && count($store_homepage) > 0) {
                
                $store_homepage['publish'] = $data['publish'];
                
                require_once('models/common/common_node.php');
                $Node = new common_node();
                
                $Node->nodeUpdate($store_homepage);
                
            }
        }

        return $store_id;
    }
    /**
     * get filtered store list
     *
     */
    
    function getFilteredStoreList($taxonomy_id = false, $keyword = false, $type_id = 0, $order_by = false, $order_dir = false, $per_page = false, $from = false, $publish_only = false)
    {
        $where = $this->prepareStoreFilteringSql($taxonomy_id, $keyword, $type_id, $publish_only);

        // order
        if ($order_by == 'title' || $order_by == 'modified') $order = "$order_by";
        else $order = "title";
        if ($order_dir == 'DESC') $order .= " DESC";
        else $order .= " ASC";

        // limits
        if (is_numeric($from)) $limit = "$from";
        else $limit = "0";
        if (is_numeric($per_page)) $limit .= ",$per_page";
        else $limit = false;

        //$records = $this->listing($where, $order, $limit);

        /**
         * prepare limit query
         */
         
        if (preg_match('/[0-9]*,[0-9]*/', $limit)) {
            
            $limit = explode(',', $limit);
            $limit = " LIMIT {$limit[1]} OFFSET {$limit[0]}";
            
        } else {
            
            $limit = '';
            
        }
        
        /**
         * build SQL query
         */
         
        $sql = "SELECT ecommerce_store.*,
                (SELECT array_to_string(array_agg(taxonomy.taxonomy_tree_id), ',') FROM ecommerce_store_taxonomy taxonomy WHERE taxonomy.node_id = ecommerce_store.id) AS taxonomy
            FROM ecommerce_store
            WHERE $where
            ORDER BY $order
            $limit";
            
        $records = $this->executeSql($sql);
        
        if (is_array($records)) {

            require_once('models/ecommerce/ecommerce_store_image.php');
            $Image = new ecommerce_store_image();

            foreach ($records as $i => $item) {
                // add image
                $images = $Image->listFiles($item['id']);
                if (count($images) > 0) {
                    $records[$i]['image_src'] = $images[0]['src'];
                    $records[$i]['image_title'] = $images[0]['title'];
                }
                
                // help old installations with transtion from one address field to multiple fields
                if (trim($item['address']) == '') {
                    if ($item['address_name']) $records[$i]['address'] .= $item['address_name'] . ",\n";
                    if ($item['address_line_1']) $records[$i]['address'] .= $item['address_line_1'] . ",\n";
                    if ($item['address_line_2']) $records[$i]['address'] .= $item['address_line_2'] . ",\n";
                    if ($item['address_line_3']) $records[$i]['address'] .= $item['address_line_3'] . ",\n";
                    if ($item['address_city']) $records[$i]['address'] .= $item['address_city'] . ",\n";
                    if ($item['address_county']) $records[$i]['address'] .= $item['address_county'] . ",\n";
                    if ($item['address_post_code']) $records[$i]['address'] .= $item['address_post_code'] . ",\n";
                    
                    $records[$i]['address'] = preg_replace("/,$/", "", $records[$i]['address']);
                    
                }
            }

            return $records;
        }

        return array();
    }

    /**
     * get filtered store list
     *
     */
    
    function getFilteredStoreCount($taxonomy_id = false, $keyword = false, $type_id = 0, $publish_only = false)
    {
        $where = $this->prepareStoreFilteringSql($taxonomy_id, $keyword, $type_id, $publish_only);
        return $this->count($where);
    }

    /**
     * prepareStoreFilteringSql
     */

    private function prepareStoreFilteringSql($taxonomy_id, $keyword, $type_id, $publish_only)
    {
        $sql = '1 = 1';

        $keyword = pg_escape_string(trim($keyword));

        // keyword
        if (is_numeric($keyword)) {
            $sql .= " AND (ecommerce_store.id = {$keyword} OR ecommerce_store.code = '{$keyword}')";
        } else if ($keyword != '') $sql .= " AND (ecommerce_store.title ILIKE '%{$keyword}%' OR ecommerce_store.description ILIKE '%{$keyword}%' OR ecommerce_store.code ILIKE '%{$keyword}%' OR ecommerce_store.address ILIKE '%{$keyword}%' OR ecommerce_store.address_name ILIKE '%{$keyword}%' OR ecommerce_store.address_line_1 ILIKE '%{$keyword}%' OR ecommerce_store.address_line_2 ILIKE '%{$keyword}%' OR ecommerce_store.address_line_3 ILIKE '%{$keyword}%' OR ecommerce_store.address_city ILIKE '%{$keyword}%' OR ecommerce_store.address_county ILIKE '%{$keyword}%' OR ecommerce_store.address_post_code ILIKE '%{$keyword}%')";

        // type
        if (is_numeric($type_id) && $type_id > 0) $sql .= " AND ecommerce_store.type_id = $type_id";
        
        // taxonomy (allowed one single ID or multiple IDs, i.e. 1,2,3)
        if ((is_numeric($taxonomy_id) && $taxonomy_id > 0) || ($taxonomy_id != '' && !preg_match('/[^0-9,]/', $taxonomy_id))) {
            
            $taxonomy_method = "AND";
            
            if ($taxonomy_method == 'OR') {
            
                $sql .= " AND ecommerce_store.id IN (SELECT node_id FROM ecommerce_store_taxonomy WHERE taxonomy_tree_id IN ($taxonomy_id))";
            
            } else {
                
                $num = count(explode(',', $taxonomy_id));
                $sql .= " AND ecommerce_store.id IN (
                    SELECT node_id FROM (
                        SELECT node_id, count(node_id) AS c FROM ecommerce_store_taxonomy 
                        WHERE taxonomy_tree_id IN ($taxonomy_id) GROUP BY node_id
                    ) AS my WHERE c = $num
                )";
            }
            
        }
        
        // publish only 
        if ($publish_only) $sql .= " AND ecommerce_store.publish = 1";
        
        return $sql;
    }

    function getStoreImage($store_id) {

        require_once('models/ecommerce/ecommerce_store_image.php');
        $Image = new ecommerce_store_image();

        $images = $Image->listFiles($store_id);
        if (count($images) > 0) return $images[0]['src'];

        return false;
    }

    /**
     * Find store homepage
     * use simple cache
     */

    function getStoreHomepage($store_id) {

        require_once('models/common/common_node.php');
        $Node = new common_node();

        if (is_numeric($store_id)) {
            if (!is_array($this->_cache_store_in_node)) $this->_cache_store_in_node = $Node->listing("node_group = 'page' AND node_controller = 'store'", 'id ASC');
            foreach ($this->_cache_store_in_node as $p) {
                //return first in list - it's homepage
                if ($p['content'] == $store_id) return $p;
            }
            //return true is search complete, but no page found
            return true;
        } else {
            return false;
        }

    }


    /**
     * find store in node
     */
     
    function findStoreInNode($store_id) {
    
        require_once('models/common/common_node.php');
        $Node = new common_node();
        
        if (is_numeric($store_id)) {
            $current = $Node->listing("node_group = 'page' AND node_controller = 'store' AND content = '{$store_id}'", 'id ASC');
            return $current;
        } else {
            msg("ecommerce_store.findStoreInNode: store id is not numeric", 'error');
            return false;
        }
    }

    /**
     * find store by node_id
     */
     
    function findStoreByNode($node_id) {
    
        require_once('models/common/common_node.php');
        $Node = new common_node();
        
        if (is_numeric($node_id)) {

            $nodes = $Node->listing("node_group = 'page' AND node_controller = 'store' AND id = '{$node_id}'", 'id ASC');
            
            $store_id = $nodes[0]['content'];
            if (is_numeric($store_id) && $store_id > 0) return $this->detail($store_id);

        } else {
            msg("ecommerce_store.findStoreByNode: node id is not numeric", 'error');
        }

        return false;
    }


    /**
     * Find nearest store that has specific taxonomy_id
     * @param  int $store_id    Home store to which the distance is measured
     * @param  int $taxonomy_id Required facility taxnomy_id
     * @return int store_id
     */
    function findNearestStoreWithFacility($store_id, $taxonomy_id)
    {
        if (!is_numeric($store_id)) return false;
        if (!is_numeric($taxonomy_id)) return false;

        $list = $this->listing("id IN (SELECT node_id FROM ecommerce_store_taxonomy WHERE taxonomy_tree_id = $taxonomy_id)");
        $store = $this->detail($store_id);

        $nearest = 99999;

        foreach ($list as $item) {
            $distance = $this->distance($item['latitude'], $item['longitude'], $store['latitude'], $store['longitude']);
            if ($distance < $nearest) {
                $nearest = $distance;
                $store_id = $item['id'];
            }
        }

        return $store_id;
    }


    /**
     * Get distance between two points on sphere (in km)
     * http://en.wikipedia.org/wiki/Haversine_formula
     * 
     * @param  float $latitude1 First point latitude
     * @param  float $longitude1 First point longitude
     * @param  float $latitude2 Second point latitude
     * @param  float $longitude2 Second point longitude
     * @return float
     */
    function distance($latitude1, $longitude1, $latitude2, $longitude2) {
        
        if (!is_numeric($latitude1)) return false;
        if (!is_numeric($longitude1)) return false;
        if (!is_numeric($latitude2)) return false;
        if (!is_numeric($longitude2)) return false;
        
        $earth_radius = 6371;
     
        $dLat = deg2rad($latitude2 - $latitude1);
        $dLon = deg2rad($longitude2 - $longitude1);
     
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * asin(sqrt($a));
        $d = $earth_radius * $c;
     
        return $d;
    
    }
    
    /**
     * getRelatedTaxonomy
     */
     
    public function getRelatedTaxonomy($store_id) {
        
        if (!is_numeric($store_id)) return false;
        
        require_once('models/common/common_taxonomy.php');
        $Taxonomy = new common_taxonomy();
        $related_taxonomy = $Taxonomy->getRelatedTaxonomy($store_id, 'ecommerce_store_taxonomy');
        
        return $related_taxonomy;
    }

    /**
     * getRelatedTaxonomyIds
     */
     
    public function getRelatedTaxonomyIds($store_id) {
        
        if (!is_numeric($store_id)) return false;
        
        $related_taxonomy = $this->getRelatedTaxonomy($store_id);
        
        $taxonomy_ids = array();
        
        foreach ($related_taxonomy as $item) {
            
            $taxonomy_ids[] = $item['id'];
        
        }
        
        return $taxonomy_ids;
    }
    
    /**
     * getDataForNoticesReport
     */

    public function getDataForNoticesReport($date_from, $date_to)
    {

        // set to last 7 days if no dates given
        if (!isValidDate($date_from)) $date_from = date("Y-m-d", time() - 3600 * 24 * 7);
        if (!isValidDate($date_to)) $date_to = date("Y-m-d", time());

        // add quotes and escape
        $date_from = $this->db->quote($date_from);
        $date_to = $this->db->quote($date_to);

        $sql = "SELECT 
                notice.id AS id,
                notice.created AS created,
                notice.modified AS modified,
                notice.other_data AS other_data,
                notice.publish AS publish,
                store.id AS store_id,
                store.code AS store_code,
                store.title AS store_title,
                store.manager_name AS store_manager_name,
                store.email AS store_email
            FROM common_node AS notice
            LEFT JOIN common_node AS parent ON parent.id = notice.parent
            LEFT JOIN ecommerce_store AS store ON store.id = parent.content::int
            WHERE notice.node_controller = 'notice' AND notice.created BETWEEN $date_from AND $date_to
        ";

        $records = $this->executeSql($sql);

        $result = array();
        foreach ($records as $record) {

            $item = array();
            $data = unserialize($record['other_data']);

            $item['Web Store Id'] = $record['store_id'];
            $item['Store Code'] = $record['store_code'];
            $item['Store Title'] = $record['store_title'];
            $item['Store Manager'] = $record['store_manager_name'];
            $item['Store Email'] = $record['store_email'];
            $item['Notice Id'] = $record['id'];
            $item['Notice Created'] = $record['created'];
            $item['Is Published'] = $record['publish'] ? 'yes' : 'no';
            $item['Notice Text'] = $data['text'];
            $item['Visible From'] = $data['visible_from'];
            $item['Visible To'] = $data['visible_to'];
            $item['Image'] = $data['image'] ? 'yes' : 'no';

            $result[] = $item;
        }

        return $result;
    }

    /**
     * insert store to node
     */
    
    public function insertNewStoreToNode($store_id, $parent_id) {
    
        if (!is_numeric($store_id)) return false;
        if (!is_numeric($parent_id)) return false;
        
        require_once 'models/common/common_node.php';
        $Node = new common_node();
        
        /**
         * get store detail
         */
         
        $store_detail = $this->detail($store_id);
         
        /**
         * prepare node data
         */
         
        $store_node['title'] = $store_detail['title'];
        $store_node['parent'] = $parent_id;
        $store_node['parent_container'] = 0;
        $store_node['node_group'] = 'page';
        $store_node['node_controller'] = 'store';
        $store_node['content'] = $store_id;
        //$store_node['layout_style'] = $Node->conf['page_store_layout_style'];
        //this need to be updated on each store update
        $store_node['priority'] = $store_detail['priority'];
        $store_node['publish'] = $store_detail['publish'];

        /**
         * insert node
         */
         
        if ($store_homepage_node_id = $Node->nodeInsert($store_node)) {
            return $store_homepage_node_id;
        } else {
            msg("Can't add store to node.", 'error');
            return false;
        }
        
    }

}
