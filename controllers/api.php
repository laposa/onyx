<?php
/** 
 * Copyright (c) 2012-2015 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 *  Generic API key: abcdefgh
 *  API key check isn't implemented yet, it works only as a referrer to identify who is using the API
 *
 *  Example call for JSON Format:
 *  /api/v1.0/resources?format=json&api_key=abcdefgh
 *  
 *  Example call for XML Format:
 *  /api/v1.0/resources?format=xml&api_key=abcdefgh
 * 
 */

class Onxshop_Controller_Api extends Onxshop_Controller {

    static $thumbnail_size = 200;

    /**
     * main action
     */
     
    public function mainAction() {

        $error = $this->getAndValidateInput();

        if ($error) $data = $error;
        else $data = $this->getData();
        
        $format = $this->GET['format'];
        
        $content = $this->formatOutput($data, $format);
        
        $this->tpl->assign('CONTENT', $content);
        
        return true;
        
    }

    /**
     * get data
     */
    
    public function getData() {
        
        //TODO implement in your own controller
        return array();
                
    }
    
    /**
     * format output
     */
     
    public function formatOutput($data, $format = 'json') {
        
        switch ($format) {
        
            case 'xml':
                $content_formated = $this->formatXML($data);
            break;
            case 'rss':
                $content_formated = $this->formatRSS($data);
            break;
            case 'csv':
                $content_formated = $this->formatCSV($data);
            break;
            case 'json':
            default:
                $content_formated = $this->formatJSON($data);
            break;
        
        }
        
        // clean invalid characters
        $content_formated = utf8_for_xml($content_formated);
        
        return $content_formated;
    
    }
    
    /**
     * format output as JSON
     */
     
    public function formatJSON($data) {
        
        header('Content-Type: application/json; charset=UTF-8');
        
        $formated_data = json_encode($data);
        
        return $formated_data;
        
    }
    
    /**
     * format output as XML
     */
     
    public function formatXML($data) {
    
        header('Content-Type: text/xml; charset=UTF-8');
        
        $formated_data = $this->generate_valid_xml_from_array($data);
        
        return $formated_data;
        
    }
    
    /**
     * format output as RSS
     * This may be done in each method controller because the data are different
     * for each method and RSS output must be the same.
     */
     
    public function formatRSS($data) {
    
        header('Content-Type: application/rss+xml; charset=UTF-8');
        
        $formated_data = '';
        
        return $formated_data;
        
    }
    
    /**
     * format output as CSV
     *
     */
     
    public function formatCSV($data) {

        $filename = "data";
        
        header('Content-type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="'.$filename.'-'.date('Y\-m\-d\_Hi').'.csv"');
        header("Cache-Control: cache, must-revalidate");
        header("Pragma: public");
        
        $formated_data = "\"id\",\"title\",\"description\",\"url\"\n";
        
        foreach ($data as $item) {
            $item['title'] = htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8');
            $item['description'] = htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8');
            $item['url'] = htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8');
            $formated_data .= "\"{$item['id']}\",\"{$item['title']}\",\"{$item['description']}\",\"{$item['url']}\"\n";
            
        }
        
        return $formated_data;
        
    }
    
    
    /**
     * create XML from array
     */
     
    private function generate_xml_from_array($array, $node_name) {
        
        $xml = '';
    
        if (is_array($array) || is_object($array)) {
        
            foreach ($array as $key=>$value) {
            
                if (is_numeric($key)) {
                    $key = $node_name;
                }
    
                $xml .= '<' . $key . '>' . "\n" . $this->generate_xml_from_array($value, $node_name) . '</' . $key . '>' . "\n";
            }
        
        } else {
        
            $xml = htmlspecialchars($array, ENT_QUOTES, 'UTF-8') . "\n";
        
        }
    
        return $xml;
    }

    /**
     * create XML from array
     */
     
    private function generate_valid_xml_from_array($array, $node_block='items', $node_name='item') {
        
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";
    
        $xml .= '<' . $node_block . '>' . "\n";
        $xml .= $this->generate_xml_from_array($array, $node_name);
        $xml .= '</' . $node_block . '>' . "\n";
    
        return $xml;
    }

    /**
     * Get and validate input parameters
     *
     * return array on error
     *        false on success
     */

    protected function getAndValidateInput()
    {
        $error = array('status' => 400);

        // process thunbnail_size parameter

        if (is_numeric($this->GET['thumbnail_size'])) {
        
            if ($this->GET['thumbnail_size'] % 5 != 0) {
                $error['message'] = "thumbnail_size has to be a multiple of 5";
                return $error;
            }

            if ($this->GET['thumbnail_size'] > 1000) {
                $error['message'] = "thumbnail_size is too big";
                return $error;
            }

            if ($this->GET['thumbnail_size'] < 1) {
                $error['message'] = "thumbnail_size is too small";
                return $error;
            }

            self::$thumbnail_size = $this->GET['thumbnail_size'];

        }

        return false;
    }   

}
