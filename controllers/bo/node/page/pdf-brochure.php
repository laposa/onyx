<?php

require_once('controllers/bo/node/page/default.php');

class Onyx_Controller_Bo_Node_Page_Pdf_Brochure extends Onyx_Controller_Bo_Node_Page_Default
{

  /**
   * main action
   */

  public function mainAction()
  {
    
    parent::mainAction();
    
    if (is_array($_POST) && array_key_exists('manifest', $_POST)) {
      // save manifest submitted from fe_edit
      savePdfManifest($this->node_data['id'], $_POST['manifest']);
    } else {
      // TODO: Do i need these?
      $this->detail();
      $this->assign();
    }
    
    return true;
  }
}
