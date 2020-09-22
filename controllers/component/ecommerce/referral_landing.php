<?php
/** 
 * Copyright (c) 2012 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once "controllers/component/ecommerce/referral.php";

class Onxshop_Controller_Component_Ecommerce_Referral_Landing extends Onxshop_Controller_Component_Ecommerce_Referral {

    public function mainAction()
    {
        /**
         * initializace models
         */
        $this->Promotion = new ecommerce_promotion();
        $this->Promotion->setCacheable(false);
        $this->Customer = new client_customer();
        $this->Customer->setCacheable(FALSE);

        $code = strtoupper($this->GET['code']);

        if (strlen($code) > 0) {
            if ($this->isCodeValid($code)) $this->tpl->parse("content.valid_code");
            else $this->tpl->parse("content.invalid_code");
        }

        return true;

    }

    /**
     * Just check if code existst
     * @param  String  $code Code pattern
     * @return boolean       Code exists (no other checks are performed atm)
     */
    protected function isCodeValid($code)
    {
        if (substr($code, 0, 4) == "REF-") {
            $codeEscaped = pg_escape_string($code);
            $promotion = $this->Promotion->listing("code_pattern = '$codeEscaped'");
            if (count($promotion) > 0 && $promotion[0]['id'] > 0) {

                $this->tpl->assign("PROMOTION", $promotion[0]);

                // we no longer check available uses as voucher code is extended on use
                return true;

                // $usage = $this->Promotion->getUsage($promotion[0]['id']);
                // $usage = $usage['count'];
                // $available_uses = max(0, $promotion[0]['uses_per_coupon'] - $usage);
                // return $available_uses > 0;
            }
        } 

        return false;
    }

}
