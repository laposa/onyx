<?php
/**
 * Copyright (c) 2006-2019 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/bo/node/content/default.php');

class Onxshop_Controller_Bo_Node_Content_Image extends Onxshop_Controller_Bo_Node_Content_Default {
    /**
     * pre action
     */
     
    function pre() {
    
      if ($_POST['node']['component']['show_caption'] == 'on' || $_POST['node']['component']['show_caption'] == 1) $_POST['component']['show_caption'] = 1;
      else $_POST['node']['component']['show_caption'] = 0;
      
      return parent::pre();
  }
}

