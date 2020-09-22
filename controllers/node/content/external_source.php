<?php
/**
 * TESTING PROTOTYPE
 *
 * Copyright (c) 2007-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/node/content/default.php');

class Onxshop_Controller_Node_Content_External_Source extends Onxshop_Controller_Node_Content_Default {

    /**
     * main action
     */
     
    public function mainAction() {      
        
        // set transparent cache
        //$rss->cache_dir = ONXSHOP_PROJECT_DIR . 'var/tmp/'; 
        //$rss->cache_time = 3600; // one hour
    
        require_once('models/common/common_node.php');
        
        $Node = new common_node();
        
        $node_data = $Node->nodeDetail($this->GET['id']);
        
        if ($this->GET['link']) $url = base64_decode($this->GET['link']);
        else $url = trim($node_data['component']['url']);
        
        msg("Opening $url", 'ok', 2);
        
        $wget_url = base64_encode($url);
        
        $_Onxshop_Request = new Onxshop_Request("component/wget&url=$wget_url");
        $source = $_Onxshop_Request->getContent();
        
        $source = translateLinks("", $source);
        
        //$source = preg_replace("/.*\<body\>(.*)\<\/body\>.*/i", '\\1', $source);
        
        //grab with pregmatch
        $source = preg_replace("/[\r\n ]{1,}/"," ",$source);
        preg_match("/<body>(?!body)(.*)<\/body>/mi", $source, $match);
        
        $source = $match[1];
        
        //grab by XML
        /*
        //init simple XML
        require_once('lib/simplexml44/class/IsterXmlSimpleXMLImpl.php');
        $SimpleXML = new IsterXmlSimpleXMLImpl;
        
        // load some source file
        if ($doc  = $SimpleXML->load_string($source)) {
            
            $component['content'] = '';
            
            if (is_object($doc->html->body)) {
                foreach( $doc->html->body->children() as $child ) {
                    $component['content'] .= $child->asXML();
                }
            } else {
                msg("Source is not valid XHTML", 'error');
            }
            
            $this->tpl->assign("COMPONENT", $component);    
        } else {
            msg ("Could not open '$url'", 'error');
        }
        */
        
        $this->tpl->assign("RESULT", $source);
        
        $this->tpl->assign('NODE', $node_data);
        
        if ($node_data['display_title'])  $this->tpl->parse('content.title');

        return true;
    }
    
        
    function translateLinksCallback($matches) {
        msg("Encoding {$matches[1]}", 'ok', 3);
        $url_encode = base64_encode($matches[1]);
        $url = "href=\"?link=$url_encode\"";
        return $url;
    }
        
    function translateLinks($url, $html) {
        /*
        href="" ''
        no javascript
        src="" ''
        and href=\'index.php\'
        */
        $html = preg_replace_callback("/href=[\"\'](?!JavaScript)(.*)[\"\']/i", 'translateLinksCallback', $html);
            
        //$html = preg_replace_callback("/action=[\"\'](?!JavaScript)(.*)[\"\']/i","create_function('$matches', 'return action=\"$url\\1\"')",$html);
        //$abs_html = preg_replace( "/(?<!href=\")((http|ftp)+(s)?:\/\/[^<>\s]+)/i", "href=\"$url\\0\"", $html );
        return $html;
    }

}
