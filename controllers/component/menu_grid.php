<?php
/** 
 * Copyright (c) 2015 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('controllers/component/menu.php');

class Onyx_Controller_Component_Menu_Grid extends Onyx_Controller_Component_Menu {
    
    /**
     * parseItem
     */
     
    public function parseItem($item)
    {
        $image_width = $this->GET['image_width'];
        $image_height = $this->GET['image_height'];
        $image_fill = $this->GET['image_fill'];
        
        $_Onyx_Request = new Onyx_Request("component/teaser_grid~target_node_id={$item['id']}:image_width=$image_width:image_height=$image_height:image_fill=$image_fill~");
        $item['teaser_content'] = $_Onyx_Request->getContent();

        return parent::parseItem($item);
    }

}
