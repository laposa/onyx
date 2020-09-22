<?php
/**
 * Crawl for Zend Lucene
 * Copyright (c) 2009-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/component/search_index.php');

class Onyx_Controller_Bo_Component_Search_Index_Crawl extends Onyx_Controller_Bo_Component_Search_Index {

    protected $profile;
    protected $index;
    protected $client;
    protected $queue;
    protected $uri;
    protected $uriParts;
    protected $uriProcessed;
    
    /**
     * main action
     */
     
    public function mainAction() {

        set_time_limit(0);
        
        $this->initializeCrawler();
        $this->runClawler();


        return true;
        
    }
    
    /**
     * Initialize the crawler
     *
     * @param array $profilePath
     * @return void
     * @access protected
     */
     
    protected function initializeCrawler() {
        
            $profile = $this->getProfile();

            // Initialize the HTTP client
            $this->client = new Zend_Http_Client();
            $this->client->setConfig(array(
                'maxredirects' => 0,
                'timeout' => 30
            ));
            
            // Enable UTF8 support for text analyzer
            // requires PCRE on and mbstring
            //Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8_CaseInsensitive());
            
            $this->uri = $profile['uri'];
            $this->uriParts = parse_url($this->uri);
            $this->uriProcessed = array();
            $this->queue = array();
            
            // Initialize the index
            $this->index = Zend_Search_Lucene::create($profile['path']);
            
    }
    
    
    
    /**
     * Process document links
     *
     * @param array $links
     * @return int
     * @access protected
     */
     
    protected function processLinks($links) {
        
        $counter = 0;
        
        foreach((array)$links as $link) {
            
            //make absolute if on the local server
            if ($link = $this->prepareLinkForCrawler($link)) {
                
                if(!in_array($link, $this->uriProcessed) && !in_array($link, $this->queue)) {
                    
                    if ($linkParts = $this->validateLinkForCrawl($link)) {
                        
                        if($linkParts['host'] == $this->uriParts['host'] && $linkParts['query'] == '') {
                            $this->queue[] = $link;
                            $counter++;
                        }
                    }
                    
                }
            }
        }
        
        return $counter;
        
    }
    

    /**
     * Run the crawler
     * 
     * @return void
     * @access public
     */
     
    public function runClawler() {
        
        // Crawl the first page
        $this->crawl($this->uri);
        
        // Process the queue
        do {
            msg('Queue length: '.count($this->queue), 'ok', 1);
            $this->crawl(array_shift($this->queue));
        } while(count($this->queue) > 0);
        
        // Optimize index.
        $this->indexOptimize();
    }
    
    /**
     * Crawl a URI
     *
     * @param string $uri
     * @return void
     * @access protected
     */
     
    protected function crawl($uri) {
        
        msg("Crawling: $uri");
        
        $this->uriProcessed[] = $uri;
        
        $uri_parts = parse_url($uri);
        
        $this->validateLinkForCrawl($uri);
        
        if ($this->validateLinkForCrawl($uri)) {
            
            // Retrieve the content
            $this->client->setUri($uri);
            
            try {
                
                $response = $this->client->request();
                
                if($response->isSuccessful() && !$response->isRedirect() && !$response->isError()) {
                    
                    //msg("Response status: ". $response->getStatus());
                    $this->index($uri_parts['path'], Zend_Search_Lucene_Document_Html::loadHTML($response->getBody(), true));
                }
                    
            } catch(Exception $e) {}
        }       
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
        
        // Extract the links
        $links = $doc->getLinks();
        msg('+ Extracting links', 'ok', 1);
        msg('+ '.count($links).' links extracted', 'ok', 1);
        
        // Process the links
        $queuedLinksCount = $this->processLinks($links);
        $disacardedLinksCount = count($links) - $queuedLinksCount;
        
        msg("+ $queuedLinksCount new link(s) added to the queue [$disacardedLinksCount link(s) discarded]");
        
    }
    
    /**
     * prepare link for crawler
     */
     
    public function prepareLinkForCrawler($link) {
        
        $uri_parts = parse_url($link);
        
        if (!($uri_parts['scheme']) || $uri_parts['scheme'] == 'http' || $uri_parts['scheme'] == 'https') {
            
            //make absolute
            $link = preg_match('/^https?:\/\//i', $link) ? $link : $this->uri . ltrim($link, '/');
            //remove anchors
            $link = preg_replace('/#.*$/', '', $link);
                
            return $link;
        } else {
            return false;
        }
        
    }
    
    
    /**
     * validate link for crawl
     */
     
    public function validateLinkForCrawl($link) {
        
        $uri_parts = parse_url($link);
        
        if ($uri_parts['scheme'] == 'http' || $uri_parts['scheme'] == 'https') {
            
            if (preg_match('/^[0-9a-z_\-\/\.]*$/i', $uri_parts['path'])) {
                
                if (!preg_match('/\.(jpg|jpeg|png|gif)$/i', $uri_parts['path'])) {
                    
                    return $uri_parts;
                } else {
                
                    msg("Link seems to be an image $link", 'error');
                    return false;
                }
            } else {
            
                msg("Invalid characters in $link", 'error');
                return false;
            }
        
        } else {
        
            msg("Found invalid scheme for crawl $link", 'error');
            return false;
        }
        
        
    }
}
