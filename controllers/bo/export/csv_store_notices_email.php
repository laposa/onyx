<?php
/** 
 * Copyright (c) 2014 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 * 
 */
 
require_once('controllers/bo/export/csv_store_notices.php');
require_once('models/common/common_email.php');

class Onyx_Controller_Bo_Export_CSV_Store_Notices_Email extends Onyx_Controller_Bo_Export_CSV_Store_Notices {

    /**
     * main action
     */
     
    public function mainAction() {
        
        set_time_limit(0);

        $Store = new ecommerce_store();
        
        /**
         * Get the list
         */

        $date_from = date("Y-m-d", strtotime("first day of last month"));
        $date_to = date("Y-m-d", strtotime("first day of this month"));

        if (isset($this->GET['date_from'])) $date_from = $this->GET['date_from'];
        if (isset($this->GET['date_to'])) $date_to = $this->GET['date_to'];

        $records = $Store->getDataForNoticesReport($date_from, $date_to);
        $this->commonCSVAction($records, 'store_notices-' . $date_to);

        return true;
    }

    /**
     * commonCSVAction
     */
     
    public function commonCSVAction($records, $filename = 'export') {
        
        if (is_array($records)) {
        
            // parse records to CSV format
            $this->parseCSVTemplate($records);

            // set the result to an email address
            $email = 'support@onxshop.com';
            if (isset($this->GET['email'])) $email = $this->GET['email'];

            $count = count($records);

            if ($this->sendCSVEmail($email, $filename)) echo "Email has been sent ($count notices)";
            else echo "An error occured during the email dispatch.";
            
        } else {
            
            echo "no records"; exit;
        
        }

        exit();
        
    }

    /**
     * sendCSVEmail
     */
     
    public function sendCSVEmail($email, $filename = 'unknown') {

        // get content
        $this->tpl->parse('content');
        $text = $this->tpl->text('content');

        $export_file = ONYX_PROJECT_DIR . "var/tmp/$filename.csv";
        file_put_contents($export_file, $text);

        // send email
        $GLOBALS['common_email'] = array(
            'date_from' => date("d/m/Y", strtotime("first day of last month")),
            'date_to' => date("d/m/Y", strtotime("first day of this month"))
        );

        $GLOBALS['onyx_atachments'] = array($export_file);

        $EmailForm = new common_email();

        $template = 'notices_report';
        $content = 'NA';
        $email_recipient = $email;
        $name_recipient = false;
        $email_from = false;
        $name_from = false;

        $email_sent_status = $EmailForm->sendEmail($template, $content, $email_recipient, $name_recipient, $email_from, $name_from);
        
        unset($GLOBALS['common_email']);
        unset($GLOBALS['onyx_atachments']);
        
        return $email_sent_status;

    }

}
