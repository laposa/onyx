<?php
/**
 * class ecommerce_order
 *
 * Copyright (c) 2009-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */
 
class ecommerce_order extends Onxshop_Model {

	/**
	 * @access private
	 */
	var $id;
	/**
	 * REFERENCES basket(id) ON UPDATE CASCADE ON DELETE CASCADE
	 * @access private
	 */
	var $basket_id;
	/**
	 * REFERENCES address(id) ON UPDATE CASCADE ON DELETE CASCADE
	 * @access private
	 */
	var $invoices_address_id;
	/**
	 * REFERENCES address(id) ON UPDATE CASCADE ON DELETE CASCADE
	 * @access private
	 */
	var $delivery_address_id;
	/**
	 * @access private
	 */
	var $other_data;
	/**
		0 New (unpaid)
		1 New (paid)
		2 Dispatched
		3 Complete
		4 Cancelled
		5 Failed payment
		6 In Progress
		7 Split
		
	 * @access private
	 */
	var $status;
	
	var $note_customer;
	
	var $note_backoffice;
	
	var $php_session_id;
	
	var $referrer;
	
	var $payment_type;
	
	var $_metaData = array(
		'id'=>array('label' => '', 'validation'=>'int', 'required'=>true), 
		'basket_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'invoices_address_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'delivery_address_id'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'other_data'=>array('label' => '', 'validation'=>'serialized', 'required'=>false),
		'status'=>array('label' => '', 'validation'=>'int', 'required'=>true),
		'note_customer'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'note_backoffice'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'php_session_id'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'referrer'=>array('label' => '', 'validation'=>'string', 'required'=>false),
		'payment_type'=>array('label' => '', 'validation'=>'string', 'required'=>true),
		'created'=>array('label' => '', 'validation'=>'datetime', 'required'=>false),
		'modified'=>array('label' => '', 'validation'=>'datetime', 'required'=>false)
		);
		
	/**
	 * create table sql
	 */
	 
	private function getCreateTableSql() {
	
		$sql = "
CREATE TABLE ecommerce_order (
    id serial NOT NULL PRIMARY KEY,
    basket_id integer REFERENCES ecommerce_basket ON UPDATE CASCADE ON DELETE RESTRICT,
    invoices_address_id integer REFERENCES client_address ON UPDATE CASCADE ON DELETE RESTRICT,
    delivery_address_id integer REFERENCES client_address ON UPDATE CASCADE ON DELETE RESTRICT,
    other_data text,
    status integer,
    note_customer text,
    note_backoffice text,
    php_session_id character varying(32),
    referrer character varying(255),
    payment_type character varying(255),
    created timestamp(0) without time zone DEFAULT now(),
    modified timestamp(0) without time zone DEFAULT now()
);
		";
		
		return $sql;
	}
	
	/**
	 * init configuration
	 */
	 
	static function initConfiguration() {
	
		if (array_key_exists('ecommerce_order', $GLOBALS['onxshop_conf'])) $conf = $GLOBALS['onxshop_conf']['ecommerce_order'];
		else $conf = array();
		
		//order of the status is important, don't change it! you can only add a new one at the end
		if (array_key_exists('status', $conf)) {
			$conf['status'] = explode(',', $conf['status']);
		} else {
			$conf['status'] = array('New (unpaid)', 'New (paid)', 'Dispatched', 'Completed', 'Cancelled', 'Failed payment', 'In Progress');
		}
		
		$conf['mail_to_address'] = $GLOBALS['onxshop_conf']['global']['admin_email'];
		$conf['mail_to_name'] = $GLOBALS['onxshop_conf']['global']['admin_email_name'];
		
		if ($conf['product_returns_mail_to_address'] == '') $conf['product_returns_mail_to_address'] = $GLOBALS['onxshop_conf']['global']['admin_email'];
		if ($conf['product_returns_mail_to_name'] == '') $conf['product_returns_mail_to_name'] = $GLOBALS['onxshop_conf']['global']['admin_email_name'];
		
		//show print proforma invoice to customer?
		if ($conf['proforma_invoice'] == 'false') $conf['proforma_invoice'] = false;
		else $conf['proforma_invoice'] = true;
		//send email about new unpaid order?
		if ($conf['mail_unpaid'] == 'false') $conf['mail_unpaid'] = false;
		else $conf['mail_unpaid'] = true;

		// zero VAT for non-EU customers? (off by default)
		if ($conf['non_eu_zero_vat'] == 'true') $conf['non_eu_zero_vat'] = true;
		else $conf['non_eu_zero_vat'] = false;

		return $conf;
		
	}
	
	/**
	 * get detail
	 */
	
	function getDetail($id) {
	
		if (is_numeric($id)) {
		
			$detail = $this->detail($id);
		
			if ($detail) {
		
				$detail['other_data'] = unserialize($detail['other_data']);
				
				return $detail;
		
			} else {
				return false;
			}
		} else {
		
			msg("ecommerce_order: order id not numeric");
			return false;
		}
	}
	
	/**
	 * get full detail
	 */
	
	function getFullDetail($id) {
	
		if (!is_numeric($id)) return false;
		return $this->getOrder($id);
	
	}
	
	/**
	 * get full detail list
	 */
	 
	function getFullDetailList($customer_id = NULL, $filter = array()) {
		
		if (!(is_numeric($customer_id) || is_null($customer_id))) return false;
		if (!is_array($filter)) return false;
		
		$order_list = $this->getOrderList($customer_id, $filter);
		
		if (!is_array($order_list)) {
			msg("ecommerce_order.getFullDetailList($customer_id, " . print_r($filter, true) . "): order list is not an array", 'error');
			return false;
		}
		
		foreach ($order_list as $item) {
			$order_detail = $this->getFullDetail($item['order_id']);
			$result[] = $order_detail;
		}
		
		return $result;
	}
	
	/**
	 * update order
	 */
	
	function updateOrder($data) {
	
		$data['other_data'] = serialize($data['other_data']);
		$data['modified'] = date('c');
		
		if ($this->update($data)) return true;
		else return false;
	}
	
	/**
	 * get list of orders
	 *
	 */
	 
	function getOrderList($customer_id = NULL, $filter = false, $limit = false, $offset = false) {
		
		if (!(is_numeric($customer_id) || is_null($customer_id))) return false;
		
		$add_to_where = $this->prepareFilterWhereQuery($filter);

		//customer ID
		if (is_numeric($customer_id)) $add_to_where .= " AND client_customer.id = $customer_id ";

		$add_limit = '';
		if (is_numeric($limit) && $limit > 0) {
			$add_limit = "LIMIT $limit";
			if (is_numeric($offset) && $offset > 0) {
				$add_limit .= " OFFSET $offset";
			}
		}

		/**
		 * SQL query
		 */
		$sql = "SELECT 
				ecommerce_order.id AS order_id,
				ecommerce_order.status AS order_status,
				ecommerce_order.created AS order_created,
				ecommerce_order.modified AS last_activity,
				ecommerce_order.other_data AS other_data,
				client_customer.id AS customer_id, 
				client_customer.email, 
				client_customer.title_before, 
				client_customer.first_name,
				client_customer.last_name,  
				client_customer.newsletter,
				client_customer.invoices_address_id,
				client_address.country_id,
				client_customer.company_id,  
				ecommerce_invoice.goods_net
			FROM ecommerce_order
			LEFT OUTER JOIN ecommerce_basket ON (ecommerce_basket.id = ecommerce_order.basket_id)
			LEFT OUTER JOIN client_customer ON (client_customer.id = ecommerce_basket.customer_id)
			LEFT OUTER JOIN client_address ON (client_address.id = client_customer.invoices_address_id)
			LEFT OUTER JOIN ecommerce_invoice ON  (ecommerce_invoice.order_id = ecommerce_order.id)
			WHERE 1=1
			$add_to_where
			ORDER BY ecommerce_order.id DESC
			$add_limit
		";

		//msg($sql);
		
		$records = $this->executeSql($sql);

		if (is_array($records)) {
	
			if (count($records) == 0) return array();
			
			/**
			 * format output array
			 */
			$breakdown = array();
	
			foreach ($records as $item) {
				$breakdown[$item['order_id']]['order_id'] = $item['order_id'];
				$breakdown[$item['order_id']]['order_status'] = $item['order_status'];
				$breakdown[$item['order_id']]['customer_id'] = $item['customer_id'];
				$breakdown[$item['order_id']]['goods_net'] = $item['goods_net'];
				$breakdown[$item['order_id']]['other_data'] = unserialize($item['other_data']);
				$breakdown[$item['order_id']]['email'] = $item['email'];
				$breakdown[$item['order_id']]['title_before'] = $item['title_before'];
				$breakdown[$item['order_id']]['first_name'] = $item['first_name'];
				$breakdown[$item['order_id']]['last_name'] = $item['last_name'];
				$breakdown[$item['order_id']]['newsletter'] = $item['newsletter'];
				$breakdown[$item['order_id']]['company_id'] = $item['company_id'];
				$breakdown[$item['order_id']]['invoices_address_id'] = $item['invoices_address_id'];
				$breakdown[$item['order_id']]['order_created'] = $item['order_created'];
				$breakdown[$item['order_id']]['last_activity'] = $item['last_activity'];
			}
	
			foreach ($breakdown as $item) {
				$c_breakdown[] = $item;
			}
			return $c_breakdown;
			
		} else {
			
			return false;
		}
	}

	/**
	 * get number of orders with given filter applied
	 *
	 */
	 
	function getOrderListCount($customer_id = NULL, $filter = false) {
		
		if (!(is_numeric($customer_id) || is_null($customer_id))) return false;
		
		$add_to_where = $this->prepareFilterWhereQuery($filter);

		//customer ID
		if (is_numeric($customer_id)) $add_to_where .= " AND client_customer.id = $customer_id ";

		/**
		 * SQL query
		 */
		$sql = "SELECT count(*) AS item_count FROM ecommerce_order
			LEFT OUTER JOIN ecommerce_basket ON (ecommerce_basket.id = ecommerce_order.basket_id)
			LEFT OUTER JOIN client_customer ON (client_customer.id = ecommerce_basket.customer_id)
			WHERE 1=1
			$add_to_where";

		$record = $this->executeSql($sql);

		return (int) $record[0]['item_count'];
	}

	/**
	 * Prepare WHERE part of SQL query according to given filter
	 */
	function prepareFilterWhereQuery($filter)
	{
		$add_to_where = '';
		
		/**
		 * query filter
		 * 
		 */
		
		//order status
		if (is_numeric($filter['status'])) $add_to_where .= " AND ecommerce_order.status = {$filter['status']}";
		
		//query
		if (is_numeric($filter['query'])) {
			$add_to_where .= " AND ecommerce_order.id = {$filter['query']}";
		} else if (isset($filter['query']) && $filter['query'] !== '') {
			// we could use ILIKE there, but it's not available in mysql
			$query = strtoupper(addslashes($filter['query']));
			//try to explode query by space
			$e_query = explode(" ", $query);
			if (count($e_query) == 2) {
				$add_to_where .= " AND (UPPER(first_name) LIKE '%{$e_query[0]}%' OR UPPER(last_name) LIKE '%{$e_query[1]}%')";
			} else {
				$add_to_where .= " AND (UPPER(email) LIKE '%$query%' OR UPPER(first_name) LIKE '%$query%' OR UPPER(last_name) LIKE '%$query%' OR UPPER(username) LIKE '%$query%')";
			}
		}
		

		//created between filter
		if ($filter['created_from'] != false && $filter['created_to'] != false) {
			if  (!preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $filter['created_from']) || !preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $filter['created_to'])) {
				msg("Invalid format for created between. Must be YYYY-MM-DD", "error");
				return false;
			}
			$add_to_where .=" AND ecommerce_order.created BETWEEN '{$filter['created_from']}' AND '{$filter['created_to']}'";
		}
		
		//activity between filter
		/*if ($filter['activity_from'] != false && $filter['activity_to'] != false) {
			if  (!preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $filter['activity_from']) || !preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $filter['activity_to'])) {
				msg("Invalid format for modified between. Must be YYYY-MM-DD", "error");
				return false;
			}

			$add_to_where .=" AND ecommerce_order_log.datetime BETWEEN '{$filter['activity_from']}' AND '{$filter['activity_to']}'";
		}*/
		
		return $add_to_where;
	}

	/**
	 * get detail of one order
	 *
	 * @param unknown_type $id
	 * @return unknown
	 */
	 
	function getOrder($id) {
	
		require_once('models/ecommerce/ecommerce_basket.php');
		require_once('models/client/client_customer.php');
		require_once('models/ecommerce/ecommerce_order_log.php');
		require_once('models/ecommerce/ecommerce_delivery.php');
		
		$Basket = new ecommerce_basket();
		$Customer = new client_customer();
		$OrderLog = new ecommerce_order_log();
		$Delivery = new ecommerce_delivery();
		
		$Basket->setCacheable(false);
		$Customer->setCacheable(false);
		$OrderLog->setCacheable(false);
		
		$order = $this->getDetail($id);
		
		//get promotion code
		$order['promotion_code'] = $this->getPromotionCode($id);
		
		//get basket detail
		$basket_detail = $Basket->getDetail($order['basket_id']);
		$exclude_vat = !$this->isVatEligible($order['delivery_address_id'], $basket_detail['customer_id']);
		$basket_content = $Basket->getContent($order['basket_id'], GLOBAL_DEFAULT_CURRENCY, $exclude_vat);
		$basket_content['delivery'] = $Delivery->getDeliveryByOrderId($id);
		$order['basket'] = $basket_content;	
		
		//get client detail
		$order['client'] = $Customer->getClientData($basket_content['customer_id']);
		
		//get status (log) detail
		$order['log'] = $OrderLog->getLog($id);
		$order['status_title'] = $this->getStatusTitle($order['status']);
		
		//get address detail
		require_once('models/client/client_address.php');
		$Address = new client_address();
		$Address->setCacheable(false);
		$address_detail['delivery'] = $Address->getDetail($order['delivery_address_id']);
		$address_detail['invoices'] = $Address->getDetail($order['invoices_address_id']);
		$order['address'] = $address_detail;
		
		//get invoice detail
		$order['invoice'] = $this->getInvoiceDetail($id);
		
		//get transaction detail
		$order['transaction'] = $this->getTransactionDetail($id);
		
		//print_r($order);
		return $order;
	}

	/**
	 * Check if the order is payed. Now only by status.
	 */
	 
	function isPayed($order_id) {
	
		$order_data = $this->detail($order_id);
		
		if ($order_data['status'] == 1 || $order_data['status'] == 2 || $order_data['status'] == 3 || $order_data['status'] == 4 || $order_data['status'] == 6 || $order_data['status'] == 7) {
			return $order_data['status'];
		} else {
			return false;
		}
	}

	/**
	 * get order status detail
	 */
	 
	function getStatus($order_id) {
	
		$order_details = $this->detail($order_id);
		
		$status['title'] = $this->getStatusTitle($order_details['status']);
		$status['id'] = $order_details['status'];
		
		return $status;
	}
	
	/**
	 * get status title
	 */
	 
	function getStatusTitle($status_id) {
	
		$status = $this->conf['status'];
		$status_info = $status[$status_id];
		
		return $status_info;
	}
	
	/**
	 * change status of the order
	 *
	 * @param unknown_type $status
	 */
	 
	function setStatus($order_id, $status) {
	
		if (!is_numeric($order_id) || !is_numeric($status)) {
			msg("ecommerce_order->setStatus(): order_id or status isn't numeric");
			return false;
		}
		
		// update
		$order_data['id'] = $order_id;
		$order_data['status'] = $status;
		$order_data['modified'] = date('c');
		$this->update($order_data);
		
		// log
		require_once('models/ecommerce/ecommerce_order_log.php');
		$OrderLog = new ecommerce_order_log();
		$log_data['order_id'] = $order_id;
		$log_data['status'] = $status;
		$log_data['datetime'] = date('c');
		$log_data_id = $OrderLog->insert($log_data);
		
		//order status change hook
		$this->orderStatusChangeAction($order_id, $status);
		
		//should return numeric, or false
		return $log_data_id;
	}
	
	/**
	 * orderStatusChangeAction
	 */
	 
	public function orderStatusChangeAction($order_id, $status) {
	
		if (!is_numeric($order_id) || !is_numeric($status)) {
			msg("ecommerce_order->orderStatusChangeAction(): order_id or status isn't numeric");
			return false;
		}
		
		/**
		 * invoice management
		 */
		 
		require_once('models/ecommerce/ecommerce_invoice.php');
		$Invoice = new ecommerce_invoice();
		$Invoice->setCacheable(false);
		
		if ($status == 1) {
			//create invoice for paid orders
			$Invoice->createNormalInvoice($order_id);
		} else if ($status == 4) {
			//mark invoice as cancelled
			$Invoice->cancelInvoice($order_id);	
		}
		
		/**
		 * customer actions configurable per customer in controllers/
		 * calling controllers from model isn't exactly my concept of MVC, let's see it as a HACK for now
		 */
		 
		$_Onxshop_Request = new Onxshop_Request("component/ecommerce/order_status_change_action~order_id={$order_id}:status={$status}~");
		
		return true;
		
	}
	

	/**
	 * check order status
	 * process payment method only if status = 0 unpaid or 5 failed payment 
	 */
	
	function checkOrderStatusValidForPayment($status) {
	
		if (!is_numeric($status)) return false;
		
		if ($status == 1 || $status == 2 || $status == 3 || $status == 4) {
		
			msg("Ecommerce_order: Can't process order in status New (paid), Dispatched, Completed, Cancelled", 'error', 2);
			return false;
		
		} else {
		
			return true;
		
		}

	}
	
	/**
	 * insert a new order
	 *
	 * @return unknown
	 */
	 
	function insertOrder($order_data) {

		// set status to 0 (Not processed payment)
		$order_data['status'] = 0;
		
		$insert_order_data = $order_data;
		$insert_order_data['other_data'] = serialize($insert_order_data['other_data']);
		$insert_order_data['created'] = date('c');
		$insert_order_data['modified'] = date('c');
		
		if (is_numeric($id = $this->insert($insert_order_data))) {
		
			$order_data['id'] = $id;
			
			//insert delivery record
			//need to be inserted before recording usage of promotion code, otherwise delivery calculation thinks coupon has be already used when uses_per_customer = 1
			if (!$this->insertDelivery($order_data)) {
				msg("Cannot insert delivery data", 'error');
				return false;
			} 
			
			//record promotion code use
			if ($order_data['other_data']['promotion_code']) {
				require_once('models/ecommerce/ecommerce_promotion_code.php');
				$Promotion_code = new ecommerce_promotion_code();
				if ($inserted_code_id = $Promotion_code->insertPromotionCode($order_data['other_data']['promotion_code'], $order_data['id'])) {
					//
				} else {
					msg("Can't insert promotion code {$order_data['other_data']['promotion_code']} ", 'error');
				}
			}
			
			//get full order data
			$order_data = $this->getOrder($id);
			
			//descrement stock
			$this->decrementStock($order_data);
			
			//set status
			$this->setStatus($id, 0);

			// calculate payable amount
			$this->updatePayableDue($order_data);
			
			//send email to admin
			require_once('models/common/common_email.php');
    
    		$EmailForm = new common_email();

    		$_Onxshop_Request = new Onxshop_Request("component/ecommerce/order_detail~order_id={$order_data['id']}~");
			$order_data['order_detail'] = $_Onxshop_Request->getContent();
	
    		//this allows use customer data and company data in the mail template
    		//is passed as DATA to template in common_email->_format
    		$GLOBALS['common_email']['order'] = $order_data;
    		
    		if ($this->conf['mail_unpaid']) {
	    		if (!$EmailForm->sendEmail('new_order_unpaid', 'n/a', $this->conf['mail_to_address'], $this->conf['mail_to_name'])) {
    				msg("ecommerce_order: can't send email", 'error', 2);
    			}
			}
    	
			//return order.id
			return $id;
			
		} else {
			return false;
		}
	}
	
	/**
	 * insert delivery
	 */

	function insertDelivery($order_data) {
		
		//calculate delivery price
		require_once('models/ecommerce/ecommerce_basket.php');
		$Basket = new ecommerce_basket();
		$Basket->setCacheable(false);
		$delivery = $Basket->calculateDelivery($order_data['basket_id'], $order_data['delivery_address_id'], $order_data['other_data']['delivery_options'], $order_data['other_data']['promotion_code']);
		
		//prepare object
		require_once('models/ecommerce/ecommerce_delivery.php');
		$Ecommerce_Delivery = new ecommerce_delivery();
		
		//format data
		$delivery_data['order_id'] = $order_data['id'];
		$delivery_data['carrier_id'] = $order_data['other_data']['delivery_options']['carrier_id'];
		$delivery_data['value_net'] = $delivery['value_net'];
		$delivery_data['vat'] = $delivery['vat'];
		$delivery_data['vat_rate'] = $delivery['vat_rate'];
		$delivery_data['required_datetime'] = $order_data['other_data']['delivery_options']['required_datetime'];
		$delivery_data['note_customer'] = '';
		$delivery_data['note_backoffice'] = '';
		$delivery_data['other_data'] = $order_data['other_data']['delivery_options']['other_data'];
		$delivery_data['weight'] = $delivery['weight'];

		//insert
		if ($id = $Ecommerce_Delivery->insertDelivery($delivery_data)) return $id;
		else return false;
	}
	
	

	
	/**
	 * descrement value on the stock
	 *
	 * @param array $order_data
	 * @return bool
	 */
	 
	function decrementStock($order_data) {
	
		if (!is_array($order_data)) {
			msg("Cannot decrement stock", 'error');
			return false;
		}
		
		require_once('models/ecommerce/ecommerce_product.php');
		$ProductVariety = new ecommerce_product_variety();
		
		foreach ($order_data['basket']['items'] as $item) {
			
			$new_stock_value = $item['product']['variety']['stock'] - $item['quantity'];
			$variety_id = $item['product']['variety']['id'];
			
			$ProductVariety->updateSingleAttribute('stock', $new_stock_value, $variety_id);
		
		}
		
		return true;
		
	}


	/**
	 * update order to include amount payable (inluding delivery fees) in other_data
	 * @param array $order_data
	 */

	function updatePayableDue($order_data) {

		$order_data['other_data']['payment_due'] = $this->calculatePayableAmount($order_data);

		$update_data['id'] = $order_data['id'];
		$update_data['modified'] = date('c');
		$update_data['other_data'] = serialize($order_data['other_data']);

		$this->update($update_data);
	}
	

	/**
	 * Calcaulate total payable amount
	 * @return float
	 */
	function calculatePayableAmount($order_data) {

		return round($order_data['basket']['total_after_discount'] + 
			$order_data['basket']['delivery']['value_net'] + 
			$order_data['basket']['delivery']['vat'], 2);
	}



	/**
	 * Return true if given address and customer is VAT eligible
	 * 
	 * @param  int    $delivery_address_id Delivery address id to check EU status
	 * @param  int    $customer_id         Customer id to check whole sale status (not implemented yet!)
	 * @return boolean
	 */
	function isVatEligible($delivery_address_id, $customer_id) {

		require_once('models/ecommerce/ecommerce_price.php');
		$order_conf = ecommerce_order::initConfiguration();

		require_once('models/client/client_address.php');
		$Address = new client_address();
		$delivery = $Address->getDetail($delivery_address_id);
		$exclude_vat = $order_conf['non_eu_zero_vat'] && !$delivery['country']['eu_status'];

		return !$exclude_vat;
	}

	/**
	 *
	 * used for drawing of graph
	 * 
	 * @param unknown_type $time_frame
	 * @param unknown_type $limit
	 * @return unknown
	 */
	 
	function getStatData($time_frame = 'week', $limit = 30) {
        switch ($time_frame) {
            case 'month':
                $format = 'YY/MM';
            break;
            case 'day':
                $format = 'MM/DD';
            break;
            case 'week':
            default:
            	$time_frame = 'week';
                $format = 'YY/WW';
            break;
        }
        $sql = "SELECT 
        	o.id, 
        	to_char(COALESCE(i.created, o.created), 'YYMMDD') AS datefull, 
        	to_char(COALESCE(i.created, o.created), '$format') AS date, 
        	i.payment_amount,
        	o.status,
        	i.status AS invoice_status
        	FROM ecommerce_order AS o
        	LEFT JOIN ecommerce_invoice AS i ON i.order_id = o.id
        	WHERE COALESCE(i.created, o.created) >= (NOW() - INTERVAL '" . ($limit + 1) . " $time_frame')
        	ORDER BY COALESCE(i.created, o.created) DESC";
       
        $records = $this->executeSql($sql);
        $data = array();
        $ir = 0;

        $d = 0;

        foreach ($records as $r) {
            if($d != $r['date']) {
                $d = $r['date'];
                $i['num_orders_finished'] = 0;
                $i['num_orders_unfinished'] = 0;
                $i['revenue'] = 0;
                $ir++;
            }

            if ($r['status'] == 1 || $r['status'] == 2 || $r['status'] == 3 || $r['status'] == 6 || $r['status'] == 7 ) {
                $i['num_orders_finished']++;
            	if ($r['invoice_status'] == 1) $i['revenue'] += $r['payment_amount'];
            } else {
            	$i['num_orders_unfinished']++;
            }

            // use limit
            if ($ir <= $limit) {
                $data[$d] = $i;
            }
        }

        $data = array_reverse($data, true);

        return $data;
    }

    /**
     * Get sales for each product type
     *
     * @param unknown_type $from
     * @param unknown_type $to
     */
     
	function getBreakdown($from, $to) {
	
		/**
		 * check input date format
		 */
		 
		if  (!preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $from) || !preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $to)) {
			msg("Invalid format for date. Must be YYYY-MM-DD", "error");
			return false;
		}

		/**
		 * build SQL query
		 */
		 
		$sql = "
			SELECT 
			ecommerce_invoice.id AS invoice_id, 
			ecommerce_invoice.created AS invoice_created, 
			ecommerce_invoice.goods_net AS goods_net, 
			ecommerce_invoice.goods_vat_sr AS goods_vat_sr, 
			ecommerce_invoice.goods_vat_rr AS goods_vat_rr,
			ecommerce_invoice.delivery_net AS delivery_net, 
			ecommerce_invoice.delivery_vat AS delivery_vat,
			ecommerce_invoice.voucher_discount AS voucher_discount,
			ecommerce_order.id AS order_id, 
			ecommerce_product_type.id AS type_id, 
			ecommerce_product_type.name AS type_name, 
			ecommerce_product_type.vat AS vat_rate, 
			ecommerce_basket_content.quantity AS quantity,
			ecommerce_price.value AS price 
			FROM ecommerce_invoice 
			LEFT OUTER JOIN ecommerce_order ON (ecommerce_order.id = ecommerce_invoice.order_id) 
			LEFT OUTER JOIN ecommerce_basket ON (ecommerce_basket.id = ecommerce_order.basket_id) 
			LEFT OUTER JOIN ecommerce_basket_content ON (ecommerce_basket.id = ecommerce_basket_content.basket_id) 
			LEFT OUTER JOIN ecommerce_price ON (ecommerce_basket_content.price_id = ecommerce_price.id) 
			LEFT OUTER JOIN ecommerce_product_type ON (ecommerce_product_type.id = ecommerce_basket_content.product_type_id) 
			WHERE ecommerce_invoice.status = 1 AND ecommerce_invoice.created BETWEEN '$from' AND '$to' 
			ORDER BY invoice_id;";

		$records = $this->executeSql($sql);
		
		$breakdown = array();

		foreach ($records as $item) {
			
			// reduce price when discount_net applied
			//if ($item['discount_net'] > 0) {
				// TODO: missing item[quantity], should be divided by quantity, looks like we don't count with quantity during calculation in ecommerce_basket->total_goods_net_before_discount 
				//$item['price'] = $item['price'] - ($item['price'] * $item['discount_net'] / ($item['goods_net'] + $item['discount_net']));
			//}
			
			// group by product type
			$breakdown['goods']['type'][$item['type_name']]['net'] += $item['price'] * $item['quantity'];
			if ($item['goods_vat_sr'] > 0 || $item['goods_vat_rr'] > 0) 
				$item['vat'] = $item['price'] * $item['quantity'] * $item['vat_rate'] / 100;
			else 
				$item['vat'] = 0;
			$breakdown['goods']['type'][$item['type_name']]['vat'] += $item['vat'];
			
			
			
			
			// check if we jump to another invoice 
			if ($item['invoice_id'] != $invoice_id) {
			
				$invoice_id = $item['invoice_id'];
				
				if ($item['delivery_vat'] == 0) {
					$breakdown['delivery']['vat_exempt'] += $item['delivery_net'];
				} else {
					$breakdown['delivery']['net'] += $item['delivery_net'];
					$breakdown['delivery']['vat'] += $item['delivery_vat'];
				}
				
				$breakdown['goods']['charged']['net'] += $item['goods_net'];
				$breakdown['goods']['charged']['vat'] += ($item['goods_vat_sr'] + $item['goods_vat_rr']); // either standard rate or reduce rate should be zero
				
				$breakdown['voucher_discount'] += $item['voucher_discount'];
				
			}
			
			//total
			$breakdown['goods']['total']['net'] += $item['price'] * $item['quantity'];
			$breakdown['goods']['total']['vat'] += $item['vat'];
		}

		//get wasted vouchers
		$sql = "SELECT sum(voucher_discount - goods_net - goods_vat_sr - goods_vat_rr) FROM ecommerce_invoice WHERE status = 1 AND(voucher_discount - goods_net - goods_vat_sr - goods_vat_rr) > 0 AND created BETWEEN '$from' AND '$to'";
		$breakdown['voucher_wasted'] = $this->db->fetchOne($sql);
		
		//ecommerce_invoice_transaction check
		//shoudn't be needed to filter by status, because unseccessfull transactions has amount 0.00
		$sql = "SELECT sum(amount) FROM ecommerce_transaction WHERE status = 1 AND created BETWEEN '$from' AND '$to'";
		$breakdown['check']['worldpay'] = $this->db->fetchOne($sql);
		
		//general invoice check
		$sql = "SELECT sum(payment_amount) FROM ecommerce_invoice WHERE status = 1 AND created BETWEEN '$from' AND '$to'";
		$breakdown['check']['invoice'] = $this->db->fetchOne($sql);
		
		//print_r($breakdown);
		return $breakdown;
		
	}
			
	/**
	 * get product sales report
	 */
	 
	public function getProductSalesList($from, $to) {
		
		/**
		 * check input date format
		 */
		 
		if  (!preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $from) || !preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $to)) {
			msg("Invalid format for date. Must be YYYY-MM-DD", "error");
			return false;
		}

		/**
		 * build SQL query
		 */
		 
		$sql = "SELECT DISTINCT product.id AS product_id, product.name AS product_name, product_variety.name AS variety_name, product_variety.sku AS variety_sku, product_variety.stock AS variety_stock, ecommerce_basket_content.product_variety_id AS variety_id, sum(ecommerce_basket_content.quantity) AS count, sum(ecommerce_price.value * ecommerce_basket_content.quantity) AS revenue
		FROM ecommerce_basket_content
LEFT OUTER JOIN ecommerce_price ON (ecommerce_price.id = ecommerce_basket_content.price_id) 
		LEFT OUTER JOIN ecommerce_product_variety product_variety ON (product_variety.id = ecommerce_basket_content.product_variety_id)
		LEFT OUTER JOIN ecommerce_product product ON (product.id = product_variety.product_id)
		LEFT OUTER JOIN ecommerce_basket ON (ecommerce_basket.id =ecommerce_basket_content.basket_id)
		LEFT OUTER JOIN ecommerce_order ON (ecommerce_order.basket_id = ecommerce_basket_content.basket_id)
		LEFT OUTER JOIN ecommerce_invoice ON (ecommerce_invoice.order_id = ecommerce_order.id)
		WHERE ecommerce_invoice.status = 1 AND ecommerce_invoice.created BETWEEN '$from' AND '$to'
		GROUP BY variety_id, variety_name, variety_sku, variety_stock, product.id, product_name
		ORDER BY variety_sku";
	
		/**
		 * process query
		 */
		 
		if ($records = $this->executeSql($sql)) {
			return $records;
		} else {
			return false;
		}
	}
	
	
	/**
	 * get promotion code
	 */
	
	function getPromotionCode($order_id) {
		if (!is_numeric($order_id)) return false;
		
		require_once('models/ecommerce/ecommerce_promotion.php');
		$Promotion = new ecommerce_promotion();
		
		return $Promotion->getPromotionCodeForOrder($order_id);
		
	}
	
	
	/**
	 * get invoice detail
	 */
	
	function getInvoiceDetail($order_id) {
		
		if (!is_numeric($order_id)) return false;
		
		require_once('models/ecommerce/ecommerce_invoice.php');
		$Invoice = new ecommerce_invoice();
		$Invoice->setCacheable(false);
		
		$invoice_detail = $Invoice->getInvoiceForOrder($order_id);
		
		if (is_array($invoice_detail)) return $invoice_detail;
		else return false;
	}
	
	/**
	 * get transaction detail
	 */
	 
	function getTransactionDetail($order_id) {
		
		if (!is_numeric($order_id)) return false;
		
		require_once('models/ecommerce/ecommerce_transaction.php');
		$Transaction = new ecommerce_transaction();
		$Transaction->setCacheable(false);
		
		$transaction_list = $Transaction->getListForOrderId($order_id);
		
		if (is_array($transaction_list)) return $transaction_list[0];
		else return false;
	}

	/**
	 * get list of orders
	 *
	 */
	 
	function getOrderListForExport($filter = false, $includeProducts = false) {
		
		$add_to_where = $this->prepareFilterWhereQuery($filter);
		
		/**
		 * SQL query
		 */
		$sql = "SELECT
			ecommerce_order.id AS order_id,
			ecommerce_order.status AS order_status,
			ecommerce_order.created AS order_created,
			ecommerce_order.modified AS last_activity,
			ecommerce_basket.customer_id AS customer_id,
			client_customer.email AS email,
			client_customer.title_before AS title_before,
			client_customer.first_name AS first_name,
			client_customer.last_name AS last_name,
			ecommerce_invoice.goods_net AS goods_net";

		if ($includeProducts) $sql .= ",
				ecommerce_product.name AS product_name,
				ecommerce_product_variety.name AS product_variety,
				ecommerce_basket_content.quantity AS product_quantity,
				ecommerce_price.value AS product_price";

		$sql .= "
			FROM ecommerce_order
		";

		if ($includeProducts) $sql .= "INNER";
		else $sql .= "LEFT";

		$sql .= " JOIN ecommerce_invoice ON ecommerce_invoice.order_id = ecommerce_order.id
			LEFT JOIN ecommerce_basket ON ecommerce_basket.id = ecommerce_order.basket_id
			LEFT JOIN client_customer ON client_customer.id = ecommerce_basket.customer_id";

		if ($includeProducts) $sql .= "
			LEFT JOIN ecommerce_basket_content ON ecommerce_basket_content.basket_id = ecommerce_basket.id
			LEFT JOIN ecommerce_product_variety ON ecommerce_product_variety.id = ecommerce_basket_content.product_variety_id
			LEFT JOIN ecommerce_product ON ecommerce_product.id = ecommerce_product_variety.product_id
			LEFT JOIN ecommerce_price ON ecommerce_price.id = ecommerce_basket_content.price_id";

		$sql .= "
			WHERE 1=1
			$add_to_where
			ORDER BY ecommerce_order.id DESC";

		//msg($sql);
		
		$records = $this->executeSql($sql);

		return $records;

	}

}
