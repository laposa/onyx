<?php
/** 
 * Copyright (c) 2009-2020 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 * TODO: this needs refactoring using DB fulltext search since we dropped Zend
 */

class Onyx_Controller_Component_Search_Result extends Onyx_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
        
        if (isset($this->GET['search_query'])) {
        
            require_once('models/common/common_node.php');
            require_once('models/common/common_uri_mapping.php');
            $this->Node = new common_node();
            $this->Uri = new common_uri_mapping();
            
            $query = $this->Uri->recodeUTF8ToAscii(trim(strip_tags($this->GET['search_query'])));
            $count = strlen($query);

            if ($count > 2) {

                $this->keywords = $this->getKeywords($query);

                //TODO: $search_query = search::parse(htmlentities($query), 'UTF-8');
                //TODO $hits = $index->find($search_query);

                try {
                    // try fuzzy search if keyword search does not return anything
                    if (count($hits) == 0) {
                        $query = $query . '~0.6';
                        //TODO: $search_query = search::parse(htmlentities($query), 'UTF-8');
                        //TODO $hits = $index->find($search_query);
                    }               
                } catch (Exception $e) {
                    $hits = array();
                }

                $result_items_show = 15;
                
                if (count($hits) > 0) {

                    $results = array();

                    foreach ($hits as $hit) {
                        
                        if (count($results) >= $result_items_show) break;

                        $doc = $hit->getDocument();
                        $uri = $hit->uri == '' ? '/' : $hit->uri; 

                        $node_id = $this->Node->getNodeIdFromSeoUri($uri);
                        if (!$node_id) continue;

                        $path = $this->Node->getFullPathDetailForBreadcrumb($node_id);
                        
                        // skip bin items
                        if ($this->Node->isInBin($node_id, $path)) continue;
                        
                        $page = end($path);

                        try {
                            $page['body'] = $doc->getFieldValue("body");
                            $page['path'] = $path;

                            $this->updatePageDetails($page);
                            $results[] = $page;

                        } catch (Exception $e) {
                            continue;
                        }

                    }

                    $results = $this->customSort($results);
                    $this->parseResults($results);
                
                }           

                $number_of_results = count($results);
                $this->tpl->assign('NUMBER_OF_RESULTS', $number_of_results);
                
                if ($number_of_results > 0) $this->tpl->parse('content.result');
                else $this->tpl->parse('content.empty_result');

            } else {
            
                msg("Please specify at least 3 characters", "error");
            
            }
        }

        return true;
    }

    /**
     * override in subclass to implement own sorting
     */
    protected function customSort($results)
    {
        return $results;
    }

    /**
     * parse result items (override to implement custom details)
     */
    protected function parseResults($results)
    {
        foreach ($results as $result) {
            
            if ($result['publish']) {
            
                $this->parseBreadcrumb($result['path']);

                $this->tpl->assign('RESULT', $result);
                if ($result['image']) $this->tpl->parse('content.result.item.image');
                $this->tpl->parse('content.result.item');
            
            }
        }
    }

    /**
     * load addional page info (excerpt and image)
     */
    protected function updatePageDetails(&$page)
    {
        $page['type_priority'] = 0;

        if (ONYX_ECOMMERCE && (substr($page['node_controller'], 0, 6) == 'recipe')) $this->getRecipeDetails($page);
        if (ONYX_ECOMMERCE && (substr($page['node_controller'], 0, 7) == 'product')) $this->getProductDetails($page);
        if (ONYX_ECOMMERCE && (substr($page['node_controller'], 0, 5) == 'store')) $this->getStoreDetails($page);
        
        $page['image'] = $this->Node->getTeaserImageForNodeId($page['id']);
        
        if (strlen($page['excerpt']) == 0) {

            if (strlen($page['description']) < 20) {

                $page['excerpt'] = $this->reduceDescription($page['body'], $this->keywords);
                $page['excerpt'] = $this->highlightKeywords($page['excerpt'], $this->keywords);

            } else {

                $page['excerpt'] = $page['description'];
            }

        }
        
        //use title as fallback if page title isn't available
        if (trim($page['page_title']) == '') $page['page_title'] = $page['title'];

    }

    /**
     * load recipe details
     */
    protected function getRecipeDetails(&$page)
    {
        $recipe_id = $page['content'];
        if (!is_numeric($recipe_id)) return false;

        require_once("models/ecommerce/ecommerce_recipe.php");
        $Recipe = new ecommerce_recipe();
        $recipe = $Recipe->detail($recipe_id);

        $page['excerpt'] = $this->highlightKeywords(strip_tags($recipe['description']), $this->keywords);

        $page['type_priority'] = 100;
        $page['priority'] = $recipe['priority'];

    }

    /**
     * load store details
     */
    protected function getStoreDetails(&$page)
    {
        $store_id = $page['content'];
        if (!is_numeric($store_id)) return false;

        require_once("models/ecommerce/ecommerce_store.php");
        $Store = new ecommerce_store();
        $store = $Store->detail($store_id);

        $excerpt = strip_tags($store['description']);
        if (strlen($store['description']) > 0) $excerpt .= "<br/>";
        if (strlen($store['address']) > 0) $excerpt .= nl2br($store['address']);
        if (strlen($store['opening_hours']) > 0) $excerpt .= "<br/><br/>" . nl2br($store['opening_hours']);
        $page['excerpt'] = $this->highlightKeywords($excerpt, $this->keywords);
        
        $page['type_priority'] = 200;
        $page['priority'] = $store['priority'];
    }

    /**
     * load product details
     */
    protected function getProductDetails(&$page)
    {
        $product_id = $page['content'];
        if (!is_numeric($product_id)) return false;

        require_once("models/ecommerce/ecommerce_product.php");
        $Product = new ecommerce_product();
        $product = $Product->detail($product_id);

        if (strlen($page['description']) > 0) $excerpt = $page['description'];
        else if (strlen($product['teaser']) > 0) $excerpt = $product['teaser'];
        else $excerpt = $product['description'];
        $page['excerpt'] = $this->highlightKeywords(strip_tags($excerpt), $this->keywords);

        $page['type_priority'] = 300;
        $page['priority'] = $product['priority'];
    }


    /**
     * display breadcrumb
     */
    protected function parseBreadcrumb(&$path)
    {
        foreach ($path as $i => $p) {
            if ($p['node_group'] == 'page' || $this->GET['type'] == 'taxonomy') {
                if ($p['page_title'] == '') $p['page_title'] = $p['title'];
                $this->tpl->assign('PATH', $p);
                if ($i == count($path) - 1) $this->tpl->parse('content.result.item.linklast');
                else $this->tpl->parse('content.result.item.link');
            }
        }
    }

    /**
     * get array of query keywords
     */
    protected function getKeywords($query)
    {
        $keywords = explode(" ", $query);
        foreach ($keywords as &$keyword) $keyword = trim($keyword);
        return $keywords;
    }


    /**
     * highlight keywords in given text using html strong tag
     */
    protected function highlightKeywords($text, $keywords)
    {
        foreach ($keywords as $keyword) {
            if (strlen($keyword) > 2) {
                $keyword = preg_quote($keyword, '/');
                $pattern = "/($keyword)/i";
                $text = preg_replace($pattern, "<span class=\"keyword_highlight\">$1</span>", $text);
            }
        }
        
        return $text;
    }

    /**
     * reduce given text to 200 characters
     * try to reduce it to the part where the first keyword is present
     */
    protected function reduceDescription($text, $keywords)
    {
        $text = preg_replace("/\s\s([\s]+)?/", " ", $text);

        foreach ($keywords as $keyword) {
            if (strlen($keyword) > 2) {
                $pos = stripos($text, $keyword);
                if ($pos !== FALSE) {
                    $start = max(0, $pos - 100);
                    $length = 200;
                    while ($start > 0 && $text[$start] != ' ') $start--; // whole words
                    while ($length < 250 && $text[$start + $length] != ' ') $length++; // whole words
                    $line = substr($text, $start, $length) . "&hellip;";
                    if (strlen($line) > 100) return $line;
                }
            }
        }

        return mb_substr($text, 0, 200) . "&hellip;";
    }
}
