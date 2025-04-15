<?php

require_once("conf/pdf2web.php");
require_once('controllers/bo/node/page/default.php');
use GuzzleHttp\Client;

class Onyx_Controller_Bo_Node_Page_Pdf_Brochure extends Onyx_Controller_Bo_Node_Page_Default
{

  protected $IMAGES_PATH = ONYX_PROJECT_DIR . 'var/files/pdf2web/';

  /**
   * main action
   */

  public function mainAction()
  {
    
    parent::mainAction();
    
    if (is_array($_POST) && array_key_exists('manifest', $_POST)) {
      // save manifest submitted from fe_edit
      $this->manifestUpdate($_POST['manifest']);
    } else {
      // convert uploaded pdf to images
      $this->preparePdf();
      $this->detail();
      $this->assign();
    }
    
    return true;
  }

  protected function preparePdf()
  {
    $files = $this->Node->getFilesForNodeId($this->GET['id']);

    $pdfFile = null;
    foreach ($files as $file) {
      if ($file['info']['mime-type'] == 'application/pdf') {
        $pdfFile = $file;
        break;
      }
    }

    if (!$pdfFile) {
      return false;
    }

    // check if file was already converted
    $pdf2Web = isset($this->node_data['custom_fields']->pdf2Web) ? json_decode($this->node_data['custom_fields']->pdf2Web) : null;
    if ($pdf2Web && $pdf2Web->source == $pdfFile['info']['filename']) {
      return false;
    }

    $folderPath = $this->IMAGES_PATH . '/' . $this->node_data['id'];

    // remove old files
    if ($pdf2Web) {
      $this->removeFolder($folderPath);
    }

    $manifest = $this->sendPdfForConversion(ONYX_PROJECT_DIR . $pdfFile['src'],  $folderPath);
    if (!$manifest) {
      return false;
    }

    // save manifest
    $this->manifestUpdate($manifest);
  }

  protected function sendPdfForConversion($pdfFilePath, $outputFolder)
  {
    $client = new Client();
    if (!file_exists($outputFolder)) {
      mkdir($outputFolder, 0777, true);
    }

    $zipFilePath = $outputFolder . '/pdf2web.zip';
    
    $client->post(PDF2WEB_API_ENDPOINT . '/convert', [
      'multipart' => [
        [
          'name'     => 'file',
          'contents' => file_get_contents($pdfFilePath),
          'filename' => basename($pdfFilePath)
        ]
      ],
      'headers' => [
        'Authorization: Bearer ' . PDF2WEB_API_KEY
      ],
      'sink' => $zipFilePath
    ]);


    $zip = new \ZipArchive;
    if ($zip->open($zipFilePath) === TRUE) {
      $zip->extractTo($outputFolder);
      $zip->close();
      unlink($zipFilePath);

      return file_get_contents($outputFolder . '/manifest.json');
    } else {
      throw new \Exception('Failed to unzip the file.');
    }

    return false;
  }

  protected function removeFolder($dir)
  {
    if (!file_exists($dir)) {
      return true;
    }

    if (!is_dir($dir)) {
      return unlink($dir);
    }

    foreach (scandir($dir) as $item) {
      if ($item == '.' || $item == '..') {
        continue;
      }

      if (!$this->removeFolder($dir . DIRECTORY_SEPARATOR . $item)) {
        return false;
      }
    }

    return rmdir($dir);
  }

  public function manifestUpdate($manifest) {
    $nodeData = $this->Node->detail($this->node_data['id']);
    $nodeData['custom_fields'] = (object) json_decode($nodeData['custom_fields']);
    $nodeData['custom_fields']->pdf2Web = $manifest;
    return $this->Node->nodeUpdate($nodeData);
  }
}
