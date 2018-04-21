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
        
        if (!empty($this->GET['template']) && $this->isSafe($this->GET['template'])) {

            $path = realpath(ONXSHOP_PROJECT_DIR . "templates/" . $this->GET['template']);
        
            if (file_exists($path) && !is_dir($path)) {
                
                $content = file_get_contents($path);
                $this->tpl->assign('CONTENT', htmlspecialchars($content));
                
                if (ONXSHOP_ALLOW_TEMPLATE_EDITING && is_writable($path) && $this->hasPermission()) {

                    $this->saveContent($path);
                    $this->tpl->parse('content.listing.edit');

                } else if (isset($_POST['content'])) {

                    echo "Unauthorized access!";
                    exit();

                }

                $this->tpl->parse('content.listing');
            
            } else {
            
                $this->tpl->parse('content.hint');
            
            }

        }

        return true;
    }

    /**
     * Save content
     */
    function saveContent($path)
    {
        if (isset($_POST['content'])) {
            file_put_contents($path, $_POST['content']);
            echo "The template has been successfully saved.";
            exit();
        }
    }

    /*
     * Checked whether the given path is safe
     */
    function isSafe($filename)
    {
        return (
            strpos($filename, '../') === false &&
            strpos($filename, '/..') === false && 
            basename($filename) != '.htaccess'
        );
    }

    /**
     * Todo
     */
    function hasPermission()
    {
        return true;
    }
}
