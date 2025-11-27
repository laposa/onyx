<?php
/**
 * class ecommerce_recipe
 *
 * Copyright (c) 2013-2021 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class ecommerce_recipe extends Onyx_Model {

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
    var $instructions;

    /**
     * @access public
     */
    var $video_url;

    /**
     * @access public
     */
    var $serving_people;
    
    /**
     * @access public
     */
    var $preparation_time;
    
    /**
     * @access public
     *
     * we also user ready_time = preparation_time + cooking_time
     */
    var $cooking_time;
    
    /**
     * @access public
     */
    var $priority;
    
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
    var $other_data;
    var $_cache_recipe_in_node;
    var $authors;

    public common_taxonomy $Taxonomy;
    
    var $_metaData = array(
        'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
        'title'=>array('label' => '', 'validation'=>'string', 'required'=>true),
        'description'=>array('label' => '', 'validation'=>'xhtml', 'required'=>false),
        'instructions'=>array('label' => '', 'validation'=>'xhtml', 'required'=>false),
        'video_url'=>array('label' => '', 'validation'=>'string', 'required'=>false),
        'serving_people'=>array('label' => '', 'validation'=>'int', 'required'=>true),
        'preparation_time'=>array('label' => '', 'validation'=>'int', 'required'=>false),
        'cooking_time'=>array('label' => '', 'validation'=>'int', 'required'=>false),
        'priority'=>array('label' => '', 'validation'=>'int', 'required'=>false),
        'created'=>array('label' => '', 'validation'=>'datetime', 'required'=>false),
        'modified'=>array('label' => '', 'validation'=>'datetime', 'required'=>false),
        'publish'=>array('label' => '', 'validation'=>'int', 'required'=>false),
        'other_data'=>array('label' => '', 'validation'=>'string', 'required'=>false)
    );
    
    /**
     * create table sql
     */
     
    private function getCreateTableSql() {
    
        $sql = "
CREATE TABLE ecommerce_recipe (
    id serial PRIMARY KEY NOT NULL,
    title character varying(255),
    description text,
    instructions text,
    video_url text,
    serving_people integer,
    preparation_time integer,
    cooking_time integer,
    priority integer,
    created timestamp without time zone,
    modified timestamp without time zone DEFAULT now(),
    publish smallint,
    other_data text
);
    ";
        
        return $sql;
    }
    
    /**
     * init configuration
     */
     
    static function initConfiguration()
    {
    
        if (array_key_exists('ecommerce_recipe', $GLOBALS['onyx_conf'])) $conf = $GLOBALS['onyx_conf']['ecommerce_recipe'];
        else $conf = array();
        
        if (!isset($conf['taxonomy_tree_id']) || !is_numeric($conf['taxonomy_tree_id'])) $conf['taxonomy_tree_id'] = 0; // set value to force getUsedTaxonomy using only this taxonomy parent
        
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
     * get recipe images
     */

    public function getRecipeImages($recipe_id) {

        if (!is_numeric($recipe_id)) return false;

        require_once('models/ecommerce/ecommerce_recipe_image.php');
        $Image = new ecommerce_recipe_image();

        $image_list = $Image->listFiles($recipe_id);

        return $image_list;
        
    }

    /**
     * insert recipe
     */
    function insertRecipe($data)
    {
        $data['priority'] = 0;
        $data['publish'] = 0;
        $data['created'] = date('c');
        $data['serving_people'] = 4;

        // handle other_data
        if (is_array($data['other_data'])) $data['other_data'] = serialize($data['other_data']);
        
        if ($id = $this->insert($data)) {
            return $id;
        } else {
            return false;
        }
    }

    /**
     * update recipe
     */
    function updateRecipe($data)
    {
        // set values
        if (!isset($data['publish'])) $data['publish'] = 0;
        $data['modified'] = date('c');
            
        // make sure values are int
        $data['serving_people'] = (int)$data['serving_people'];
        $data['preparation_time'] = (int)$data['preparation_time'];
        $data['cooking_time'] = (int)$data['cooking_time'];
            
        // handle other_data
        $data['other_data'] = serialize($data['other_data'] ?? '');

        if ($id = $this->update($data)) {
        
            // update node info (if exists)
            $recipe_homepage = $this->getRecipeHomepage($id);
        
            if (is_array($recipe_homepage) && count($recipe_homepage) > 0) {
            
                $recipe_homepage['publish'] = $data['publish'];
                
                require_once('models/common/common_node.php');
                $Node = new common_node();
                
                $Node->nodeUpdate($recipe_homepage);
                
            }
            
            return $id;
        
        } else {
        
            return false;
        
        }
    }

    /**
     * prepareRecipeSql
     */
    private function prepareRecipeSql($keywords, $ready_time, $taxonomy_id, $product_variety_sku, $publish, $count_only = false)
    {
        $select = "";
        $keywordsWhere = "";
        $where = "";

        if ($keywords) {
            $keywords = pg_escape_string($keywords);
            $select = ", ts_rank_cd(search_vector, websearch_to_tsquery('english', '" . $keywords . "')) AS rank,
                word_similarity(title, '" . $keywords . "') AS sim ";
            $keywordsWhere = " AND (search_vector @@ websearch_to_tsquery('english', '" . $keywords . "')
                OR word_similarity(title, '" . $keywords . "') > 0.5 ) ";
        }

        if ($publish) {
            $where .= " AND r.publish = 1 ";
        }

        // product SKU
        if (strlen(trim($product_variety_sku)) > 0) {
            $product_variety_sku_array = explode(",", $product_variety_sku);
            $product_variety_sku = array();
            foreach ($product_variety_sku_array as $sku) {
                $product_variety_sku[] = "'" . pg_escape_string($sku) . "'";
            }
            $product_variety_sku = implode(",", $product_variety_sku);
            if (count($product_variety_sku) > 0) {
                $product_variety_ids = "SELECT id FROM ecommerce_product_variety WHERE ecommerce_product_variety.sku IN ($product_variety_sku)";
                $recipe_ids = "SELECT recipe_id FROM ecommerce_recipe_ingredients WHERE product_variety_id = ALL ($product_variety_ids)";
                $where .= " AND r.id IN ($recipe_ids)";
            }
        }

        //taxonomy
        if (is_numeric($taxonomy_id) && $taxonomy_id > 0) $where .= " AND r.id IN (SELECT node_id FROM ecommerce_recipe_taxonomy WHERE taxonomy_tree_id = $taxonomy_id)";

        //time
        if (is_numeric($ready_time) && $ready_time > 0) $where .= " AND (r.preparation_time + r.cooking_time) <= $ready_time";

        if ($count_only) {
            $select = "count(*)";
        } else {
            $select = "* {$select}";
        }

        $sql = "
            WITH 
            -- aggregate all product names for recipe ingredients
            recipe_ingredients AS (
                SELECT
                    ri.recipe_id,
                    string_agg(DISTINCT p.name, ' ') AS ingredient_names
                FROM ecommerce_recipe_ingredients ri
                LEFT JOIN ecommerce_product_variety pv ON ri.product_variety_id = pv.id
                LEFT JOIN ecommerce_product p ON pv.product_id = p.id
                GROUP BY ri.recipe_id
            ),

            -- create tsvector with weighted fields
            recipes_with_vector AS (
                SELECT 
                    r.*,
                    setweight(to_tsvector('english', coalesce(r.title, '')), 'A') ||
                    setweight(to_tsvector('english', coalesce(r.description, '')), 'B') ||
                    setweight(to_tsvector('english', coalesce(r.instructions, '')), 'C') ||
                    setweight(to_tsvector('english', coalesce(ri.ingredient_names, '')), 'B') AS search_vector
                FROM ecommerce_recipe r
                LEFT JOIN recipe_ingredients ri ON r.id = ri.recipe_id
                WHERE 1=1 $where
            )

            SELECT $select
            FROM recipes_with_vector
            WHERE 1=1 $keywordsWhere
        ";   

        return $sql;
    }

    /**
     * Get filtered recipe list
     * 
     * @param  string $keywords            Search keywords (looking though title, description, instructions and ingredients)
     * @param  string $ready_time          Maximum ready time (cooking + preparation time)
     * @param  string $taxonomy_id         Recipe category
     * @param  string $product_variety_sku Filter by product SKU(s)
     * @param  string $limit_per_page      Page Limit
     * @param  string $limit_from          Page Limit
     * @param  string $order_by            Ordering
     * @param  string $order_dir           Ordering
     * @param  boolean $publish            Publish filter
     * @return array
     */
    function getFilteredRecipeList($keywords = false, $ready_time = false, $taxonomy_id = false, $product_variety_sku = false, $limit_per_page = false, $limit_from = false, $order_by = false, $order_dir = false, $publish = 1)
    {
        /**
         * initialise
         */
         
        require_once('models/ecommerce/ecommerce_recipe_review.php');
        $Review = new ecommerce_recipe_review();
        
        /**
         * prepare SQL query
         */
         
        $sql = $this->prepareRecipeSql($keywords, $ready_time, $taxonomy_id, $product_variety_sku, $publish);

        // limits
        if (!is_numeric($limit_from)) $limit_from = 0;
        if (!is_numeric($limit_per_page)) $limit_per_page = 25;

        // order
        if (in_array($order_by, array('title', 'created', 'modified', 'priority', 'share_counter'))) {
            $order = "$order_by";
            if ($order_dir == 'DESC') $order .= " DESC";
            else $order .= " ASC";
        } else {
            $order = "priority DESC, id DESC";
            if ($keywords) {
                $order = "rank DESC, sim DESC, " . $order;
            }
        }

        // sql
        $sql = "$sql " .
            " ORDER BY $order" .
            " LIMIT $limit_per_page OFFSET $limit_from";

        // list
        $records = $this->executeSql($sql);

        if (is_array($records)) {

            require_once('models/ecommerce/ecommerce_recipe_image.php');
            $Image = new ecommerce_recipe_image();

            require_once('models/common/common_node.php');
            $Node = new common_node();

            $recipe_pages = $Node->listing("node_group = 'page' AND node_controller = 'recipe' AND content ~ '[0-9]+' AND publish = 1");

            foreach ($records as $i => $item) {
                
                // images
                $images = $Image->listFiles($item['id']);
                if (count($images) > 0) {
                    $records[$i]['image']['src'] = $images[0]['src'];
                    $records[$i]['image']['title'] = $images[0]['title'];
                }
                
                // taxonomy
                $taxonomy = $this->getTaxonomyForRecipe($item['id']);
                $records[$i]['taxonomy'] = implode(',', $taxonomy);
                
                // recipe homepage
                foreach ($recipe_pages as $recipe_page) {
                    if ($recipe_page['content'] == $item['id']) {
                        $records[$i]['page'] = $recipe_page;
                    }
                }
                
                // load review
                $records[$i]['review'] = $Review->getRating($item['id']);
                
                // total cooking time
                $records[$i]['total_cooking_time'] = $records[$i]['preparation_time'] + $records[$i]['cooking_time'];
            }

            return $records;
        }

        return array();

    }

    /**
     * getFilteredRecipeCount
     */

    function getFilteredRecipeCount($keywords = false, $ready_time = false, $taxonomy_id = false, $product_variety_sku = false, $publish = 1)
    {
        $sql = $this->prepareRecipeSql($keywords, $ready_time, $taxonomy_id, $product_variety_sku, $publish, true);
        $count = $this->executeSql($sql);
        return (int)$count[0]['count'];
    }

    /**
     * Find recipe homepage
     * use simple cache
     */
     
    function getRecipeHomepage($recipe_id) {
    
        require_once('models/common/common_node.php');
        $Node = new common_node();
        
        if (is_numeric($recipe_id)) {
            if (!is_array($this->_cache_recipe_in_node)) $this->_cache_recipe_in_node = $Node->listing("node_group = 'page' AND node_controller = 'recipe'", 'id ASC');
            foreach ($this->_cache_recipe_in_node as $p) {
                //return first in list - it's homepage
                if ($p['content'] == $recipe_id) return $p;
            }
            //return true is search complete, but no page found
            return true;
        } else {
            return false;
        }

    }


    /**
     * find recipe in node
     */
     
    function findRecipeInNode($recipe_id) {
    
        require_once('models/common/common_node.php');
        $Node = new common_node();
        
        if (is_numeric($recipe_id)) {
            $current = $Node->listing("node_group = 'page' AND node_controller = 'recipe' AND content = '{$recipe_id}'", 'id ASC');
            return $current;
        } else {
            msg("ecommerce_recipe.findProductInNode: recipe id is not numeric", 'error');
            return false;
        }
    }


    /**
     * getRecipeListForTaxonomy
     *
     * list recipes for given taxonomy_ids
     * each item contains
     *    - main image details as 'image' field
     *    - page details as 'page' field
     *
     * @param array $taxonomy_ids
     * @param string $sort_by
     * @param string $sort_direction
     * @param int $limit_from
     * @param int $limit_per_page
     * @param string $image_role
     * @param bool $conjunction - whether included recipes should have all given $taxonomy_ids (true) or any of given $taxonomy_ids (false)
     * @param int|string $publish_status - integer to limit by publishing status, string, i.e. 'all' for no restriction by publishing status
     * @return array
     */
    function getRecipeListForTaxonomy($taxonomy_ids, $sort_by = 'created', $sort_direction = 'DESC', $limit_from = false, $limit_per_page = false, $image_role = 'teaser', $conjunction = true, $publish_status = 1)
    {
    
        /**
         * input filter
         */

        // sorting
        if (!in_array($sort_by, array('title', 'created', 'modified', 'priority', 'share_counter'))) $sort_by = 'created';
        if (!in_array($sort_direction, array('DESC', 'ASC'))) $sort_direction = 'DESC';
        $order_by = " ORDER BY $sort_by $sort_direction";
        
        // limit
        if  (!is_numeric($limit_from)) $limit_from = false;
        if (!is_numeric($limit_per_page)) $limit_per_page = false;
        
        // allow to use limit_per_page without providing limit_from
        if (is_numeric($limit_per_page) && $limit_from === false) $limit_from = 0;
        
        if (is_numeric($limit_from) && is_numeric($limit_per_page)) {
            $limit = " LIMIT $limit_per_page OFFSET $limit_from";
        } else {
            $limit = '';
        }
        
        /**
         * initialise
         */
         
        require_once('models/common/common_node.php');
        require_once('models/ecommerce/ecommerce_recipe_taxonomy.php');
        require_once('models/ecommerce/ecommerce_recipe_image.php');
        require_once('models/ecommerce/ecommerce_recipe_review.php');

        $Node = new common_node();
        $Image = new ecommerce_recipe_image();
        $Taxonomy = new ecommerce_recipe_taxonomy();
        $Review = new ecommerce_recipe_review();
        
        /**
         * recipes list
         */
         
        $recipes = array();

        $where = "";

        if (is_array($taxonomy_ids) && count($taxonomy_ids) > 0) {
        
            $id_list = implode(",", $taxonomy_ids);

            if ($conjunction) {
                $count = count($taxonomy_ids);
                $where = "AND ecommerce_recipe.id IN (
                    SELECT ecommerce_recipe.id
                    FROM ecommerce_recipe
                    INNER JOIN ecommerce_recipe_taxonomy ON ecommerce_recipe_taxonomy.node_id = ecommerce_recipe.id
                    WHERE ecommerce_recipe_taxonomy.taxonomy_tree_id IN ($id_list)
                    GROUP BY ecommerce_recipe.id
                    HAVING count(DISTINCT ecommerce_recipe_taxonomy.taxonomy_tree_id) = $count
                )";
            } else {
                $where = "AND ecommerce_recipe.id IN (SELECT node_id FROM ecommerce_recipe_taxonomy WHERE taxonomy_tree_id IN ($id_list))";
            }

        }
        
        // $publish_status
        if (is_numeric($publish_status)) {
            $where_node_publish = " AND common_node.publish = {$publish_status}";
            $where_recipe_publish = " AND ecommerce_recipe.publish = {$publish_status}";
        }
        
        $sql = "SELECT ecommerce_recipe.*, common_node.share_counter,
                (SELECT array_to_string(array_agg(taxonomy.taxonomy_tree_id), ',') FROM ecommerce_recipe_taxonomy taxonomy WHERE taxonomy.node_id = ecommerce_recipe.id) AS taxonomy
            FROM ecommerce_recipe
            INNER JOIN common_node ON (common_node.node_group = 'page' 
                AND common_node.node_controller = 'recipe'
                AND common_node.content = ecommerce_recipe.id::varchar
                $where_node_publish)
            WHERE 1=1 $where_recipe_publish $where
            $order_by
            $limit";

        $recipes = $this->executeSql($sql);
        
        // return empty array if nothing is found
        if (!is_array($recipes)) return array();
        
        $recipe_pages = $Node->listing("node_group = 'page' AND node_controller = 'recipe' AND content ~ '[0-9]+' AND publish = 1");

        foreach ($recipe_pages as $recipe_page) {
            foreach ($recipes as &$recipe) {
                if ($recipe_page['content'] == $recipe['id']) {

                    // assign page
                    $recipe['page'] = $recipe_page;

                    // load images
                    $image_list = $Image->listFiles($recipe['id'], $image_role);
                    // if empty list, get any image, without specification of image_role
                    if (is_array($image_list) && count($image_list) == 0) {
                        $image_list = $Image->listFiles($recipe['id']);
                    }
                    // return only one image
                    $recipe['image']  = isset($image_list[0]) ? $image_list[0] : array();
                    
                    // load review
                    $recipe['review'] = $Review->getRating($recipe['id']);

                    // get author name
                    $recipe['author_name'] = $this->getRecipeAuthorName($recipe);
                    
                }
            }
        }

        
        return $recipes;

    }

    /**
     * getRecipeAuthorName
     * @param array $item
     * @return string author name
     */

    public function getRecipeAuthorName($item)
	{
        require_once('models/common/common_taxonomy.php');

        $this->Taxonomy = new common_taxonomy();

        if (empty($this->authors)) $this->authors = $this->Taxonomy->getChildren(96);

		$ids = explode(",", $item['taxonomy']);

		foreach ($ids as $id) {
			foreach ($this->authors as $taxonomy) {
				if ($taxonomy['id'] == $id) return  $taxonomy['label']['title'];
			}
		}

		return $GLOBALS['onyx_conf']['global']['title'];
	}

    /**
     * getRecipeCountForTaxonomy
     */

    function getRecipeCountForTaxonomy($taxonomy_ids) {

        require_once('models/ecommerce/ecommerce_recipe_taxonomy.php');
        $Taxonomy = new ecommerce_recipe_taxonomy();

        if (is_array($taxonomy_ids) && count($taxonomy_ids) > 0) {
        
            $taxonomy = $Taxonomy->listing("taxonomy_tree_id IN (" . implode(",", $taxonomy_ids) . ")");

            $recipe_ids = array();
            foreach ($taxonomy as $category) {
                $recipe_ids[] = $category['node_id'];
            }

            $where = "AND ecommerce_recipe.id IN (" . implode(",", $recipe_ids) . ")";

        } else {

            $where = "";
        }
        
        $sql = "SELECT count(ecommerce_recipe.id) AS count
            FROM ecommerce_recipe
            INNER JOIN common_node ON (common_node.node_group = 'page' 
                AND common_node.node_controller = 'recipe'
                AND common_node.content = ecommerce_recipe.id::varchar
                AND common_node.publish = 1)
            WHERE ecommerce_recipe.publish = 1 $where";
    
        $result = $this->executeSql($sql);
        return (int) $result[0]['count'];

    }

    
    /**
     * get taxonomy relation
     * @returns array taxonomy IDs only
     */
     
    static function getTaxonomyForRecipe($recipe_id) {
    
        if (!is_numeric($recipe_id)) return false;
        
        require_once('models/ecommerce/ecommerce_recipe_taxonomy.php');
        $Taxonomy = new ecommerce_recipe_taxonomy();
        
        $relations = $Taxonomy->getRelationsToRecipe($recipe_id);
        
        return $relations;
    }
    
    /**
     * getRelatedTaxonomy
     * @returns array full taxonomy details
     */
     
    public function getRelatedTaxonomy($recipe_id) {
        
        if (!is_numeric($recipe_id)) return false;
        
        require_once('models/common/common_taxonomy.php');
        $Taxonomy = new common_taxonomy();
        $related_taxonomy = $Taxonomy->getRelatedTaxonomy($recipe_id, 'ecommerce_recipe_taxonomy');
        
        return $related_taxonomy;
    }
    
    /**
     * getUsedTaxonomy
     * TODO: this should re-used or merged with /api/v1.3/recipe_category_list
     */
     
    public function getUsedTaxonomy() {
        
        // get categories from taxonomy
        require_once('models/common/common_taxonomy.php');
        $Taxonomy = new common_taxonomy;
        require_once('models/ecommerce/ecommerce_recipe_taxonomy.php');
        $RecipeTaxonomy = new ecommerce_recipe_taxonomy();
        
        //$this->conf['taxonomy_tree_id'] = 4;
        
        if ($this->conf['taxonomy_tree_id'] > 0) {
            $recipe_categories = $Taxonomy->getChildren($this->conf['taxonomy_tree_id']);
        } else {
            $recipe_categories = $RecipeTaxonomy->getUsedTaxonomyLabels();
            foreach ($recipe_categories as $i=>$item) {
                $recipe_categories[$i]['label'] = $item;
            }
        }
        
        //print_r($recipe_categories);
        /*
            0] => Array
        (
            [taxonomy_tree_id] => 247
            [id] => 247
            [title] => Fruit
            [description] => 
            [priority] => 220
            [publish] => 0
            [parent] => 214
        )
        
        
        [0] => Array
        (
            [id] => 9
            [label_id] => 9
            [parent] => 4
            [priority] => 40
            [publish] => 1
            [label] => Array
                (
                    [id] => 9
                    [title] => Cakes and Baking
                    [description] => 
                    [priority] => 0
                    [publish] => 1
                    [image] => Array
                        (
                            [0] => Array
                                (
                                    [id] => 43
                                    [src] => var/files/real-food/recipes/baking/baking-recipe.jpg
                                    [role] => main
                                    [node_id] => 9
                                    [title] => Baking recipe
                                    [description] => 
                                    [priority] => 0
                                    [modified] => 2016-01-05 09:28:36
                                    [author] => 0
                                    [content] => 
                                    [other_data] => 
                                    [link_to_node_id] => 
                                    [customer_id] => 22810
                                )

                        )

                )

        )

    [1]
            */
        return $recipe_categories;
        
    }
    
}
