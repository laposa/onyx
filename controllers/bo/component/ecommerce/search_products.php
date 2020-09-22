<?php
/** 
 * Copyright (c) 2006-2011 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
  *
  */

class Onyx_Controller_Bo_Component_Ecommerce_Search_Products extends Onyx_Controller {
    
    /**
     * main action
     */
     
    public function mainAction() {
    
        if (isset($_GET['search'])) {
            require_once('models/ecommerce/ecommerce_product.php');
        
            $Product = new ecommerce_product();
            $result = $Product->search($_GET['search']['query']);
            
            $added = array();
            foreach ($result as $r) {
                if (!in_array($r['id'], $added)) {
                    if ($r['publish'] == 0) $r['class'] = 'notpublic';
                    $r['name_safe'] = addslashes(htmlspecialchars($r['name'], ENT_QUOTES, 'UTF-8'));
                    $this->tpl->assign('RESULT', $r);
                    $this->tpl->parse('content.result.item');
                    $added[] = $r['id'];
                }
            }
            
            $this->tpl->parse('content.result');
        }

        return true;
    }
}
