<?php
/** 
 * Page builder from content tags
 * shouldn't be used on any project at the moment 
 *
 * Copyright (c) 2006-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Bo_Page_Builder extends Onxshop_Controller {

    /**
     * main action
     */
     
    public function mainAction() {
    
        $node_group = $this->GET['node_group'];
        $node_controller = $this->GET['node_controller'];
        $parent = $this->GET['parent'];
        
        $_Onxshop_Request = new Onxshop_Request("node/{$node_group}/{$node_controller}~id={$parent}~");
        $template = $_Onxshop_Request->tpl->filecontents;
        
        $tags = $this->findContainerTags($template);
        
        if (is_array($tags[1])) {
        
            foreach ($tags[1] as $id=>$container_id) {
        
                $node['title'] = $tags[2][$id] . $container_id;
                $node['node_controller'] = $tags[3][$id];
                $node['node_group'] = $tags[2][$id];
                $node['parent'] = $parent;
                $node['parent_container'] = $container_id;
                $_POST['node'] = $node;
                $_Onxshop_Request1 = new Onxshop_Request("bo/component/node_add@blank&parent={$parent}&dontforward=1");
            }
        }

        return true;
    }
}

