<?php
/** 
 * Copyright (c) 2010-2016 Onxshop Ltd (https://onxshop.com)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */


class Onxshop_Controller_Bo_Component_Template_Edit extends Onxshop_Controller {

	/**
	 * main action
	 */
	 
	public function mainAction() {
		
		//getting detail of template
		if ($this->GET['template'] != '') {
			$template_file = $this->GET['template'];
			$dir = explode("/", $template_file);
			$filename = "templates/$template_file";
			$this->tpl->assign('TEMPLATE_FILENAME', $filename);
			
			$path = ONXSHOP_PROJECT_DIR . $filename;
			if (!file_exists($path)) $path = ONXSHOP_DIR . $filename;
			//$real_path = realpath($path);
		
			if (file_exists($path) && !is_dir($path)) {
				
				$content = file_get_contents($path);
				
				if (1== 1) {
					
					$this->tpl->assign('CONTENT', htmlspecialchars($content));
					$this->tpl->parse('content.listing.edit');
					
				} else {
					
					$this->tpl->assign('CONTENT', $this->html_highlight($content));
					$this->tpl->parse('content.listing.view');
					
				}
				
				$this->tpl->parse('content.listing');
			
			} else {
			
				$this->tpl->parse('content.hint');
			
			}
		}

		return true;
	}
	
	/**
	 * highlight html
	 */
	 
 	function html_highlight($str) {
            $str = preg_replace("/(<!--)(.+?)(-->)/", "(%comment_b%)\\2(%comment_e%)", $str);  // Replace COMMENTs

            $tag_array = preg_split("/(<.+?>)/", $str, -1, PREG_SPLIT_DELIM_CAPTURE);  // Breake by TAGs to array

            while (list($ar_counter,$ar_value) = each($tag_array)) // walk array
            {
                if ($ar_counter % 2 != 0)
                {
                        //        <TAG ATTRIBUTE="VALUE" />
                        //replace <(%tag_b%)TAG(%span_e%) (%attribute_b%)ATTRIBUTE(%span_e%)=(%velue_b%)"VALUE"(%span_e%) />
                        $re=array("/(<+)([\/]?)(\S+)(>| [^>]*>)/", "/ (\S+)( *= *)([\"']?)([^\"'>]+)([\"' \?>]?)/");
                        $replacement=array( "\\1\\2(%tag_b%)\\3(%span_e%)\\4", " (%attribute_b%)\\1(%span_e%)\\2(%value_b%)\\3\\4\\5(%span_e%)");
                        $ar_value=preg_replace($re,$replacement,$ar_value);

                        $ar_value=htmlspecialchars($ar_value);

                        // replace signs (%something%) to span HTML TAGs
                        $ar_value=str_replace("(%tag_b%)", "<span class=\"tag\">", $ar_value);
                        $ar_value=str_replace("(%value_b%)", "<span class=\"value\">", $ar_value);
                        $ar_value=str_replace("(%attribute_b%)", "<span class=\"attribute\">", $ar_value);
                        $ar_value=str_replace("(%span_e%)", "</span>", $ar_value);

                } else
                {
                    // replace signs (%something%) to span HTML TAGs
                    $ar_value=str_replace("(%comment_b%)", "<span class=\"comment\">&lt;!--", $ar_value);
                    $ar_value=str_replace("(%comment_e%)", "--&gt;</span>", $ar_value);
                }

                $res.=$ar_value;
            }
            return "<span class=\"text\">$res</span>"; // return RESULT close in tags SPAN
	}
}
