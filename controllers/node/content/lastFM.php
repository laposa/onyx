<?php
/** 
 * Copyright (c) 2006-2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/node/content/default.php');

class Onxshop_Controller_Node_Content_LastFM extends Onxshop_Controller_Node_Content_Default {

	/**
	 * main action
	 */
	 
	public function mainAction() {

		return false; //disable

		//TODO: implement Zend_Service_Audioscrobbler
		
		require_once('models/common/common_node.php');
		
		$Node = new common_node();
		
		$node_data = $Node->nodeDetail($this->GET['id']);
		$username = trim($node_data['content']);
		
		//Include and initialize the class on your page:
		//REMOVED, USE Zend_Service_Audioscrobbler require_once('lib/scrobbler.class.php');
		$scrobbler = new Scrobbler();
		
		//Configure the class
		// Change path to cache dir
		$scrobbler->setOption('CACHE_DIR', ONXSHOP_PROJECT_DIR . 'var/tmp/'); 
		// Relative path would of cause be okay, too
		
		
		if (isset($_POST['scrobbler'])) {
			$_SESSION['scrobbler'] = $_POST['scrobbler'];
		} else if (!isset($_SESSION['scrobbler'])) {
			$_SESSION['scrobbler'] = 'TOP_ARTISTS';
		}
		
		
		// load
		/*
		    * SCROBBLER_XML_RECENT_WEEKLY_ARTIST_CHART
		    * SCROBBLER_XML_RECENT_WEEKLY_TRACK_CHART
		    * SCROBBLER_XML_TOP_ARTISTS
		    * SCROBBLER_XML_TOP_TRACKS
		    * SCROBBLER_XML_TOP_ALBUMS
		    
		define('SCROBBLER_XML_RECENT_TRACKS', 0x1);
		
		// XML: Recent Weekly Charts
		
		define('SCROBBLER_XML_RECENT_WEEKLY_ARTIST_CHART', 0x2);
		//define('SCROBBLER_XML_RECENT_WEEKLY_ALBUM_CHART', 0x4);   // Not supported (feed doesn't work very well)
		define('SCROBBLER_XML_RECENT_WEEKLY_TRACK_CHART', 0x8);
		
		// XML: Overall Charts
		
		define('SCROBBLER_XML_TOP_ARTISTS', 0x10);
		define('SCROBBLER_XML_TOP_ALBUMS', 0x20);
		define('SCROBBLER_XML_TOP_TRACKS', 0x40);
		*/
		
		switch($_SESSION['scrobbler']) {
			case 'TOP_ARTISTS':
			$type = 0x10;
			break;
			case 'TOP_ALBUMS':
			$type = 0x20;
			break;
			case 'TOP_TRACKS':
			$type = 0x40;
			break;
			case 'RECENT_TRACKS':
			$type = 0x1;
			break;
			case 'RECENT_WEEKLY_ARTIST_CHART':
			$type = 0x2;
			break;
			case 'RECENT_WEEKLY_TRACK_CHART':
			$type = 0x8;
			break;
			default:
			$type = 0x10;
			break;
		}
		
		$sb['selected'][$_SESSION['scrobbler']] = "selected='selected'";
		
		$data = $scrobbler->getDataFormatted($username, $type);
		if ($data) {
			$sb['content'] = $data;
		} else {
			msg ("Username '$username' not found...", 'error');
		}
		
		$this->tpl->assign("SCROBBLER", $sb);
		
		$this->tpl->assign('NODE', $node_data);
		
		if ($node_data['display_title'])  $this->tpl->parse('content.title');

		return true;
	}
}
