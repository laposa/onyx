<?php
/** 
 * Copyright (c) 2025 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once('models/common/common_file.php');
require_once("conf/pdf2web.php");
require_once('controllers/bo/component/x.php');

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

class Onyx_Controller_Bo_Component_X_Leaflet_Generator extends Onyx_Controller_Bo_Component_X
{
    protected $IMAGES_PATH = ONYX_PROJECT_DIR . 'var/files/pdf2web/';

    private $node;
    private $node_data;
    private $files;
    private $pdf_file;
    private $folder_path;
    /**
     * main action
     */

    public function mainAction() {

        // get details
        $this->node = new common_node();
        $node_id = $this->GET['node_id'] ?? $_POST['node']['id'];
        $this->node_data = $this->node->nodeDetail($node_id);
        $this->folder_path = $this->IMAGES_PATH . '/' . $this->node_data['id'];
        $this->files = $this->node->getFilesForNodeId($this->node_data['id']);
        $this->initializeNodeFiles();

        // show generate only if pdf2web is not generated yet or if there is only one file (pdf) in the node
        if (!$this->node_data['custom_fields']->pdf2Web || ($this->pdf_file && count($this->files) == 1)) {
            $this->tpl->assign('HIDDEN_REGENERATE', 'hidden');
        } else {
            $this->tpl->assign('HIDDEN_GENERATE', 'hidden');
        }

        // generate new leaflet 
        if(isset($this->GET['generate']) && $this->GET['generate'] == 'true') {
            if(!$this->pdf_file) {
                $this->tpl->parse('content.edit.no_file');
            } else {
                $manifest = $this->sendPdfForConversion(ONYX_PROJECT_DIR . $this->pdf_file['src'], $this->folder_path);
                
                if($manifest) {
                    // Add images to node
                    $this->appendImagesToNode($this->folder_path, $manifest);
                    $this->node_data['custom_fields']->pdf2Web = true;
                    $this->node->nodeUpdate([
                        'id' => $this->node_data['id'],
                        'custom_fields' => $this->node_data['custom_fields']
                    ]);
                    header('HX-Trigger: refreshLeaflet');
                } else {
                    $this->tpl->parse('content.error');
                }
            }
        }

        //re-generate images
        if(isset($this->GET['regenerate']) && $this->GET['regenerate'] == 'true') {

            if(!$this->pdf_file) {
                $this->tpl->parse('content.edit.no_file');
            } else {
                $manifest = $this->sendPdfForConversion(ONYX_PROJECT_DIR . $this->pdf_file['src'], $this->folder_path);
                
                if($manifest) {
                        
                    $image = new common_image();
                    $manifest_array = json_decode($manifest, true);
                    $current_page_count = count($this->files) - 1; // excluding pdf file
                    $new_page_count = count($manifest_array['pages']);

                    // if new manifest has more pages than existing files, append new files to the node, otherwise, unlink and delete excess images
                    if ($new_page_count < $current_page_count) {
                        $excess_files = array_slice($this->files, $new_page_count + 1);

                        foreach($excess_files as $file) {
                            $image->unlinkFile($file['id']);
                            $image->deleteFile($file['src']);
                        }
                    }

                    $this->appendImagesToNode($this->folder_path, $manifest);
                    
                    // refresh thumbnails
                    foreach ($manifest_array['pages'] as $page => $content) {
                        $file_path = 'var/files/pdf2web/' . $this->node_data['id'] . '/' . $content['filename'];
                        $image->removeThumbnailsForFile($file_path);
                    }

                    header('HX-Trigger: refreshLeaflet');

                } else {
                    $this->tpl->parse('content.error');
                }
            }
        }

        // save
        if (isset($_POST['save'])) {

            $image = new common_image();
            $manifest = json_decode($_POST['manifest'], true);

            array_shift($this->files); // remove pdf file from files array

            foreach($manifest['pages'] as $page => $content) {
                $this->files[$page]['other_data'] = serialize(['hotspots' => $content['hotspots']]);
                unset($this->files[$page]['file_path_encoded']);
                unset($this->files[$page]['info']);
                $image->updateFile($this->files[$page]);
            }

            msg('Leaflet has been updated', 'ok');
            return true;
            
        }
        
        if ($this->node_data) $this->tpl->assign('NODE', $this->node_data);

        parent::parseTemplate();

        return true;
    }

    protected function initializeNodeFiles() {
        foreach ($this->files as $file) {
            if ($file['info']['mime-type'] == 'application/pdf') {
                $this->pdf_file = $file;
                break;
            }
        }
    }


    protected function sendPdfForConversion($pdfFilePath, $outputFolder) {
        
        $client = new Client();
        
        if (!file_exists($outputFolder)) {
            mkdir($outputFolder, 0777, true);
        }

        $zipFilePath = $outputFolder . '/pdf2web.zip';

        try {
            $client->post(PDF2WEB_API_ENDPOINT . '/convert', [
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => file_get_contents($pdfFilePath),
                        'filename' => basename($pdfFilePath)
                    ]
                ],
                'headers' => [
                    'Authorization: Bearer ' . PDF2WEB_API_KEY
                ],
                'sink' => $zipFilePath
            ]);
        } catch (BadResponseException $e) {
            $this->tpl->parse('content.error');
            return false;
        }

        $zip = new \ZipArchive;
        if ($zip->open($zipFilePath) === TRUE) {
            $zip->extractTo($outputFolder);
            $zip->close();
            unlink($zipFilePath);

            return file_get_contents($outputFolder . '/manifest.json');
        } else {
            throw new \Exception('Failed to unzip the file.');
        }
    }

    public function appendImagesToNode($folderPath, $manifest) {
        $image = new common_image();
        $node_id = $this->node_data['id'];
        $this->files = $this->node->getFilesForNodeId($node_id); // refresh files after potential deletion of excess files
        $file_list = $image->getFlatArrayFromFs($folderPath, 'f');

        //need to use manifest in order to insert files in correct order
        $manifest_array = json_decode($manifest, true);

        foreach ($manifest_array['pages'] as $key => $page) {

            // check on already existing files and potentially overwrite source
            if(count($this->files) > 2 && isset($this->files[$key + 1])) { 
                if($this->files[$key + 1]['src'] != 'var/files/pdf2web/' . $node_id . '/' . $page['filename']) {
                    $file_data = [];
                    $file_data['id'] = $this->files[$key + 1]['id'];
                    $file_data['src'] = 'var/files/pdf2web/' . $node_id . '/' . $page['filename'];
                    $file_data['node_id'] = $node_id;
                    $file_data['title'] = 'Page ' . ($key + 1);
                    $file_data['role'] = 'main';

                    $image->updateFile($file_data);
                    continue;
                } else {
                    continue;
                }
            }

            $file_index = array_search($page['filename'], array_column($file_list, 'name'));
            
            $file_data = [];
            $file_data['src'] = 'var/files/pdf2web/' . $node_id . '/' . $file_list[$file_index]['name'];
            $file_data['node_id'] = $node_id;
            $file_data['title'] = 'Page ' . ($key + 1);
            $file_data['role'] = 'main';

            $image->insertFile($file_data);
        }
    }
}
