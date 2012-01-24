<?php
/** 
 * Copyright (c) 2011 Laposa Ltd (http://laposa.co.uk)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('controllers/component/comment.php');

class Onxshop_Controller_Component_Comment_Approve extends Onxshop_Controller_Component_Comment {

	/**
	 * custom comment action
	 */
	 
	public function customCommentAction($data, $options) {
	
		if ($this->checkEditPermission($data)) {
		
			if (is_numeric($this->GET['comment_id'])) $comment_id = $this->GET['comment_id'];
			else return false;
			
			if (is_numeric($this->GET['publish'])) $publish = $this->GET['publish'];
			else return false;
			
			$comment_data = $this->Comment->getDetail($comment_id);
			$comment_data['publish'] = $publish;
			
			if ($this->Comment->updateComment($comment_data)) {
			
				if ($publish == 1) msg("Comment ID $comment_id approved by client ID {$_SESSION['client']['customer']['id']}");
				else if ($publish == -1) msg("Comment ID $comment_id rejected by client ID {$_SESSION['client']['customer']['id']}");
				onxshopGoTo($_SESSION['referer'], 2);
			}
			
		}
		
		return true;
	}
	
}