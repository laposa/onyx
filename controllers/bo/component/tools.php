<?php
/**
 * Copyright (c) 2008-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onxshop_Controller_Bo_Component_Tools extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
	
		set_time_limit(0);
		
		require_once('models/common/common_file.php');
		$File = new common_file();
		
		$tool = $this->GET['tool'];
		
		switch ($tool) {
			case 'uri':
				$_Onxshop_Request = new Onxshop_Request("uri_mapping~generate=1~");
				msg("Nice URLs has been completely generated");
			break;
			case 'flush_thumb':
				if  ($File->rm(ONXSHOP_PROJECT_DIR . "var/thumbnails/*")) msg("All image thumbnails have been deleted");
				else ("Flushing thumbnails failed");
			break;
			case 'flush_tmp':
				if ($File->rm(ONXSHOP_PROJECT_DIR . "var/tmp/*")) msg("Temp directory has been cleaned");
				else ("Flushing temp dir failed");
			break;
			case 'flush_cache':
				if ($File->rm(ONXSHOP_PROJECT_DIR . "var/cache/*")) msg("Cache has been refreshed");
				else msg("Flushing cache failed");
			break;
			
			case 'find_hard_links':
				require_once('models/common/common_node.php');
				$Node = new common_node();
				$hard_links = $Node->findHardLinks();
				
				foreach ($hard_links as $hard_link) {
					$this->tpl->assign('ITEM', $hard_link);
					$this->tpl->parse('content.hard_links.item');
				}
				$this->tpl->parse('content.hard_links');
			break;

			case 'delete_orphaned_baskets':
				require_once('models/ecommerce/ecommerce_basket.php');
				$Basket = new ecommerce_basket();

				if ($Basket->deleteOrphanedAnonymouseBaskets()) {
					msg('Deleted orphaned baskets older than two weeks');
				}

			break;
			
			case 'backup':
				$_Onxshop = new Onxshop_Request("bo/component/backup");
				$this->tpl->assign('SUB_CONTENT', $_Onxshop->getContent());
			break;

			default:
				$this->tpl->parse('content.menu');
			break;
		}

		return true;
	}
}
