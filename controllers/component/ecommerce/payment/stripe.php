<?php
require_once('controllers/component/ecommerce/payment.php');
require_once('conf/payment/stripe.php');
require_once('models/common/common_node.php');
require_once('models/ecommerce/ecommerce_order.php');
require_once('models/common/common_email.php');

class Onyx_Controller_Component_Ecommerce_Payment_Stripe extends Onyx_Controller_Component_Ecommerce_Payment
{

    /**
     * @var \Stripe\StripeClient
     */
    protected $stripe;

    public function mainAction()
    {
        $this->initStripe();

        $this->transactionPrepare();
        $sessionData = $this->paymentPrepare($this->GET['order_id']);

        if (!$sessionData) return false;
        $session = $this->stripe->checkout->sessions->create($sessionData);

        header("HTTP/1.1 303 See Other");
        header("Location: " . $session->url);
        die();
    }

    protected function initStripe()
    {
        if ($this->stripe) return;

        $this->stripe = new \Stripe\StripeClient(ECOMMERCE_TRANSACTION_STRIPE_SECRET_KEY);
    }

    function paymentPrepare($order_id)
    {
        $node_conf = common_node::initConfiguration();
        $order_data = $this->Transaction->getOrderDetail($order_id);

        // process payment method only if status = 0 unpaid or 5 failed payment
        if (!$this->checkOrderStatusValidForPayment($order_data['status'])) return false;

        $protocol = onyxDetectProtocol();
        $server_url = "$protocol://{$_SERVER['HTTP_HOST']}";

        $Order = new ecommerce_order();
        $total = $Order->calculatePayableAmount($order_data);

        $data = [
            'mode' => 'payment',
            'client_reference_id' => $order_data['id'],
            'customer_email' => $order_data['client']['customer']['email'],
            'success_url' => "$server_url/page/" . $node_conf['id_map-payment_stripe_success'] . "?stripe_session_id={CHECKOUT_SESSION_ID}",
            'cancel_url' => "$server_url/page/" . $node_conf['id_map-payment_stripe_cancel'] . "?stripe_session_id={CHECKOUT_SESSION_ID}",
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => GLOBAL_DEFAULT_CURRENCY,
                        'product_data' => [
                            'name' => "Order #{$order_data['id']}",
                        ],
                        'unit_amount' => $total * 100, // Stripe expects amount in cents
                    ],
                    'quantity' => 1,
                ],
            ],
        ];

        return $data;
    }

    public function fulfill_checkout($sessionId)
    {
        $this->initStripe();
        $this->transactionPrepare();

        $checkoutSession = $this->stripe->checkout->sessions->retrieve($sessionId, [
            'expand' => ['line_items'],
        ]);

        $Order = new ecommerce_order();
        $Order->setCacheable(false);

        $orderId = $checkoutSession->client_reference_id;
        $order_data = $Order->getOrder($orderId);

        // check order status, as it could be already processed by webhook
        if ($order_data['status'] == 1) {
            return true;
        }

        $transaction_data['order_id'] = $order_data['id'];
        $transaction_data['pg_data'] = serialize($checkoutSession->toArray());
        $transaction_data['currency_code'] = GLOBAL_DEFAULT_CURRENCY;
        $transaction_data['amount'] = $checkoutSession->amount_total / 100; // Stripe returns amount in cents
        $transaction_data['created'] = date('c');
        $transaction_data['type'] = 'stripe';
        $transaction_data['status'] = $checkoutSession->payment_status != 'unpaid' ? 1 : 0; // 1 for paid, 0 for unpaid

        $id = $this->Transaction->insert($transaction_data);
        if (!$id) {
            msg("payment/stripe: cannot insert serialized pg_data: {$transaction_data['pg_data']}", 'error');
            return true;
        }

        if ($transaction_data['status'] == 0) {
            $Order->setStatus($orderId, 5);
            return $id; // Unpaid, exit early
        }

        // set order status to paid
        $Order->setStatus($orderId, 1);

        // send email to admin
        $EmailForm = new common_email();

        // use hash to allow webhook to verify the order
        $code = makeHash($order_data['id']);
        $_Onyx_Request = new Onyx_Request("component/ecommerce/order_detail~order_id={$order_data['id']}:code={$code}~");
        $order_data['order_detail'] = $_Onyx_Request->getContent();

        // this allows use customer data and company data in the mail template
        // is passed as DATA to template in common_email->_format
        $GLOBALS['common_email']['transaction'] = $transaction_data;
        $GLOBALS['common_email']['order'] = $order_data;

        if (!$EmailForm->sendEmail('new_order_paid', 'n/a', $order_data['client']['customer']['email'], $order_data['client']['customer']['first_name'] . " " . $order_data['client']['customer']['last_name'])) {
            msg("ecommerce_transaction: Can't send email.", 'error', 2);
        }

        if ($Order->conf['mail_to_address']) {
            if (!$EmailForm->sendEmail('new_order_paid', 'n/a', $Order->conf['mail_to_address'], $Order->conf['mail_to_name'])) {
                msg('ecommerce_transaction: Cant send email.', 'error', 2);
            }
        }

        return $id;
    }
}
