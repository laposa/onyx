<?php
/** 
 * Copyright (c) 2007-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Node_Site_Default extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		require_once('models/common/common_node.php');
		$this->Node = new common_node();
		
		/**
		 * detail of the page underneath
		 */
		 
		$node_data = $this->Node->nodeDetail($this->GET['id']);
		
		/**
		 * when display_secondary_navigation is hidden, add extra css class "noSecondaryNavigation"
		 * make the page span to the full width in the default CSS
		 */
		 
		if (!isset($node_data['display_secondary_navigation'])) $node_data['display_secondary_navigation'] = $GLOBALS['onxshop_conf']['global']['display_secondary_navigation'];
		if ($node_data['display_secondary_navigation'] == 0) $node_data['css_class'] = "{$node_data['css_class']} noSecondaryNavigation";
		
		/**
		 * get node conf
		 */
		 
		$node_conf = $this->getNodeConfiguration();
		
		/**
		 * assign to template
		 */
		 	
		$this->tpl->assign("NODE", $node_data);
		$this->tpl->assign("NODE_CONF", $node_conf);
		
		/**
		 * global navigation
		 */
		 
		$_nSite = new nSite("component/menu~id=" . $node_conf['id_map-globalmenu'] . ":level=1:open={$this->GET['id']}~");
		$this->tpl->assign('GLOBAL_NAVIGATION', $_nSite->getContent());
		
		/**
		 * main menu (primary navigation) will show all items only if secondary navigation is hidden
		 */
		 
		if ($GLOBALS['onxshop_conf']['global']['display_secondary_navigation'] == 1) {
			$_nSite = new nSite("component/menu~level=1:expand_all=0:display_teaser=0:id=" . $node_conf['id_map-mainmenu'] . ":open={$this->GET['id']}~");
		} else {
			$_nSite = new nSite("component/menu~level=3:expand_all=0:display_teaser=0:id=" . $node_conf['id_map-mainmenu'] . ":open={$this->GET['id']}~");
		}
		
		$this->tpl->assign('PRIMARY_NAVIGATION', $_nSite->getContent());
		
		/**
		 * footer navigation
		 */
		 
		$_nSite = new nSite("component/menu~id=" . $node_conf['id_map-footermenu'] . ":level=1:open={$this->GET['id']}~");
		$this->tpl->assign('FOOTER_NAVIGATION', $_nSite->getContent());
		
		/**
		 * content side
		 */
		 
		if ($GLOBALS['onxshop_conf']['global']['display_content_side'] == 1) {
			$_nSite = new nSite("node~id={$node_conf['id_map-content_side']}~");
			$this->tpl->assign('CONTENT_SIDE', $_nSite->getContent());
		}
		
		/**
		 * content foot
		 */
		 
		if ($GLOBALS['onxshop_conf']['global']['display_content_foot'] == 1) {
			$_nSite = new nSite("node~id={$node_conf['id_map-content_foot']}~");
			$this->tpl->assign('CONTENT_FOOT', $_nSite->getContent());
		}

		return true;
	}
	
	/**
	 * get configuration
	 */
	 
	public function getNodeConfiguration() {
		
		$node_conf = $this->Node->conf;
		
		$node_conf = $this->localeOverwriteConfiguration($node_conf);
		
		return $node_conf;
		
	}
	
	/**
	 * locale ovewrites for conf
	 */
	 
	public function localeOverwriteConfiguration($node_conf) {
		
		/*if (in_array(1049, $_SESSION['active_pages'])) {
			$node_conf['id_map-globalmenu']
			$node_conf['id_map-mainmenu']
			$node_conf['id_map-footermenu']
			$node_conf['id_map-content_side']
			$node_conf['id_map-content_foot']
		}*/
	
		return $node_conf;	
	}
	
}
