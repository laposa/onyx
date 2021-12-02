<?php
/**
 *
 * Copyright (c) 2020-2021 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 * 
- Get list of URLs
- Iterate through the list using Node ID
- Run OnyxRequest to render HTML for Node ID
- Create folder for the URL
- Save rendered HTML index.html file
 */

require_once('models/common/common_node.php');
require_once('models/common/common_uri_mapping.php');

class Onyx_Controller_Bo_Pages_Build extends Onyx_Controller
{
    protected $builds = null;
    protected $buildsStorage = ONYX_PROJECT_DIR . '/public_html/static';

    /**
     * main action
     */
    public function mainAction()
    {

        $action = $_POST['action'] ?? '';
        if ($action == 'new-build') {
            $this->createNewBuild();
        }

        if ($action == 'publish-build') {
            $this->publishVersion($_POST['version']);
        }

        $builds = $this->getBuilds(true);

        // get list from filesystem
        foreach ($builds as $item) {
            $this->tpl->assign('ITEM', $item);
            if ($item->status != 'published') {
                $this->tpl->parse('content.item.publish');
            }

            $this->tpl->parse('content.item');
        }

        return true;
    }

    public function getBuilds($refresh = false)
    {
        if (!$refresh && $this->builds !== null) {
            return $this->builds;
        }

        $builds = [];

        foreach (glob($this->buildsStorage . '/*/metadata.json') as $file) {
            $metadata = json_decode(file_get_contents($file));
            if (isset($builds[$metadata->versionName])) {
                continue;
            }

            $builds[$metadata->versionName] = $metadata;
            $pathParts = explode('/', $file);
            if ($pathParts[count($pathParts) - 2] == 'published') {
                $builds[$metadata->versionName]->status = 'published';
            } else {
                $builds[$metadata->versionName]->status = '-';
            }
        }

        usort($builds, function ($a, $b) {
            return $a->version < $b->version;
        });
        $this->builds = $builds;
        return $builds;
    }

    protected function createNewBuild()
    {
        $builds = $this->getBuilds();
        $nextBuildVersion = count($builds) == 0 ? 1 : ((int)$builds[0]->version + 1);

        mkdir("{$this->buildsStorage}/v{$nextBuildVersion}");

        $all_urls = $this->getUrlList();

        set_time_limit(0);
        foreach ($all_urls as $url) {
            if ($url['type'] == 'generic') {
                $content = $this->getContentForNodeIdCurl($url['node_id']);
                $folder = "{$this->buildsStorage}/v{$nextBuildVersion}{$url['public_uri']}";
                mkdir($folder);
                file_put_contents($folder . "/index.html", $content);
            }
        }

        $this->createMetadata($nextBuildVersion);
    }

    protected function getContentForNodeId($node_id)
    {
        //echo file_get_contents("https://supervalu.dev.musgrave.io/page/$node_id");exit;
        $content = new Onyx_Request("node~id=$node_id~");
        return $content;
    }

    protected function getContentForNodeIdCurl($node_id) 
    {
        // create curl resource 
        $ch = curl_init(); 

        // set url 
        curl_setopt($ch, CURLOPT_URL, "https://supervalu.dev.musgrave.io/page/$node_id"); 

        //return the transfer as a string 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

        // $output contains the output string 
        $content = curl_exec($ch); 

        // close curl resource to free up system resources 
        curl_close($ch);

        return $content;
    }

    protected function getContentForNodeIdRouter($node_id) 
    {
        $request = "node/site/default~id=$node_id~.node~id=$node_id~";
        $router = new Onyx_Router();
        $Onyx = $router->processAction($request);
        //$this->headers = $this->getPublicHeaders();

        $content = $Onyx->finalOutput();

        return $content;
    }

    protected function getUrlList()
    {

        $UriMapping = new common_uri_mapping();
        $all_urls = $UriMapping->getList();
        
        return $all_urls;

    }

    protected function createMetadata($buildVersion)
    {
        $data = [
            'author'      => $_SESSION['authentication']['user_details']['first_name'] . " " . $_SESSION['authentication']['user_details']['last_name'],
            'created'     => date('c'),
            'versionName' => "v{$buildVersion}",
            'version'     => $buildVersion,
        ];
        file_put_contents("{$this->buildsStorage}/v{$buildVersion}/metadata.json", json_encode($data));
    }


    protected function publishVersion($version)
    {
        foreach ($this->branches as $branch) {
            copy("{$this->buildsStorage}/{$version}/{$branch}.json", "{$this->buildsStorage}/published/{$branch}.json");
        }

        copy("{$this->buildsStorage}/{$version}/metadata.json", "{$this->buildsStorage}/published/metadata.json");
    }

    
}
