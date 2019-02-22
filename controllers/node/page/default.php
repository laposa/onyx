<?php
/**
 * Copyright (c) 2008-2018 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/node/default.php');

class Onxshop_Controller_Node_Page_Default extends Onxshop_Controller_Node_Default {

    /**
     * main action
     */
     
    public function mainAction() {
        
        $this->processContainers();
        $this->processPage();

        return true;
    }
    
    /**
     * process page
     */
     
    public function processPage() {
    
        if (!is_numeric($this->GET['id'])) return false;
        
        /**
         * get node detail
         */
         
        require_once('models/common/common_node.php');
        $this->Node = new common_node();
        $this->node_data = $this->Node->nodeDetail($this->GET['id']);
        
        /**
         * prepare titles
         */
         
        if (trim($this->node_data['page_title']) == '') $this->node_data['page_title'] = $this->node_data['title']; // page title is also used component/page_header, this will be effective only if page_title is directly in page template
        if (trim($this->node_data['browser_title']) == '') $this->node_data['browser_title'] = $this->node_data['page_title'];
        
        /**
         * fallback on options to global configuration
         */
         
        if (!isset($this->node_data['display_title'])) $this->node_data['display_title'] = $GLOBALS['onxshop_conf']['global']['display_title'];
        if (!isset($this->node_data['display_secondary_navigation'])) $this->node_data['display_secondary_navigation'] = $GLOBALS['onxshop_conf']['global']['display_secondary_navigation'];
        
        /**
         * get related_taxonomy
         */
        
        $related_taxonomy = $this->getNodeRelatedTaxonomy($this->node_data);
        
        /**
         * create taxonomy class
         */
         
        $this->node_data['taxonomy_class'] = $this->createTaxonomyClass($related_taxonomy);
        
        /**
         * create hierarchy CSS class
         */
         
        $this->node_data['hierarchy_class'] = $this->createHierarchyClass($_SESSION['full_path']);
        
        /**
         * save node_controller, page css_class, current node id, breadcrumb and taxonomy_class into registry to be used in sys/(x)html* as body class
         */
        
        $node_controller_css_class = preg_replace('/_/', '-', $this->node_data['node_controller']);
        
        $body_css_class = "$node_controller_css_class {$this->node_data['css_class']} {$this->node_data['taxonomy_class']} node-id-{$this->GET['id']} {$this->node_data['hierarchy_class']}";
        
        $this->saveBodyCssClass($body_css_class);

        /**
         * save node_id to registry
         */
            
        Zend_Registry::set('node_id', $this->GET['id']);
        
        /**
         * assign to template
         */
         
        $this->tpl->assign("NODE", $this->node_data);
        
        /**
         * load related image with role 'background'
         */
         
        $image = $this->Node->getImageForNodeId($this->node_data['id'], 'background');
        $this->tpl->assign("IMAGE", $image);
        
        /**
         * process open graph tags
         */
         
        $this->processOpenGraph($this->node_data);
        
        /**
         * display page header
         */
         
        if ($this->node_data['display_title'])  {
            $_Onxshop_Request = new Onxshop_Request("component/page_header~id={$this->node_data['id']}~");
            $this->tpl->assign('PAGE_HEADER', $_Onxshop_Request->getContent());
            $this->tpl->parse('content.page_header'); // for templates having page header directly within the page template
            $this->tpl->parse('content.title'); // for templates using simple title block same way as layouts and content
        }
        
        /**
         * display secondary navigation
         */
         
        if ($this->node_data['display_secondary_navigation'] == 1) {
        
            $first_page_id = $this->Node->getFirstParentPage($_SESSION['active_pages']);
            $_Onxshop_Request = new Onxshop_Request("component/menu~level=0:expand_all=0:display_strapline=1:id={$first_page_id}:open={$this->node_data['id']}~");
            $this->tpl->assign('SECONDARY_NAVIGATION', $_Onxshop_Request->getContent());
            $this->tpl->parse('content.secondary_navigation');
        }
        
        /**
         * standard return value
         */
         
        return true;
    }
    
    /**
     * hook before parsing
     */
     
    public function parseContentTagsBeforeHook() {
        
        /**
         * set active pages
         */
        $this->GET['node_id'] = $this->GET['id'];
        $this->setActivePages();
        
        return true;
    }
    
    
    /**
     * set active pages
     */
         
    public function setActivePages() {
        
        require_once('models/common/common_node.php');
        $this->Node = new common_node();
        
        $_SESSION['active_pages'] = $this->Node->getActivePages($this->GET['id']);
        $_SESSION['full_path'] = $this->Node->getFullPath($this->GET['id']);
        
    }
    
    /**
     * get taxonomy related to node
     */
     
    public function getNodeRelatedTaxonomy($node_data) {
        
        if (!is_array($node_data)) return false;
        
        require_once('models/common/common_node.php');
        $this->Node = new common_node();
        $related_taxonomy = $this->Node->getRelatedTaxonomy($node_data['id']);
        
        return $related_taxonomy;
    }
    
    /**
     * createTaxonomyClass from $related_taxonomy array
     */
     
    public static function createTaxonomyClass($related_taxonomy) {
    
        if (!is_array($related_taxonomy)) return false;
        
        /**
         * create taxonomy_class from related_taxonomy
         */
        
        $taxonomy_class = '';
        
        foreach ($related_taxonomy as $t_item) {
            if (is_numeric($t_item['id'])) $taxonomy_class .= "t{$t_item['id']} ";
        }
        
        return $taxonomy_class;
        
    }
    
    /**
     * createHierarchyClass
     */
    
    public static function createHierarchyClass($full_path) {
        
        if (!is_array($full_path)) return false;
        
        // remove first item (active page)
        array_shift($full_path);
        
        $css_class = '';
        
        foreach ($full_path as $item) {
        
            $css_class = "$css_class parent-node-id-$item";
        
        }
        
        return $css_class;
        
    }
    
    /**
     * saveBodyCssClass
     */
     
    public function saveBodyCssClass($body_css_class) {
        
        if (Zend_Registry::isRegistered('body_css_class')) {
        
            Zend_Registry::set('body_css_class', $body_css_class . ' ' . Zend_Registry::get('body_css_class'));
        
        } else {
        
            Zend_Registry::set('body_css_class', $body_css_class);
        
        }
        
    }

    /**
     * Process Open Graph meta tags (if app is active)
     */
    public function processOpenGraph($node_data) {

        /**
         * opengraph image
         */
         
        if ($opengraph_image = $this->getOpenGraphImage($node_data['id'], $node_data['content'])) {
            
            $this->tpl->assign('OPENGRAPH_IMAGE', $opengraph_image);
            $this->tpl->parse('head.open_graph.image');
            
        }

        $this->tpl->parse('head.open_graph');

    }   

    /**
     * getOpenGraphImage
     */
     
    public function getOpenGraphImage($node_id, $content = false) {
        
        return $this->Node->getTeaserImageForNodeId($node_id, 'opengraph');
        
    }
    
}
