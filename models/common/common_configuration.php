<?php
/**
 * class common_configuration
 *
 * Copyright (c) 2009-2018 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class common_configuration extends Onyx_Model {

    /**
     * NOT NULL PRIMARY KEY
     * @access private
     */
    var $id;
    /**
     * NOT NULL REFERENCES common_node(id) ON UPDATE CASCADE ON DELETE CASCADE
     * @access private
     */
    var $node_id;
    /**
     * @access private
     */
    var $object;
    /**
     * @access private
     */
    var $property;
    /**
     * @access private
     */
    var $value;
    /**
     * @access private
     */
    var $description;
    /**
     * @access private
     */
    var $apply_to_children;


    var $_metaData = array(
        'id'=>array('label' => 'ID', 'validation'=>'int', 'required'=>true), 
        'node_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
        'object'=>array('label' => '', 'validation'=>'string', 'required'=>true),
        'property'=>array('label' => '', 'validation'=>'string', 'required'=>true),
        'value'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'description'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'apply_to_children'=>array('label' => '', 'validation'=>'int', 'required'=>false),
    );

    static $localCache = false;

    /**
     * create table sql
     * 
     * @return string
     * SQL command for table creating
     */
         
    private function getCreateTableSql() {
    
        $sql = "CREATE TABLE common_configuration ( 
            id serial NOT NULL PRIMARY KEY,
            node_id int NOT NULL DEFAULT 0 REFERENCES common_node(id) ON UPDATE CASCADE ON DELETE CASCADE,
            object varchar(255) ,
            property varchar(255) ,
            value text ,
            description text,
            apply_to_children smallint NULL DEFAULT '0'
        );";
        
        return $sql;
    }
    
    /**
     * get configuration
     * 
     * @param integer $node_id
     * ID of node
     * 0 for default configuration
     * 
     * @return array
     * configuration
     * 
     * @see getDefaultCoreValues
     */
    
    function getConfiguration($node_id = 0) {
    
        if (!is_numeric($node_id)) {
            msg("common_configuration.getConfiguration: node_id is not numeric", 'error');
            $node_id = 0;
        }

        /**
         * Cache configuration within a single HTTP request
         */
        if (!self::$localCache) {
            self::$localCache = array();
            $list = $this->listing();
            foreach ($list as $item) {
                self::$localCache[$item['node_id']][] = $item;
            }
        }

        $conf = array();
            
        if (is_array(self::$localCache[$node_id])) {
            foreach (self::$localCache[$node_id] as $c) {
                $conf[$c['object']][$c['property']] = $c['value'];
            }
        }

        /**
         * default core values (only for root node)
         */
        
        if ($node_id == 0) {
        
            $conf = $this->getDefaultCoreValues($conf);

        } else {

            /**
             * Try to inherit configuration from parent nodes
             */

            require_once('models/common/common_node.php');
            $Node = new common_node();

            $path = $Node->getFullPath($node_id);

            for ($i = 1; $i < count($path); $i++) {
                $parent_id = $path[$i];
                if (is_array(self::$localCache[$parent_id])) {
                    foreach (self::$localCache[$parent_id] as $c) {
                        if (!isset($conf[$c['object']][$c['property']]) && $c['apply_to_children'])
                            $conf[$c['object']][$c['property']] = $c['value'];
                    }
                }
            }

        }

        return $conf;
    }
    
    /**
     * get default core values
     *
     * values can be overwritten in common_configuration table as object named "global"
     * TODO: we should use object name "common_configuration" instead of "global"
     * 
     * @param array $conf
     * configuration
     * 
     * @return array
     * updated configuration
     */
     
    public function getDefaultCoreValues($conf) {
        
        if (!array_key_exists('title', $conf['global'])) $conf['global']['title'] = 'White Label';
        if (!array_key_exists('html_title_suffix', $conf['global'])) $conf['global']['html_title_suffix'] = 'White Label';
        if (!array_key_exists('locale', $conf['global'])) $conf['global']['locale'] = 'en_GB.UTF-8';
        if (!array_key_exists('default_currency', $conf['global'])) $conf['global']['default_currency'] = 'GBP';
        if (!array_key_exists('author_content', $conf['global'])) $conf['global']['author_content'] = 'White Label, http://example.com/';
        if (!array_key_exists('copyright', $conf['global'])) {
            $year = date('Y', time());
            $title = $conf['global']['title'];
            $conf['global']['copyright'] = "&copy; $year <span>$title</span>";
        }
        if (!array_key_exists('credit', $conf['global'])) $conf['global']['credit'] = '<a href="https://onxshop.com" title="eCommerce solution"><span>Powered by Onyx</span></a>';
        if (!array_key_exists('google_analytics', $conf['global'])) $conf['global']['google_analytics'] = '';
        if (!array_key_exists('css', $conf['global'])) $conf['global']['css'] = '';
        
        define('GLOBAL_DEFAULT_CURRENCY', $conf['global']['default_currency']);
        
        /**
         * default site template constants
         */
        //used only in node/site/default.html
        if (!array_key_exists('extra_head', $conf['global'])) $conf['global']['extra_head'] = '';
        if (!array_key_exists('extra_body_top', $conf['global'])) $conf['global']['extra_body_top'] = '';
        if (!array_key_exists('extra_body_bottom', $conf['global'])) $conf['global']['extra_body_bottom'] = '';
        //used only in node/site/default.php
        if (!array_key_exists('display_content_side', $conf['global'])) $conf['global']['display_content_side'] = 0;
        if (!array_key_exists('display_content_foot', $conf['global'])) $conf['global']['display_content_foot'] = 0;
        
        /**
         * default page constants (used in site as well)
         */
        if (!array_key_exists('display_secondary_navigation', $conf['global'])) $conf['global']['display_secondary_navigation'] = 0;
        
        
        /**
         * default node constants (valid for content,layout and page)
         */
        if (!array_key_exists('display_title', $conf['global'])) $conf['global']['display_title'] = 1;
        
        
        /**
         * product template constants
         */
        if (!array_key_exists('product_list_per_page', $conf['global'])) $conf['global']['product_list_per_page'] = 24; //should be dividable by 4, 3 and 2
        if (!array_key_exists('product_list_sorting', $conf['global'])) $conf['global']['product_list_sorting'] = 'created'; // popularity, price, name, created, priority
        if (!array_key_exists('product_list_mode', $conf['global'])) $conf['global']['product_list_mode'] = 'shelf'; //shelf, grid
        if (!array_key_exists('product_list_grid_columns', $conf['global'])) $conf['global']['product_list_grid_columns'] = 4;
        if (!array_key_exists('product_detail_image_width', $conf['global'])) $conf['global']['product_detail_image_width'] = 350;
        if (!array_key_exists('product_list_image_width', $conf['global'])) $conf['global']['product_list_image_width'] = 175; // for stack list use stack_list_image_width option
        //can be: gallery_smooth, gallery, simple_list
        if (!array_key_exists('product_image_gallery', $conf['global'])) $conf['global']['product_image_gallery'] = 'gallery_smooth';
        
        if (!array_key_exists('stack_list_image_width', $conf['global'])) $conf['global']['stack_list_image_width'] = 0;
        if (!array_key_exists('stack_list_image_height', $conf['global'])) $conf['global']['stack_list_image_height'] = 0;
        
        /**
         * set default address and name for emails (used for common_email, perhaps it should be moved there)
         */
        if (!array_key_exists('admin_email', $conf['global'])) $conf['global']['admin_email'] = 'test@onxshop.com';
        if ($conf['global']['admin_email_name'] == '') $conf['global']['admin_email_name'] = $conf['global']['title'];
        
        //addthis tracking configuration, by default uses shared Onyx profile
        if (!array_key_exists('addthis_profile', $conf['global'])) $conf['global']['addthis_profile'] = 'ra-51114b69066f0fe4';
        
        // CSS selector for elements we want to be ingnored by the internal search indexing
        if (!array_key_exists('search_index_exclude_selector', $conf['global'])) $conf['global']['search_index_exclude_selector'] = '';
        
        // taxonomy_tree_id of parent folder for province structure of a country
        if (!array_key_exists('province_taxonomy_tree_id', $conf['global'])) $conf['global']['province_taxonomy_tree_id'] = 53;
        
        return $conf;
    }
     
    
    /**
     * save configuration
     * 
     * @param string $object
     * name of object
     * 
     * @param string $property
     * name of property
     * 
     * @param string $value
     * value of saved object.property
     * 
     * @param integer $node_id
     * ID of node
     * 
     * TODO: keep id up to 1000 as reserved for system
     */

    function saveConfig($object, $property, $value, $node_id = 0) {
        
        //check
        if (!is_numeric($node_id)) {
            msg('common_configuration->saveConfig(): node_id is not numeric', 'error');
            return false;
        }
        
        //trim
        $object = trim($object);
        $property = trim($property);
        $value = trim($value);
        
        //try to find current value
        $cs = $this->listing("node_id = $node_id AND object = '$object' AND property = '$property'");
        if (count($cs) > 0) $conf_old = $cs[0];
        else $conf_old = array();

        
        $conf_new = $conf_old;
        
        //set values
        $conf_new['node_id'] = $node_id;
        $conf_new['object'] = $object;
        $conf_new['property'] = $property;
        $conf_new['value'] = $value;
        $conf_new['description'] = '';

        //compare and save only if values changed
        if ($this->checkIfValuesChanged($conf_old, $conf_new)) {
            
            // invalidate cache
            self::$localCache = false;
            
            // save (update or insert)
            if ($id = $this->save($conf_new)) {
            
                $conf_new['id'] = $id;
                
                // return ID
                return $id;
            
            } else {
                
                return false;
            
            }
            
        } else {
        
            msg("Property for {$conf_new['property']} in {$conf_new['object']} did not change");
            return false;
        }
    }
    
    /**
     * delete configuration variable by object/property
     * - this will delete all records with specific name
     * 
     * @param string $object
     * 
     * @param string $property
     * 
     * @return boolean
     */

    public function deleteRecords($object, $property) {

        if (strlen($object) == 0) return false;
        if (strlen($property) == 0) return false;

        $object = $this->db->quote($object);
        $property = $this->db->quote($property);

        $sql = "DELETE FROM common_configuration WHERE object = $object AND property = $property";
        
        $result =  $this->executeSql($sql);

        // invalidate cache
        self::$localCache = false;

        return $result;
    }
    
    /**
     * compare values
     * 
     * @param array $old
     * compared value
     * 
     * @param array $new
     * compared value
     * 
     * @return boolean
     * is this values equal
     * 
     */
     
    public function checkIfValuesChanged($old, $new) {

        if ($old['node_id'] != $new['node_id'] ||
            $old['object'] != $new['object'] ||
            $old['property'] != $new['property'] ||
            $old['value'] != $new['value'] ||
            $old['description'] != $new['description']) {
        
            return true;
        } else {
            
            return false;
        }
    }

}
