<?php
/**
 * Copyright (c) 2009-2014 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

require_once('models/ecommerce/ecommerce_promotion_type.php');

class Onxshop_Controller_Bo_Component_Ecommerce_Promotion_Filter extends Onxshop_Controller {

    /**
     * main action
     */
     
    public function mainAction() {

        /**
         * Store submited data to the SESSION
         */
        if (isset($_POST['voucher-filter'])) {
            $_SESSION['bo']['voucher-filter'] = $_POST['voucher-filter'];
            onxshopGoTo('/backoffice/marketing');
        } else {
            if (!isset($_SESSION['bo']['voucher-filter']['type'])) $_SESSION['bo']['voucher-filter']['type'] = 1;
        }

        $filter = $_SESSION['bo']['voucher-filter'];
        $this->tpl->assign('FILTER', $filter);
        
        $this->parseTypeSelect($filter['type']);
        $this->tpl->parse("content.form");

        return true;

    }

    /**
     * parseTypeSelect
     */
     
    protected function parseTypeSelect($selected_id)
    {
        $Type = new ecommerce_promotion_type();
        $records = $Type->listing();

        foreach ($records as $item) {
            if ($item['id'] == $selected_id) $item['selected'] = 'selected="selected"';
            $this->tpl->assign("ITEM", $item);
            $this->tpl->parse("content.form.type.item");
        }
        $this->tpl->parse("content.form.type");
    }

}

