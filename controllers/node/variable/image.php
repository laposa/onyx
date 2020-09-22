<?php
/** 
 * Copyright (c) 2019 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/node/variable/default.php');

class Onxshop_Controller_Node_Variable_Image extends Onxshop_Controller_Node_Variable_Default {
    /**
     * main action
     */
     
    public function mainAction() {
        
      $node_id = $this->GET['id'];
      
      if (!is_numeric($node_id)) {
          msg('node/content/default: id not numeric', 'error');
          return false;
      }
      
      require_once('models/common/common_node.php');
      
      $this->Node = new common_node();
      
      /**
       * load related images
       */
       
      $image = $this->Node->getImageForNodeId($node_id);
      $this->tpl->assign("IMAGE", $image);

      return true;
  }
}
