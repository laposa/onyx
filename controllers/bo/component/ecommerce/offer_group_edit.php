<?php
/**
 * Special Offer Group edit form
 *
 * Copyright (c) 2013-2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

require_once('models/ecommerce/ecommerce_offer_group.php');

class Onxshop_Controller_Bo_Component_Ecommerce_Offer_Group_Edit extends Onxshop_Controller
{
	
	/**
	 * main action
	 */
	 
	public function mainAction()
	{
		$this->Offer_Group = new ecommerce_offer_group();

		$offer_group_id = (int) $this->GET['offer_group_id'];

		if ($_POST['save']) {

			$_POST['offer_group']['schedule_start'] = $this->dateAndTimeToFull($_POST['offer_group']['start_date'], $_POST['offer_group']['start_time']);
			$_POST['offer_group']['schedule_end'] = $this->dateAndTimeToFull($_POST['offer_group']['end_date'], $_POST['offer_group']['end_time']);

			$offer_group_id = $this->processForm($_POST['offer_group'], $offer_group_id);
			onxshopGoTo("/backoffice/products");

		} else {

			$_POST['offer_group']['schedule_start'] = date("Y-m-d", time() + 7 * 24 * 3600);
			$_POST['offer_group']['schedule_end'] = date("Y-m-d", time() + 14 * 24 * 3600);
			$_POST['offer_group']['publish'] = 1;
		}

		if ($offer_group_id > 0) $offer_group = $this->Offer_Group->detail($offer_group_id);
		else $offer_group = $_POST['offer_group'];

		if ($offer_group['publish']) $offer_group['publish'] = 'checked="checked"'; else $offer_group['publish'] = '';
		$this->tpl->assign("OFFER_GROUP", $offer_group);

		return true;
	}



	protected function dateAndTimeToFull($date, $time)
	{
		$date = implode("-", array_reverse(explode("/", $date)));
		$time = strtotime($date . " " . $time);
		return date("Y-m-d H:i:s", $time);
	}

	protected function processForm($offer_group, $offer_group_id)
	{
		// save offer_group data
		$data = array(
			'title' => $offer_group['title'],
			'description' => $offer_group['description'],
			'schedule_start' => $offer_group['schedule_start'],
			'schedule_end' => $offer_group['schedule_end'],
			'publish' => (int) $offer_group['publish']
		);

		if ($offer_group_id > 0) $data['id'] = $offer_group_id;

		return $this->Offer_Group->save($data);

	}

}
