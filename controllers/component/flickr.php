<?php
/** 
 * Copyright (c) 2014 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once 'lib/phpFlickr/phpFlickr.php';

class Onxshop_Controller_Component_Flickr extends Onxshop_Controller
{

	/**
	 * main action 
	 */

	public function mainAction()
	{
		return true;

	}

	public function init()
	{
		$this->flickr = new phpFlickr(ONXSHOP_FLICKR_API_KEY);
		$this->flickr->enableCache("fs", ONXSHOP_PROJECT_DIR . "var/cache");
	}

	public function getUserIdByUserName($username)
	{
		$response = $this->flickr->people_findByUsername($username);
		if ($response === false) msg($this->flickr->getErrorMsg(), 'error', 1);
		return $response['id'];
	}

	public function getPhotoSizes($photo_id)
	{
		$response = $this->flickr->photos_getSizes($photo_id);
		if ($response === false) msg($this->flickr->getErrorMsg(), 'error', 1);
		return $response['id'];
	}

	public function getGalleryList($user_id, $per_page = null, $page = null)
	{
		$response = $this->flickr->galleries_getList($user_id, $per_page, $page);
		if ($response === false) msg($this->flickr->getErrorMsg(), 'error', 1);
		return $response;
	}

	public function getPhotosetList($user_id, $page = null, $per_page = null, $primary_photo_extras = null)
	{
		$response = $this->flickr->photosets_getList($user_id, $page, $per_page, $primary_photo_extras);
		if ($response === false) msg($this->flickr->getErrorMsg(), 'error', 1);
		return $response;
	}

	public function getPhotoset($photoset_id, $extras = null, $privacy_filter = null, $per_page = null, $page = null, $media = 'photos')
	{
		$response = $this->flickr->photosets_getPhotos($photoset_id, $extras, $privacy_filter, $per_page, $page, $media);
		if ($response === false) msg($this->flickr->getErrorMsg(), 'error', 1);
		return $response;
	}

}
