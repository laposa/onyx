<?php
/**
 * Backoffice customer list
 *
 * Copyright (c) 2008-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Customer_List extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		require_once('models/client/client_customer.php');	
		$Customer = new client_customer();
		//force cache even for back office user
		$Customer->setCacheable(true);
		
		/**
		 * Filtering
		 */
		
			 
		/**
		 * Get the list
		 */
		$customer_filter = $_SESSION['customer-filter'];
		
		$customer_list = $Customer->getClientList(0, $customer_filter);

		
		if (is_array($customer_list) && count($customer_list) > 0) {
			
			/**
			 * Sorting
			 */
			
			if ($this->GET['customer-list-sort-by']) {
				$_SESSION['customer-list-sort-by'] = $this->GET['customer-list-sort-by'];
			}
			
			if ($this->GET['customer-list-sort-direction']) {
				$_SESSION['customer-list-sort-direction'] = $this->GET['customer-list-sort-direction'];
			}
			
			if ($_SESSION['customer-list-sort-by']) {
				$sortby = $_SESSION['customer-list-sort-by'];
			} else {
				$sortby = "id";
			}
			
			if ($_SESSION['customer-list-sort-direction']) {
				$direction = $_SESSION['customer-list-sort-direction'];
			} else {
				$direction = "DESC";
			}
			
			//msg("Sorted by $sortby $direction");
			
			switch ($sortby) {
				default:
				case 'id':
					$customer_list = php_multisort($customer_list, array(array('key'=>'customer_id', 'sort'=>$direction), array('key'=>'customer_id', 'type'=>'numeric')));
			
					foreach ($customer_list as $item) {
						$p[] = $item;
					}
					
					$customer_list = $p;
					break;
				case 'last_order':
					$customer_list = php_multisort($customer_list, array(array('key'=>'last_order', 'sort'=>$direction), array('key'=>'customer_id', 'type'=>'numeric')));
			
					foreach ($customer_list as $item) {
						$p[] = $item;
					}
					
					$customer_list = $p;
					break;
				case 'goods_net':
					$customer_list = php_multisort($customer_list, array(array('key'=>'goods_net', 'sort'=>$direction), array('key'=>'customer_id', 'type'=>'numeric')));
			
					foreach ($customer_list as $item) {
						$p[] = $item;
					}
					
					$customer_list = $p;
					break;
				case 'count_baskets':
                    $customer_list = php_multisort($customer_list, array(array('key'=>'count_baskets', 'sort'=>$direction), array('key'=>'customer_id', 'type'=>'numeric')));

                    foreach ($customer_list as $item) {
                        $p[] = $item;
                    }

                    $customer_list = $p;
                    break;
                case 'count_orders':
                    $customer_list = php_multisort($customer_list, array(array('key'=>'count_orders', 'sort'=>$direction), array('key'=>'customer_id', 'type'=>'numeric')));

                    foreach ($customer_list as $item) {
                        $p[] = $item;
                    }

                    $customer_list = $p;
                    break;
				case 'count_items':
					$customer_list = php_multisort($customer_list, array(array('key'=>'count_items', 'sort'=>$direction), array('key'=>'customer_id', 'type'=>'numeric')));
			
					foreach ($customer_list as $item) {
						$p[] = $item;
					}
					
					$customer_list = $p;
					break;
			}
			
			/**
			 * Initialize pagination variables
			 */
			
			if  (is_numeric($this->GET['limit_from'])) $from = $this->GET['limit_from'];
			else $from = 0;
			if (is_numeric($this->GET['limit_per_page'])) $per_page = $this->GET['limit_per_page'];
			else $per_page = 25;
			
			
			$limit = "$from,$per_page";
			
			
			/**
			 * Display pagination
			 */
			
			//$link = "/page/" . $_SESSION['active_pages'][0];
			$count = count($customer_list);
			
			$_nSite = new nSite("component/pagination~limit_from=$from:limit_per_page=$per_page:count=$count~");
			$this->tpl->assign('PAGINATION', $_nSite->getContent());
			
			
			/**
			 * Display items
			 * Implemented pagination
			 */
		
		
			foreach ($customer_list as $i=>$customer) {
				if ($i >= $from  && $i < ($from + $per_page) ) {
		
					$even_odd = ( 'odd' != $even_odd ) ? 'odd' : 'even';
					$item['even_odd'] = $even_odd;
				
					$this->tpl->assign('ITEM', $customer);
					$this->tpl->parse('content.list.item');
				}
			}
		
			$this->tpl->parse('content.list');
		} else {
			msg("No user found", 'error');
		}

		return true;
	}
}
