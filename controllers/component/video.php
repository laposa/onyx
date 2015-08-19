<?php
/** 
 * Copyright (c) 2015 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */

class Onxshop_Controller_Component_Video extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {

		$vimeo_video_id = false;
		$youtube_video_id = false;
		
		if (is_numeric($this->GET['autoplay']) && $this->GET['autoplay'] == 1) $autoplay = 1;
		else $autoplay = 0;

		// detect vimeo
		preg_match("/https?:\/\/vimeo.com\/(\d+)/", $this->GET['video_url'], $matches);
		if (isset($matches[1]) && is_numeric($matches[1])) $vimeo_video_id = $matches[1];

		// detect youtube
		preg_match("/https?:\/\/youtu.be\/([0-9a-zA-Z-_]+)/", $this->GET['video_url'], $matches);
		if (isset($matches[1]) && !empty($matches[1])) $youtube_video_id = $matches[1];
		preg_match("/https?:\/\/www.youtube.com\/watch\?v=([0-9a-zA-Z-_]+)/", $this->GET['video_url'], $matches);
		if (isset($matches[1]) && !empty($matches[1])) $youtube_video_id = $matches[1];

		if ($vimeo_video_id) {

			$_Onxshop_Request = new Onxshop_Request("component/video_vimeo~video_id={$vimeo_video_id}:autoplay=$autoplay~");
			$this->tpl->assign('CONTENT', $_Onxshop_Request->getContent());

		} else if ($youtube_video_id) {

			$_Onxshop_Request = new Onxshop_Request("component/video_youtube~video_id={$youtube_video_id}:autoplay=$autoplay~");
			$this->tpl->assign('CONTENT', $_Onxshop_Request->getContent());

		}

		return true;
		
	}
	
}
