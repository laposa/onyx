<?php
/**
 * Copyright (c) 2005-2021 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

class Onyx_Controller_Component_Contact_Form extends Onyx_Controller {
    protected $enableReCaptcha;

    /**
     * main action
     */
    public function mainAction() {
        $this->enableReCaptcha = ONYX_RECAPTCHA_PUBLIC_KEY && ONYX_RECAPTCHA_PRIVATE_KEY;

        $formdata = isset($_POST['formdata']) ? $this->preProcessEmailForm($_POST['formdata']) : [];
        if (is_array($formdata) && isset($_POST['node_id']) && $_POST['node_id'] == $this->GET['node_id']) {
            $formdata = $this->processEmailForm($formdata);
        }

        $formdata = $this->postProcessEmailForm($formdata);
        $this->tpl->assign('FORMDATA', $formdata);

        if ($this->enableReCaptcha) {
            $this->tpl->parse("content.recaptcha_field");
        }

        return true;
    }

    /**
     * preprocess (before any data processing)
     */

    // TODO Does this have any function? (undefined array key on line 16)
    public function preProcessEmailForm($formdata) {
        return $formdata;
    }

    /**
     * postprocess (after data processing, but before parsing any variables to the template)
     */
    public function postProcessEmailForm($formdata) {
        $this->tpl->assign('MAX_FILE_SIZE', ini_get('upload_max_filesize'));
        if (ONYX_ECOMMERCE) $this->parseStoreSelect($formdata['form']['store_id'], 'content');

        // pre-populate with customer data if available
        if (!empty($_SESSION['client']['customer']['id'])) {
            if (!$formdata['first_name'] && $_SESSION['client']['customer']['first_name']) $formdata['first_name'] = $formdata['required_first_name'] = $_SESSION['client']['customer']['first_name'];
            if (!$formdata['last_name'] && $_SESSION['client']['customer']['last_name']) $formdata['last_name'] = $formdata['required_last_name'] = $_SESSION['client']['customer']['last_name'];
            if (!$formdata['name'] && ($formdata['first_name'] || $formdata['last_name'])) $formdata['name'] = $formdata['required_name'] = $formdata['first_name'] . " " . $formdata['last_name'];
            if (!$formdata['email'] && $_SESSION['client']['customer']['email']) $formdata['email'] = $formdata['required_email'] = $_SESSION['client']['customer']['email'];
            if (!$formdata['telephone'] && $_SESSION['client']['customer']['telephone']) $formdata['telephone'] = $formdata['required_telephone'] = $_SESSION['client']['customer']['telephone'];
        }

        return $formdata;
    }


    /**
     * process form send action
     */
    public function processEmailForm($formdata) {
        if (!is_array($formdata)) return false;

        require_once('models/common/common_email.php');
        $Email = new common_email();
        $content = $Email->exploreFormData($formdata);

        $node_id = (int) $this->GET['node_id'];
        $reg_key = "form_notify_" . $node_id;

        // mail to
        if ($this->GET['mail_to'] == '') {
            $mail_to = $Email->conf['mail_recipient_address'];
            $mail_toname = $Email->conf['mail_recipient_name'];
        } else {
            $mail_to = $this->GET['mail_to'];
            $mail_toname = $this->GET['mail_toname'];
        }

        // mail from
        if ($Email->conf['sender_overwrite_allowed']) {
            if ($formdata['required_email']) $mail_from = $formdata['required_email'];
            else if ($formdata['email']) $mail_from = $formdata['email'];
            else $mail_from = false;

            if ($formdata['required_name']) $mail_fromname = $formdata['required_name'];
            else if ($formdata['name']) $mail_fromname = $formdata['name'];
            else if ($formdata['first_name'] || $formdata['last_name']) $mail_fromname = "{$formdata['first_name']} {$formdata['last_name']}";
            else $mail_fromname = false;
        } else {
            $mail_from = false;
            $mail_fromname = false;
        }

        // spam protection
        if ($this->enableReCaptcha) {
            $isCaptchaValid = verifyReCaptchaToken($_POST['g-recaptcha-response']);
            $Email->setValid("captcha", $isCaptchaValid);
        }

        // send out via Common_Email
        if ($Email->sendEmail('contact_form', $content, $mail_to, $mail_toname, $mail_from, $mail_fromname)) {
            $this->container->set($reg_key, 'sent');
        } else {
            $this->container->set($reg_key, 'failed');
        }

        return $formdata;
    }

    /**
     * parse country list
     */
    public function parseCountryList($template_block = 'content.country') {
        require_once('models/international/international_country.php');
        $Country = new international_country();
        $countries = $Country->listing();

        foreach ($countries as $c) {
            if ($c['id'] == $_POST['formdata']['country_id']) $c['selected'] = "selected='selected'";
            else $c['selected'] = '';
            $this->tpl->assign('COUNTRY', $c);
            $this->tpl->parse("{$template_block}.item");
        }

        $this->tpl->parse($template_block);
    }


    /**
     * parseStoreSelect
     */
    protected function parseStoreSelect($selected_id, $template_block_path = 'content.form')
    {
        require_once('models/ecommerce/ecommerce_store.php');
        $Store = new ecommerce_store();
        $provinces = $this->getTaxonomyBranch($GLOBALS['onyx_conf']['global']['province_taxonomy_tree_id']);

        $all_stores = $Store->getFilteredStoreList(false, false, 1, false, false, false, false, true);
        $processed_store_count = 0;
        $stores_with_county = [];

        foreach ($provinces as $province) {
            $this->tpl->assign("PROVINCE_NAME", $province['label']['title']);
            $counties = $this->getTaxonomyBranch($province['id']);

            foreach ($counties as $county) {
                $county['selected'] = ($selected_id == $county['id'] ? 'selected="selected"' : '');
                $this->tpl->assign("COUNTY", $county);
                // get all stores in this count
                $store_list = $Store->getFilteredStoreList($county['id'], false, 1, false, false, false, false, true);

                foreach ($store_list as $store_item) {
                    if ($store_item['publish']) {
                        $this->tpl->assign('STORE', $store_item);
                        $this->tpl->parse("$template_block_path.store.county_dropdown.province.store");
                        $processed_store_count++;
                        $stores_with_county[$store_item['id']] = $store_item;
                    }
                }
            }

            $this->tpl->parse("$template_block_path.store.county_dropdown.province");
        }

        // check if there are stores with no county category
        if (count($all_stores) > $processed_store_count) {
            
            $this->tpl->assign("PROVINCE_NAME", 'Unknown province');
            $this->tpl->assign("COUNTY", 'Unassigned county');

            $store_without_county = [];

            foreach($all_stores as $all_stores_item) {
                if (!array_key_exists($all_stores_item['id'], $stores_with_county)) {
                    $store_without_county[] = $all_stores_item;
                }
            }

            foreach($store_without_county as $store_item) {
                $this->tpl->assign('STORE', $store_item);
                $this->tpl->parse("$template_block_path.store.county_dropdown.province.store");
            }

            $this->tpl->parse("$template_block_path.store.county_dropdown.province");
        }

        // parse block
        $this->tpl->parse("$template_block_path.store.county_dropdown");

        // show only if there is at least one store
        if ($processed_store_count > 0) $this->tpl->parse("$template_block_path.store");
    }

    /**
     * getTaxonomyBranch
     */
    public function getTaxonomyBranch($parent)
    {
        require_once('models/common/common_taxonomy.php');
        $Taxonomy = new common_taxonomy();

        return $Taxonomy->getChildren($parent);
    }
}
