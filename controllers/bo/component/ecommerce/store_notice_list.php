<?php
/**
 * Copyright (c) 2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

class Onxshop_Controller_Bo_Component_Ecommerce_Store_Notice_List extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		require_once('models/common/common_node.php');
		require_once('models/common/common_scheduler.php');
		require_once('models/ecommerce/ecommerce_store.php');
		
		$this->Node = new common_node();
		$this->Scheduler = new common_scheduler();
		$Store = new ecommerce_store();
		$this->Node->setCacheable(false);
		$this->Scheduler->setCacheable(false);
		$Store->setCacheable(false);

		$store_id =  $this->GET['store_id'];
		
		$node_detail = $Store->getStoreHomepage($store_id);

		if (!is_array($node_detail)) {
			msg("node_child: Node not found", 'error');
			return false;
		}

		$this->parseList($node_detail['id']);

		return true;
	}

	public function parseList($node_id) {

		$children = $this->Node->getChildren($node_id);
		
		if (is_array($children) && count($children) > 0) { 

			foreach ($children as $child) {

				$child['other_data'] = unserialize($child['other_data']);

				$this->handlePublishing($child);

				if ($child['other_data']['type'] != 'store_notice') continue;
				if ($child['other_data']['image']) $child['image'] = '<img src="/thumbnail/100x55/' . $child['other_data']['image'] . '?method=crop"/>';
				if ($child['publish'] == 0) $child['class'] = 'disabled';
				$this->tpl->assign("CHILD", $child);
				if ($child['publish'] == 0 && !$this->isScheduled($child['id'])) {
					$this->tpl->parse('content.children.item.approve');
				}
				$this->tpl->parse('content.children.item');
			}

			$this->tpl->parse('content.children');

		} else {

			$this->tpl->parse('content.empty');

		}

	}

	public function handlePublishing(&$notice) {

		if ($this->GET['action'] == 'approve' && $notice['id'] == $this->GET['notice_id']) {

			// scheduled publishing
			if ($date = $this->checkDate($notice['other_data']['visible_from'])) {

				$this->Scheduler->scheduleNewJob(array(
					'node_id' => $notice['id'],
					'node_type' => 'common_node',
					'controller' => 'node_publish',
					'scheduled_time' => $date,
					'parameters' => null,
				));

			} else {
				$notice['publish'] = 1;
				$this->Node->nodeUpdate($notice);
			}

			// scheduled unpublishing
			if ($date = $this->checkDate($notice['other_data']['visible_to'])) {

				$this->Scheduler->scheduleNewJob(array(
					'node_id' => $notice['id'],
					'node_type' => 'common_node',
					'controller' => 'node_unpublish',
					'scheduled_time' => $date,
					'parameters' => null,
				));

			}

		}

	}

	public function checkDate($date) {

		if (!$date) return false;

		$parts = explode("/", $date, 3);
		$unix_date = strtotime($parts[2] . "-" . $parts[1] . "-" . $parts[0]);

		if ($unix_date < time()) return false;
		return $unix_date;

	}

	public function isScheduled($node_id) {
		$list = $this->Scheduler->getScheduleForNode($node_id, 'common_node');
		foreach ($list as $item) if ($item['status'] == 0) return true;
		return false;
	}
}
