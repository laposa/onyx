<?php
require_once('controllers/component/ecommerce/payment/stripe.php');

class Onyx_Controller_Component_Ecommerce_Payment_Stripe_Callback extends Onyx_Controller_Component_Ecommerce_Payment_Stripe
{

    /**
     * main action
     */

    public function mainAction()
    {
        if (isset($this->GET['session_id'])) {
            try {
                $this->fulfill_checkout($this->GET['session_id']);
            } catch (Exception $e) {
                msg($e->getMessage(), 'error');
                $node_conf = common_node::initConfiguration();
                onyxGoTo("page/" . $node_conf['id_map-payment_stripe_cancel']);
            }
        }

        return true;
    }
}
