<?php
/**
 * Rebuild search index for Zend Lucene using common_uri_mapping
 * Copyright (c) 2013-2015 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/component/search_index.php');
require_once('models/common/common_node.php');

class Onyx_Controller_Bo_Component_Search_Index_Build extends Onyx_Controller_Bo_Component_Search_Index {

    protected $profile;
    protected $index;
    protected $client;
    protected $pageList;

    
    /**
     * main action
     */
     
    public function mainAction() {

        set_time_limit(0);

        if (is_numeric($this->GET['node_id'])) $node_id = $this->GET['node_id'];
        else $node_id = false;
        
        $this->initializeData($node_id);
        $this->initializeIndex($node_id);
        $this->build();

        return true;
        
    }

    /**
     * Initialize data structures
     */
     
    protected function initializeData($node_id) {
        
        $this->profile = $this->getProfile();

        // load all pages
        $Node =  new common_node();
        $sql = "publish = 1 AND display_in_menu = 1 AND node_group = 'page' AND node_controller <> 'symbolic'";
        if ($node_id) $sql .= " AND id = $node_id";
        $this->pageList = $Node->listing($sql);

        // Initialize the HTTP client
        $this->client = new Zend_Http_Client();
        $this->client->setConfig(array(
            'maxredirects' => 0,
            'timeout' => 30
        ));
    }
        
    protected function initializeIndex($node_id) {

        if ($node_id) {

            try {

                // Open existing index
                $this->index = Zend_Search_Lucene::open($this->profile['path']);

                // Delete existing pages
                $uri = translateURL("page/$node_id");
                $hits = $this->index->find("uri:" . $uri);
                foreach ($hits as $hit) {
                    msg("Deleting page: $uri");
                    $this->index->delete($hit);
                }

            } catch(Exception $e) {

                // Create index
                $this->index = Zend_Search_Lucene::create($this->profile['path']);

            }

        } else {

            $this->index = Zend_Search_Lucene::create($this->profile['path']);

        }
            
    }

    /**
     * Loop through all URIs
     * 
     * @return void
     * @access public
     */
     
    public function build() {

        // Process the queue
        $i = 0;
        foreach ($this->pageList as $page) {

            $uri = translateURL("page/{$page['id']}");

            try {
                
                /**
                 * check if customised template for indexing exists
                 * this is DEPRECATED approach how to customise indexable content, use getExcludes() instead
                 * remember that you need also to create controller for the template
                 */
                if (file_exists(ONYX_PROJECT_DIR . "templates/node/page/{$page['node_controller']}_indexable.html")) {
                    $toFetch = "request/node/page/{$page['node_controller']}_indexable~id={$page['id']}~";
                } else {
                    $toFetch = "request/node~id={$page['id']}~";
                }

                msg("Fetching page {$page['id']}: $uri using $toFetch");

                $this->client->setUri($this->profile['uri'] . $toFetch);
                $response = $this->client->request();
                
                if ($response->isSuccessful() && !$response->isRedirect() && !$response->isError()) {
                    
                    $response_body = $this->filterHtmlDocument($response->getBody());
                    
                    $this->index($uri, Zend_Search_Lucene_Document_Html::loadHTML($response_body, true));
                }
                    
            } catch(Exception $e) {
                msg("HTTP fetch exception: " . $e->getMessage());
            }

            $i++;
            // if ($i == 10) break;

        }

        // Optimize index.
        $this->indexOptimize();
    }

    /**
     * Index a URI
     *
     * @param string $uri
     * @param string $body
     * @return array
     * @access protected
     */
     
    protected function index($uri, $doc) {

        msg("Indexing document $uri");
        
        // Add some information
        $doc->addField(Zend_Search_Lucene_Field::Keyword('uri', $uri));

        // Add the document to the index
        $this->index->addDocument($doc);
        $this->index->commit();
        
    }
    
    
    /**
     * filterHtmlDocument
     */
     
    public function filterHtmlDocument($html_document) {
        
        $excludes = $this->getExcludes();
        
        if ($excludes != '') {
        
            require_once('lib/Zend/Dom/Query.php');
            
            $dom = new Zend_Dom_Query($html_document);
            $items = $dom->query($excludes);
                
            msg("Found " . count($items) . " elements matching search_index_exclude_selector");
                
            foreach($items as $item) {
                    
                $item->parentNode->removeChild($item);
            
            }
        
            $html_document = $dom->getDocument();
        }
        
        return $html_document;
        
    }
    
    /**
     * getExcludes
     */
     
    public function getExcludes() {
        
        $excludes = $GLOBALS['onyx_conf']['global']['search_index_exclude_selector']; // CSS selector, i.e. body.product div.rowBottom
        
        return $excludes;
        
    }
    
}
