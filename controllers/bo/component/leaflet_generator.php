<?php
/** 
 * Copyright (c) 2024 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 */

require_once("conf/pdf2web.php");
require_once('controllers/bo/component.php');

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

class Onyx_Controller_Bo_Component_Leaflet_Generator extends Onyx_Controller_Bo_Component
{
    protected $IMAGES_PATH = ONYX_PROJECT_DIR . 'var/files/pdf2web/';

    private $File;
    /**
     * main action
     */

    public function mainAction() {

        parent::assignNodeData();

        if(isset($this->GET['generate']) && $this->GET['generate'] == 'true') {
            // TODO: returns only PDF?
            $files = $this->Node->getFilesForNodeId($this->GET['node_id']);
            
            $pdfFile = null;
            foreach ($files as $file) {
                if ($file['info']['mime-type'] == 'application/pdf') {
                    $pdfFile = $file;
                    break;
                }
            }

            if(!$pdfFile) {
                $this->tpl->parse('content.edit.no_file');
            } else {
                $folderPath = $this->IMAGES_PATH . '/' . $this->node_data['id'];

                bar_dump($files);
        
                // remove old files & unlink
                $this->removeFolder($folderPath);
                $image = new common_image();
                foreach($files as $index => $file) {
                    if ($index == 0) continue;
                    $image->unlinkFile($file['id']);
                    $image->delete($file['id']);
                }
                
                $manifest = $this->sendPdfForConversion(ONYX_PROJECT_DIR . $pdfFile['src'], $folderPath);
                
                if($manifest) {
                    // Add images to node
                    $this->appendImagesToNode($folderPath, $manifest);
                    $this->node_data['custom_fields']->pdf2Web = $manifest;
                } else {
                    $this->tpl->parse('content.error');
                }
            }
        }

        parent::parseTemplate();

        return true;
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

    protected function removeFolder($dir) {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            bar_dump($item);
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->removeFolder($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($dir);
    }

    public function appendImagesToNode($folderPath, $manifest) {
        
        require_once('models/common/common_file.php');

        $Image = new common_image();
        $node_id = $this->node_data['id'];

        $file_list = $Image->getFlatArrayFromFs($folderPath, 'f');

        //need to use manifest in order to insert files in correct order
        $manifest_array = json_decode($manifest, true);

        foreach ($manifest_array['pages'] as $key => $page) {
            $file_index = array_search($page['filename'], array_column($file_list, 'name'));

            $file_data = [];
            $file_data['src'] = 'var/files/pdf2web/' . $node_id . '/' . $file_list[$file_index]['name'];
            $file_data['node_id'] = $node_id;
            $file_data['title'] = 'Page ' . ($key + 1);
            $file_data['role'] = 'main';

            $Image->insertFile($file_data);
        }
    }
}
