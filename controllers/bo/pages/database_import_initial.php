<?php
/**
 * Copyright (c) 2005-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Bo_Pages_Database_Import_Initial extends Onxshop_Controller {
	
	/**
	 * main action
	 */
	 
	public function mainAction() {

		return false; 

		set_time_limit(0);
		$process = $this->GET['process'];
		$process = 0;
		
		require_once('models/ecommerce/ecommerce_product.php');
		require_once('models/ecommerce/ecommerce_product_variety.php');
		require_once('models/ecommerce/ecommerce_price.php');
		require_once('models/common/common_node.php');
		
		$Product = new ecommerce_product();
		$Product_variety = new ecommerce_product_variety();
		$Price = new ecommerce_price();
		$Node = new common_node();
		
		//START THE TRANSACTION
		$Product->db->beginTransaction();
		
		//get data file
		
		if ($this->GET['data_file'] != '') {
			$data_file = ONXSHOP_PROJECT_DIR . $this->GET['data_file'];
			if (file_exists($data_file)) $google_base = $this->convertCSVtoAssocMArray($data_file, ";");
			else msg("Datafile $data_file does not exists", 'error');
		}
		
		
		foreach ($google_base as $gb) {
		
			$onxshop_product['name'] = ucwords(strtolower(trim($gb['title'])));
			
			//check if the product name has been changed, if not, it's only new variety
			if ($watch_name != $onxshop_product['name']) {
				$watch_name = $onxshop_product['name'];
				
				$onxshop_product['teaser'] = $gb['teaser'];
				
				if ($gb['description'] != '') $onxshop_product['description'] = $gb['description'];
				else $onxshop_product['description'] = $onxshop_product['name'];
				
				$onxshop_product['priority'] = 0;
				$onxshop_product['brand_id'] = 1;
				$onxshop_product['publish'] = 1;
		
				//Food
				//if ($gb['tax_percent'] == 19) $onxshop_product['product_type_id'] = 7;
				//Food-Bio
				//else if ($gb['tax_percent'] == 5) $onxshop_product['product_type_id'] = 8;
				/*
				if ($gb['vat_group'] == 'tea') $onxshop_product['product_type_id'] = 9;
				else if ($gb['vat_group'] == 'tea ware') $onxshop_product['product_type_id'] = 10;
				else msg("Unknown vat group (product_type_id) {$gb['vat_group']}", 'error');
				*/
				$onxshop_product['product_type_id'] = 9;
				
				//other data
				unset($onxshop_product['other_data']);
				if ($gb['other_data-taste'] != '') $onxshop_product['other_data']['taste'] = $gb['other_data-taste'];
				if ($gb['other_data-infuse'] != '') $onxshop_product['other_data']['infuse'] = $gb['other_data-infuse'];
				if ($gb['other_data-drink'] != '') $onxshop_product['other_data']['drink'] = $gb['other_data-drink'];
				$onxshop_product['other_data'] = serialize($onxshop_product['other_data']);
				
				//taxonomy
				$_POST['product']['rt'][] = '';
				
				/*
				switch (strtolower($gb['taxonomy-region'])) {
					case 'china':
						$_POST['product']['rt'][] =  18;
					break;
					case 'india':
						$_POST['product']['rt'][] =  19;
					break;
					case 'japan':
						$_POST['product']['rt'][] =  20;
					break;
					case 'sri lanka':
						$_POST['product']['rt'][] =  21;
					break;
					case 'taiwan':
						$_POST['product']['rt'][] =  34;
					break;
				}*/
				
				switch (trim($gb['brand'])) {
					case 'Aqualeader':
						$_POST['product']['rt'][] =  6;
					break;
					case 'Atlantic':
						$_POST['product']['rt'][] =  14;
					break;
					case 'Bosta':
						$_POST['product']['rt'][] =  19;
					break;
					case 'Catalina':
						$_POST['product']['rt'][] =  16;
					break;
					case 'Doughboy':
						$_POST['product']['rt'][] =  8;
					break;
					case 'Easy Cove':
						$_POST['product']['rt'][] =  22;
					break;
					case 'Equinox':
						$_POST['product']['rt'][] =  7;
					break;
					case 'Esther Williams':
						$_POST['product']['rt'][] =  4;
					break;
					case 'Folkpool':
						$_POST['product']['rt'][] =  9;
					break;
					case 'Freedom':
						$_POST['product']['rt'][] =  11;
					break;
					case 'Garden Leisure':
						$_POST['product']['rt'][] =  10;
					break;
					case 'Hayward':
						$_POST['product']['rt'][] =  23;
					break;
					case 'Insta':
						$_POST['product']['rt'][] =  17;
					break;
					case 'Kafko':
						$_POST['product']['rt'][] =  15;
					break;
					case 'Kreepy Krauly':
						$_POST['product']['rt'][] =  25;
					break;
					case 'Plastica':
						$_POST['product']['rt'][] =  21;
					break;
					case 'Polaris':
						$_POST['product']['rt'][] =  24;
					break;
					case 'Pomaz':
						$_POST['product']['rt'][] =  12;
					break;
					case 'Spaform':
						$_POST['product']['rt'][] =  13;
					break;
					case 'Sta-Rite':
						$_POST['product']['rt'][] =  26;
					break;
					case 'Vogue':
						$_POST['product']['rt'][] =  5;
					break;
					case 'Voyager':
						$_POST['product']['rt'][] =  18;
					break;
					case 'Waterpik':
						$_POST['product']['rt'][] =  20;
					break;
				}
				
				$_nSite = new nSite("bo/component/relation_taxonomy~relation=product:id=$product_id~");
				
				
				//print_r($onxshop_product);
				$product_id = $Product->insert($onxshop_product);
				
				if (is_numeric($product_id)) {
					//image add
					if ($gb['image_link'] != '') {
						$_POST['file']['title'] = $onxshop_product['name'];
						$_POST['file']['src'] = "var/files/products/{$gb['image_link']}.jpg";
						$_POST['file']['priority'] = 0;
						$_POST['file']['role'] = 'main';
						$_POST['file']['node_id'] = $product_id;
						$_POST['add'] = 'add';
					
						$file = ONXSHOP_PROJECT_DIR . $_POST['file']['src'];
						if (file_exists($file)) $_nSite = new nSite("bo/component/form_file~relation=product~");
						else msg("File $file does not exists", 'error');
						unset($_POST['file']);
					}
				
				
					//add to node
					$_POST['product']['name'] = $onxshop_product['name'];
					$_POST['product']['publish'] = 1;
					$_POST['product']['pin'][] = '';
					$_POST['product']['pin'][] =  $gb['page_id'];
				
					$_nSite = new nSite("bo/component/ecommerce/relation_product_in_nodes~id=$product_id~");
					unset($_POST['product']);
				}
			}
			
			if (is_numeric($product_id)) {
				$onxshop_product_variety['name'] = $gb['variety_name'];
				$onxshop_product_variety['product_id'] = $product_id;
				$onxshop_product_variety['sku'] = $gb['sku'];
				if (is_numeric($gb['weight'])) $onxshop_product_variety['weight'] = $gb['weight'];
				else $onxshop_product_variety['weight'] = -1;
				$onxshop_product_variety['stock'] = $gb['stock'];
				$onxshop_product_variety['priority'] = 0;
				$onxshop_product_variety['description'] = '';
				$product_variety_id = $Product_variety->insert($onxshop_product_variety);
				//print_r($onxshop_product_variety);
				if (is_numeric($product_variety_id)) {
					$onxshop_price['product_variety_id'] = $product_variety_id;
					$onxshop_price['currency_code'] = $GLOBALS['onxshop_conf']['global']['default_currency'];
					$onxshop_price['value'] = $gb['price'];
					//$onxshop_price['value'] = $gb['price'] / 117.5 * 100;
					$onxshop_price['type'] = 'common';
					$onxshop_price['date'] = date('c');
					$price_id = $Price->insert($onxshop_price);
				}
		
				
			}
		
		}
		
		// COMPLETE THE TRANSACTION
		if ($process) {
			
			if ($Product->db->commit()) {
				msg('CompleteTrans');
			} else {
				msg('Some error in the import, try debug.');
			}
			
		} else {
			
			msg("Some errors in import has occurred", 'error');
			
			msg('RollbackTrans');
			//$this->tpl->parse();
			$Product->db->rollBack();
		}
	}
	
		/*NOSPAM-michael at mediaconcepts dot nl
		15-Sep-2003 03:45
		A little contribution to make it more easy to use this function when working with a database. I noticed this function doesn't add logical keys to the array, so I made a small function which creates a 2dimensional array with the corresponding keys added to the rows.
		*/
		// this function requires that the first line of your CSV file consists of the keys corresponding to the values of the other lines
		function convertCSVtoAssocMArray($file, $delimiter) {
		    $result = Array();
		    $size = filesize($file) +1;
		    $file = fopen($file, 'r');
		    $keys = fgetcsv($file, $size, $delimiter);
		    while ($row = fgetcsv($file, $size, $delimiter)) {
		        for($i = 0; $i < count($row); $i++) {
		            if(array_key_exists($i, $keys)) {
		                $row[$keys[$i]] = $row[$i];
		            }
		        }
		        $result[] = $row;
		    }
		    fclose($file);
		    return $result;
		}
}
