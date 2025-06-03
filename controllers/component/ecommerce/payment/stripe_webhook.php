<?php
require_once('controllers/component/ecommerce/payment/stripe.php');

class Onyx_Controller_Component_Ecommerce_Payment_Stripe_Webhook extends Onyx_Controller_Component_Ecommerce_Payment_Stripe
{

    /**
     * main action
     */

    public function mainAction()
    {
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                ECOMMERCE_TRANSACTION_STRIPE_WEBHOOK_SECRET
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }

        if (
            $event->type == 'checkout.session.completed'
            || $event->type == 'checkout.session.async_payment_succeeded'
        ) {
            $this->fulfill_checkout($event->data->object->id);
        }

        http_response_code(200);
    }
}
